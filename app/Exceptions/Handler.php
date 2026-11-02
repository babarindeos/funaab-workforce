<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // A record that is still referenced elsewhere cannot be deleted
        // (lookup foreign keys are ON DELETE RESTRICT). Tell the user
        // instead of showing a 500 error page.
        $this->renderable(function (\Illuminate\Database\QueryException $e, $request) {
            $isFkViolation = ($e->errorInfo[0] ?? null) === '23000'
                && str_contains(strtolower($e->getMessage()), 'foreign key');

            if ($isFkViolation && !$request->expectsJson()) {
                $msg = $request->isMethod('delete')
                    ? 'This record cannot be deleted because other records still use it.'
                    : 'This change refers to a record that does not exist or is still in use.';

                return back()->withInput()->with(['error' => $msg, 'status' => 'fail', 'message' => $msg]);
            }
        });
    }
}
