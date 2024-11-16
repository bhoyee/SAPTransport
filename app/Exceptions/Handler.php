<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpFoundation\Exception\SessionExpiredException;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;


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
        // Check if the exception is a TokenMismatchException (CSRF token issue)
        if ($exception instanceof TokenMismatchException) {
            // Log the occurrence of a TokenMismatchException
            \Log::warning('Token mismatch detected. Redirecting user to the home page.', [
                'url' => $request->url(),
            ]);
    
            // Redirect the user to the home page
            return redirect()->route('home')
                ->with('error', 'Your session has expired. Please try again.');
        }
    
        return parent::render($request, $exception);
    }
    
}
