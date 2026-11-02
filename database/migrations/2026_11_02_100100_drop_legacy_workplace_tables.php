<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Removes the tables left over from the earlier "WorkPlace" app
 * (circles/cells, documents/workflows, messages, announcements,
 * ministries, deans, categories). Their models and migrations no longer
 * exist in the code base, and all of them were empty on 2 Nov 2026.
 *
 * A table that still holds rows is NOT dropped; it is reported instead.
 */
return new class extends Migration
{
    private array $tables = [
        // children first
        'announcement_comments', 'announcement_files', 'announcement_permissions', 'announcements',
        'circle_general_rooms', 'cell_users', 'cells', 'cell_types',
        'private_messages', 'general_messages', 'flow_contributors', 'workflows', 'documents',
        'deans', 'ministries', 'categories',
    ];

    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        foreach ($this->tables as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }
            if (DB::table($table)->exists()) {
                echo "  ! Skipped dropping `{$table}`: it still contains data.\n";
                continue;
            }
            Schema::drop($table);
        }

        Schema::enableForeignKeyConstraints();

        // Forget the old migration records so `migrate:status` stays clean.
        if (Schema::hasTable('migrations')) {
            DB::table('migrations')->whereIn('migration', [
                '2019_12_13_010801_create_cell_types_table',
                '2019_12_15_005335_create_cells_table',
                '2024_07_10_110612_create_ministries_table',
                '2024_07_11_041227_create_categories_table',
                '2024_07_11_075655_create_deans_table',
                '2024_07_12_075104_create_documents_table',
                '2024_07_13_135209_create_flow_contributors_table',
                '2024_07_13_144737_create_workflows_table',
                '2024_07_21_010617_create_general_messages_table',
                '2024_07_21_073050_create_private_messages_table',
                '2024_09_18_172806_create_cell_users_table',
                '2024_09_19_144425_create_circle_general_rooms_table',
                '2024_09_20_124832_create_announcements_table',
                '2024_09_20_125604_create_announcement_files_table',
                '2024_09_24_155609_create_announcement_comments_table',
                '2024_10_13_175257_create_announcement_permissions_table',
            ])->delete();
        }
    }

    public function down(): void
    {
        // Not reversible: the legacy modules have been removed from the code.
    }
};
