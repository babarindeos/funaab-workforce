<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Columns that are optional on the official APER forms but were created
 * NOT NULL, so saving a perfectly valid form failed (e.g. a HOD who
 * recommends an increment leaves "No salary increment – reasons" blank).
 */
return new class extends Migration
{
    private array $columns = [
        'aper_form_junior_hod_recommend'      => ['no_salary_increment'],
        'aper_form_senior_hod_recommends'     => ['no_salary_increment'],
        'aper_form_senior_performances'       => ['report_agreement_comment'],
        'aper_form_junior_qualifications'     => ['institution', 'from_date', 'to_date', 'qualification_obtained'],
        'aper_form_senior_qualifications'     => ['degree'],
        'aper_form_senior_professionals'      => ['qualification', 'award_body', 'date_obtained'],
        'aper_form_senior_position_helds'     => ['position_held', 'duty'],
        'aper_form_junior_personals'          => ['division_id', 'department_id', 'marital_status_id', 'retirement_date'],
        'aper_form_senior_personals'          => ['division_id', 'department_id', 'marital_status_id', 'retirement_date'],
    ];

    public function up(): void
    {
        if (!in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            return;
        }

        $schema = DB::getDatabaseName();

        foreach ($this->columns as $table => $cols) {
            if (!Schema::hasTable($table)) continue;
            foreach ($cols as $col) {
                $info = DB::selectOne(
                    'SELECT COLUMN_TYPE, IS_NULLABLE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?',
                    [$schema, $table, $col]
                );
                if ($info && $info->IS_NULLABLE === 'NO') {
                    DB::statement("ALTER TABLE `{$table}` MODIFY `{$col}` {$info->COLUMN_TYPE} NULL");
                }
            }
        }
    }

    public function down(): void
    {
        // Not reversible: the values may now legitimately be empty.
    }
};
