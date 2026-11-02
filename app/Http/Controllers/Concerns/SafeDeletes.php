<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;

/**
 * Deletes a lookup record, refusing politely when other records still use
 * it (foreign keys are ON DELETE RESTRICT since 2 Nov 2026).
 */
trait SafeDeletes
{
    protected function safeDelete(Model $model, string $label): RedirectResponse
    {
        try {
            $model->delete();
        } catch (QueryException $e) {
            if (($e->errorInfo[0] ?? null) === '23000') {
                return back()->with([
                    'error'   => "This {$label} cannot be deleted because it is still in use by other records.",
                    'status'  => 'fail',
                    'message' => "This {$label} cannot be deleted because it is still in use by other records.",
                ]);
            }
            throw $e;
        }

        return back()->with([
            'success' => ucfirst($label) . ' deleted successfully.',
            'status'  => 'success',
            'message' => ucfirst($label) . ' deleted successfully.',
        ]);
    }
}
