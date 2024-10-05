<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpFoundation\Exception\SessionExpiredException;
use Illuminate\Session\TokenMismatchException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
           // Handle CSRF Token Mismatch or Session Timeout (419)
        if ($exception instanceof TokenMismatchException || $exception instanceof SessionExpiredException) {
            // Redirect to login page with a session expired message
            return redirect()->route('login')->with('message', 'Your session has expired. Please log in again.');
        }

        return parent::render($request, $exception);
    }
}
