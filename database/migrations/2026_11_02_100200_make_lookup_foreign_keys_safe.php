<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Most foreign keys were created with ON DELETE CASCADE, which means that
 * deleting a lookup row silently deletes real records: removing a gender,
 * state, salary step or department would delete every staff member or
 * employment record that points to it (and their APER forms with them).
 *
 * This migration changes those keys to:
 *   - RESTRICT  for references to lookup / master-data tables
 *               (the delete is refused while the row is in use);
 *   - SET NULL  for audit columns (created_by, updated_by, assessor…).
 *
 * CASCADE is kept only where the child truly belongs to the parent:
 *   - rows owned by a staff member or an APER form,
 *   - profiles / staff_updates owned by a user,
 *   - identity_update_histories owned by an identity.
 */
return new class extends Migration
{
    private array $keepCascadeParents = ['staff', 'aper_forms', 'identities'];

    private array $keepCascadePairs = [
        'profiles.user_id',
        'staff_updates.user_id',
    ];

    private array $setNullColumns = ['created_by', 'updated_by'];

    public function up(): void
    {
        if (!in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            return; // SQLite (tests) cannot alter foreign keys in place.
        }

        $schema = DB::getDatabaseName();

        $fks = DB::select("
            SELECT r.CONSTRAINT_NAME AS name, r.TABLE_NAME AS tbl, k.COLUMN_NAME AS col,
                   r.REFERENCED_TABLE_NAME AS ref_tbl, k.REFERENCED_COLUMN_NAME AS ref_col,
                   r.UPDATE_RULE AS on_update, c.IS_NULLABLE AS nullable
            FROM information_schema.REFERENTIAL_CONSTRAINTS r
            JOIN information_schema.KEY_COLUMN_USAGE k
              ON k.CONSTRAINT_SCHEMA = r.CONSTRAINT_SCHEMA
             AND k.CONSTRAINT_NAME  = r.CONSTRAINT_NAME
             AND k.TABLE_NAME       = r.TABLE_NAME
            JOIN information_schema.COLUMNS c
              ON c.TABLE_SCHEMA = k.TABLE_SCHEMA AND c.TABLE_NAME = k.TABLE_NAME AND c.COLUMN_NAME = k.COLUMN_NAME
            WHERE r.CONSTRAINT_SCHEMA = ? AND r.DELETE_RULE = 'CASCADE'
        ", [$schema]);

        foreach ($fks as $fk) {
            if (in_array($fk->ref_tbl, $this->keepCascadeParents, true)
                || in_array("{$fk->tbl}.{$fk->col}", $this->keepCascadePairs, true)) {
                continue;
            }

            $rule = (in_array($fk->col, $this->setNullColumns, true) && $fk->nullable === 'YES')
                ? 'SET NULL'
                : 'RESTRICT';

            DB::statement("ALTER TABLE `{$fk->tbl}` DROP FOREIGN KEY `{$fk->name}`");
            DB::statement(sprintf(
                'ALTER TABLE `%s` ADD CONSTRAINT `%s` FOREIGN KEY (`%s`) REFERENCES `%s` (`%s`) ON DELETE %s ON UPDATE %s',
                $fk->tbl, $fk->name, $fk->col, $fk->ref_tbl, $fk->ref_col, $rule, $fk->on_update
            ));
        }
    }

    public function down(): void
    {
        // Intentionally not reversible: cascading deletes on lookups are unsafe.
    }
};
