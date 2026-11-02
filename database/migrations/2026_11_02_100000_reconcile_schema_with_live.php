<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Brings every database (fresh install or the live DB) to the same schema.
 *
 * The live database was patched by hand while the create_* migrations were
 * edited afterwards, so the two drifted apart. Every step here is guarded,
 * so it is safe to run on either one.
 */
return new class extends Migration
{
    /** Lookup tables that must have a unique uuid column. */
    private array $uuidTables = [
        'contact_types', 'divisions', 'division_types', 'marital_statuses',
        'states', 'staff_categories', 'geo_pol_zones', 'lgas',
        'salary_structures', 'salary_levels', 'salary_steps',
        'senatorial_districts', 'departments',
    ];

    public function up(): void
    {
        // 1. uuid columns: add where missing, backfill blanks, enforce uniqueness.
        foreach ($this->uuidTables as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            if (!Schema::hasColumn($table, 'uuid')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->uuid('uuid')->nullable()->after('id');
                });
            }

            DB::table($table)
                ->whereNull('uuid')->orWhere('uuid', '')
                ->orderBy('id')
                ->pluck('id')
                ->each(fn ($id) => DB::table($table)->where('id', $id)->update(['uuid' => (string) Str::uuid()]));

            if ($this->isMysql()) {
                DB::statement("ALTER TABLE `{$table}` MODIFY `uuid` CHAR(36) NOT NULL");
            }

            if (!$this->hasIndex($table, "{$table}_uuid_unique") && !$this->hasUniqueOn($table, 'uuid')) {
                Schema::table($table, function (Blueprint $t) use ($table) {
                    $t->unique('uuid', "{$table}_uuid_unique");
                });
            }
        }

        // 2. profiles: columns used by the code but missing on one side.
        if (Schema::hasTable('profiles')) {
            Schema::table('profiles', function (Blueprint $t) {
                if (!Schema::hasColumn('profiles', 'home_address')) {
                    $t->string('home_address')->nullable();
                }
                if (!Schema::hasColumn('profiles', 'bio')) {
                    $t->text('bio')->nullable();
                }
            });
            $this->makeNullable('profiles', ['designation' => 'VARCHAR(255)', 'phone' => 'VARCHAR(255)', 'avatar' => 'VARCHAR(255)']);
        }

        // 3. promotions.status (present on live only).
        if (Schema::hasTable('promotions') && !Schema::hasColumn('promotions', 'status')) {
            Schema::table('promotions', function (Blueprint $t) {
                $t->string('status')->nullable()->after('remark');
            });
        }

        // 4. staff_updates name columns (present in code only).
        if (Schema::hasTable('staff_updates')) {
            Schema::table('staff_updates', function (Blueprint $t) {
                foreach (['surname', 'firstname', 'othernames'] as $col) {
                    if (!Schema::hasColumn('staff_updates', $col)) {
                        $t->string($col)->nullable();
                    }
                }
            });
        }

        // 5. salary_levels.level was unique across ALL structures, so
        //    CONUASS 07 could not exist next to CONTISS 07. Make it unique
        //    per structure instead.
        if (Schema::hasTable('salary_levels')) {
            if ($this->hasIndex('salary_levels', 'salary_levels_level_unique')) {
                Schema::table('salary_levels', fn (Blueprint $t) => $t->dropUnique('salary_levels_level_unique'));
            }
            if (!$this->hasIndex('salary_levels', 'salary_levels_structure_level_unique')) {
                Schema::table('salary_levels', fn (Blueprint $t) => $t->unique(['salary_structure_id', 'level'], 'salary_levels_structure_level_unique'));
            }
        }
        if (Schema::hasTable('salary_steps') && $this->hasIndex('salary_steps', 'salary_steps_step_unique')) {
            Schema::table('salary_steps', fn (Blueprint $t) => $t->dropUnique('salary_steps_step_unique'));
        }
        if (Schema::hasTable('salary_steps') && !$this->hasIndex('salary_steps', 'salary_steps_level_step_unique')) {
            Schema::table('salary_steps', fn (Blueprint $t) => $t->unique(['salary_level_id', 'step'], 'salary_steps_level_step_unique'));
        }

        // 6. LGA names repeat across states (e.g. Nasarawa, Ifelodun, Surulere),
        //    so uniqueness must be per state, not global.
        if (Schema::hasTable('lgas')) {
            if ($this->hasIndex('lgas', 'lgas_lga_name_unique')) {
                Schema::table('lgas', fn (Blueprint $t) => $t->dropUnique('lgas_lga_name_unique'));
            }
            if (!$this->hasIndex('lgas', 'lgas_state_lga_unique')) {
                Schema::table('lgas', fn (Blueprint $t) => $t->unique(['state_id', 'lga_name'], 'lgas_state_lga_unique'));
            }
        }

        // 7. Nullability that the live data depends on.
        $this->makeNullable('staff_categories', ['parent_staff_category' => 'BIGINT UNSIGNED']);
        $this->makeNullable('employments', ['employment_type_id' => 'BIGINT UNSIGNED']);
    }

    public function down(): void
    {
        // Reconciliation is intentionally not reversible.
    }

    private function isMysql(): bool
    {
        return in_array(DB::getDriverName(), ['mysql', 'mariadb'], true);
    }

    private function makeNullable(string $table, array $columns): void
    {
        if (!$this->isMysql() || !Schema::hasTable($table)) {
            return;
        }
        foreach ($columns as $column => $type) {
            if (Schema::hasColumn($table, $column)) {
                DB::statement("ALTER TABLE `{$table}` MODIFY `{$column}` {$type} NULL");
            }
        }
    }

    private function hasIndex(string $table, string $index): bool
    {
        if (!$this->isMysql()) {
            return collect(DB::select("PRAGMA index_list('{$table}')"))->contains('name', $index);
        }
        return !empty(DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$index]));
    }

    private function hasUniqueOn(string $table, string $column): bool
    {
        if (!$this->isMysql()) {
            return false;
        }
        return !empty(DB::select("SHOW INDEX FROM `{$table}` WHERE Column_name = ? AND Non_unique = 0", [$column]));
    }
};
