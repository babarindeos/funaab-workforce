<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Staff record history:
 *  - next_of_kin: relationship, is_current (older entries kept as "previous"), replaced_at
 *  - deployments: from_department_id (where the staff member moved from)
 *  - off_employments: previous_staff_status_id (to restore the status if the record is deleted)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('next_of_kin', function (Blueprint $t) {
            if (!Schema::hasColumn('next_of_kin', 'relationship')) $t->string('relationship', 50)->nullable()->after('staff_id');
            if (!Schema::hasColumn('next_of_kin', 'is_current'))   $t->boolean('is_current')->default(true)->after('email');
            if (!Schema::hasColumn('next_of_kin', 'replaced_at'))  $t->timestamp('replaced_at')->nullable()->after('is_current');
        });
        // If a staff member already has several, only the newest stays current.
        DB::table('next_of_kin')->select('staff_id')->groupBy('staff_id')->havingRaw('COUNT(*) > 1')->pluck('staff_id')
            ->each(function ($staffId) {
                $keep = DB::table('next_of_kin')->where('staff_id', $staffId)->max('id');
                DB::table('next_of_kin')->where('staff_id', $staffId)->where('id', '<>', $keep)
                    ->update(['is_current' => false, 'replaced_at' => now()]);
            });

        if (!Schema::hasColumn('deployments', 'from_department_id')) {
            Schema::table('deployments', function (Blueprint $t) {
                $t->foreignId('from_department_id')->nullable()->after('staff_id')->constrained('departments')->restrictOnDelete();
            });
            // Existing records: assume they moved from their home department.
            DB::table('deployments')->whereNull('from_department_id')->orderBy('id')->get(['id', 'staff_id'])
                ->each(function ($d) {
                    $home = DB::table('employments')->where('staff_id', $d->staff_id)->value('department_id');
                    if ($home) DB::table('deployments')->where('id', $d->id)->update(['from_department_id' => $home]);
                });
        }

        if (!Schema::hasColumn('off_employments', 'previous_staff_status_id')) {
            Schema::table('off_employments', function (Blueprint $t) {
                $t->foreignId('previous_staff_status_id')->nullable()->after('staff_status_id')->constrained('staff_statuses')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::table('off_employments', function (Blueprint $t) {
            $t->dropConstrainedForeignId('previous_staff_status_id');
        });
        Schema::table('deployments', function (Blueprint $t) {
            $t->dropConstrainedForeignId('from_department_id');
        });
        Schema::table('next_of_kin', function (Blueprint $t) {
            $t->dropColumn(['relationship', 'is_current', 'replaced_at']);
        });
    }
};
