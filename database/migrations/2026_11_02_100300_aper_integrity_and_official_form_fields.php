<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * APER integrity + fields required by the official FUNAAB APER forms
 * (Senior Non-Teaching and Junior CONTISS 01-05).
 *
 *  - unique (aper_form_id, staff_id) on every one-row-per-staff table (A5)
 *  - unique (aper_form_id, staff_id, metric_id) on scorings
 *  - aper_forms.review_period_start / _end (1 Oct - 30 Sep)          (F1)
 *  - junior qualifications: subjects & grades                       (F11)
 *  - senior qualifications/professionals: "additional this period"  (F12)
 *  - HOD recommendation: number of increment steps                  (F14)
 *  - scoring guides: code / sort_order / source for auto-scoring    (F7)
 *  - promotion minimum requirements: numeric percentage
 *  - aper_submissions: one status row per staff per APER form       (A3, F16)
 */
return new class extends Migration
{
    private array $oneRowPerStaff = [
        'aper_form_junior_personals',
        'aper_form_junior_sick_leaves',
        'aper_form_junior_health_center_frequencies',
        'aper_form_junior_performances',
        'aper_form_junior_training_needs',
        'aper_form_junior_staff_comments',
        'aper_form_junior_hod_recommend',
        'aper_form_junior_promotion_recommendations',
        'aper_form_senior_personals',
        'aper_form_senior_performances',
        'aper_form_senior_training_needs',
        'aper_form_senior_staff_comments',
        'aper_form_senior_hod_recommends',
        'aper_form_senior_promotion_recommendations',
    ];

    public function up(): void
    {
        // ── 1. De-duplicate, then add unique keys ─────────────────────────
        foreach ($this->oneRowPerStaff as $table) {
            $this->dedupe($table, ['aper_form_id', 'staff_id']);
            $this->addUnique($table, ['aper_form_id', 'staff_id'], $this->indexName($table, 'form_staff_unique'));
        }
        foreach (['aper_form_junior_scorings', 'aper_form_senior_scorings'] as $table) {
            $this->dedupe($table, ['aper_form_id', 'staff_id', 'metric_id']);
            $this->addUnique($table, ['aper_form_id', 'staff_id', 'metric_id'], $this->indexName($table, 'form_staff_metric_unique'));
        }

        // ── 2. Review period on the APER form ────────────────────────────
        Schema::table('aper_forms', function (Blueprint $t) {
            if (!Schema::hasColumn('aper_forms', 'review_period_start')) {
                $t->date('review_period_start')->nullable()->after('name');
            }
            if (!Schema::hasColumn('aper_forms', 'review_period_end')) {
                $t->date('review_period_end')->nullable()->after('review_period_start');
            }
        });

        // ── 3. Official-form fields ──────────────────────────────────────
        $this->addColumn('aper_form_junior_qualifications', 'subjects_grades', fn (Blueprint $t) => $t->text('subjects_grades')->nullable());
        $this->addColumn('aper_form_senior_qualifications', 'is_additional', fn (Blueprint $t) => $t->boolean('is_additional')->default(false));
        $this->addColumn('aper_form_senior_professionals', 'is_additional', fn (Blueprint $t) => $t->boolean('is_additional')->default(false));
        $this->addColumn('aper_form_junior_hod_recommend', 'increment_steps', fn (Blueprint $t) => $t->unsignedTinyInteger('increment_steps')->nullable());
        $this->addColumn('aper_form_senior_hod_recommends', 'increment_steps', fn (Blueprint $t) => $t->unsignedTinyInteger('increment_steps')->nullable());

        foreach (['aper_form_junior_scoring_guides', 'aper_form_senior_scoring_guides'] as $table) {
            $this->addColumn($table, 'code', fn (Blueprint $t) => $t->string('code', 50)->nullable()->after('uuid'));
            $this->addColumn($table, 'sort_order', fn (Blueprint $t) => $t->unsignedSmallInteger('sort_order')->default(0));
            // manual | hod_total | hod_organisational_x2
            $this->addColumn($table, 'source', fn (Blueprint $t) => $t->string('source', 30)->default('manual'));
        }

        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            foreach (['aper_form_junior_promotion_min_reqs', 'aper_form_senior_promotion_min_reqs'] as $table) {
                DB::table($table)->update(['percentage' => DB::raw("REPLACE(TRIM(percentage), '%', '')")]);
                DB::statement("ALTER TABLE `{$table}` MODIFY `percentage` DECIMAL(5,2) NOT NULL DEFAULT 0");
            }
        }

        // ── 4. Per-staff APER status record ──────────────────────────────
        if (!Schema::hasTable('aper_submissions')) {
            Schema::create('aper_submissions', function (Blueprint $t) {
                $t->id();
                $t->uuid('uuid')->unique();
                $t->foreignId('aper_form_id')->constrained('aper_forms')->cascadeOnDelete();
                $t->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
                $t->string('form_type', 20);                 // junior | senior | academic
                $t->string('status', 30)->default('draft');  // see App\Models\AperSubmission
                $t->foreignId('salary_level_id')->nullable()->constrained('salary_levels')->nullOnDelete();

                $t->timestamp('staff_submitted_at')->nullable();
                $t->foreignId('supervisor_staff_id')->nullable()->constrained('staff')->nullOnDelete();
                $t->timestamp('supervisor_signed_at')->nullable();
                $t->foreignId('hod_staff_id')->nullable()->constrained('staff')->nullOnDelete();
                $t->timestamp('hod_signed_at')->nullable();
                $t->timestamp('staff_commented_at')->nullable();
                $t->foreignId('panel_staff_id')->nullable()->constrained('staff')->nullOnDelete();
                $t->timestamp('panel_signed_at')->nullable();

                $t->boolean('recommended_for_promotion')->nullable();
                $t->decimal('total_score', 6, 2)->nullable();
                $t->decimal('min_required', 5, 2)->nullable();
                $t->boolean('meets_minimum')->nullable();
                $t->boolean('punctuality_ok')->nullable();   // 70% punctuality rule

                $t->timestamps();
                $t->unique(['aper_form_id', 'staff_id']);
                $t->index('status');
            });
        }

        // Backfill a draft status row for every Part A already started.
        foreach (['junior' => 'aper_form_junior_personals', 'senior' => 'aper_form_senior_personals'] as $type => $table) {
            DB::table($table)->select('aper_form_id', 'staff_id', 'created_at')->get()
                ->each(function ($row) use ($type) {
                    $exists = DB::table('aper_submissions')
                        ->where('aper_form_id', $row->aper_form_id)->where('staff_id', $row->staff_id)->exists();
                    if (!$exists) {
                        DB::table('aper_submissions')->insert([
                            'uuid' => (string) Str::uuid(),
                            'aper_form_id' => $row->aper_form_id,
                            'staff_id' => $row->staff_id,
                            'form_type' => $type,
                            'status' => 'draft',
                            'created_at' => $row->created_at ?? now(),
                            'updated_at' => now(),
                        ]);
                    }
                });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('aper_submissions');
    }

    // ── helpers ──────────────────────────────────────────────────────────

    private function indexName(string $table, string $suffix): string
    {
        // MySQL identifiers are limited to 64 chars.
        $name = "{$table}_{$suffix}";
        return strlen($name) <= 64 ? $name : substr(md5($table), 0, 10) . "_{$suffix}";
    }

    private function addColumn(string $table, string $column, callable $definition): void
    {
        if (Schema::hasTable($table) && !Schema::hasColumn($table, $column)) {
            Schema::table($table, fn (Blueprint $t) => $definition($t));
        }
    }

    private function dedupe(string $table, array $keys): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }
        $dupes = DB::table($table)->select($keys)->selectRaw('MAX(id) AS keep_id')
            ->groupBy($keys)->havingRaw('COUNT(*) > 1')->get();

        foreach ($dupes as $d) {
            $q = DB::table($table)->where('id', '<>', $d->keep_id);
            foreach ($keys as $k) {
                $q->where($k, $d->{$k});
            }
            $q->delete();
        }
    }

    private function addUnique(string $table, array $columns, string $name): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }
        $exists = in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)
            ? !empty(DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$name]))
            : collect(DB::select("PRAGMA index_list('{$table}')"))->contains('name', $name);

        if (!$exists) {
            Schema::table($table, fn (Blueprint $t) => $t->unique($columns, $name));
        }
    }
};
