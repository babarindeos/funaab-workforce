<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Fills the `uuid` column automatically when a record is created,
 * if the model's table has one. The uuid is never changed afterwards.
 */
trait HasUuid
{
    /** @var array<string,bool> */
    protected static array $uuidColumnCache = [];

    protected static function bootHasUuid(): void
    {
        static::creating(function ($model) {
            $table = $model->getTable();

            if (!array_key_exists($table, self::$uuidColumnCache)) {
                self::$uuidColumnCache[$table] = Schema::hasColumn($table, 'uuid');
            }

            if (self::$uuidColumnCache[$table] && empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });

        // Protect existing uuids from being overwritten by mass updates.
        static::updating(function ($model) {
            if ($model->isDirty('uuid') && !empty($model->getOriginal('uuid'))) {
                $model->uuid = $model->getOriginal('uuid');
            }
        });
    }
}
