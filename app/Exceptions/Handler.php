<?php
/**
 * Php version 5.6 || 7.0
 *
 * Class Handler
 *
 * @category Interview
 * @package  App\Exceptions
 * @author   Georgi Staykov <g.staikov85@gmail.com>
 * @license  Just Man
 * @link     localhost
 */
namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class Handler
 *
 * @category Interview
 * @package  App\Exceptions
 * @author   Georgi Staykov <g.staikov85@gmail.com>
 * @license  Just Man
 * @link     localhost
 */
class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport
        = [
            AuthorizationException::class,
            HttpException::class,
            ModelNotFoundException::class,
            ValidationException::class,
        ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param \Exception $e Error
     *
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request Request object
     * @param \Exception               $e       Error
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        $error = ['error message'   => $e->getMessage(),];

        if (env('APP_DEBUG')) {
            $error = [
                'error code'      => $e->getCode(),
                'error message'   => $e->getMessage(),
                'file with error' => $e->getFile(),
                'line with error' => $e->getLine(),
                'trace'           => $e->getTrace(),
            ];
        }

        if ($e instanceof NotFoundHttpException
            || $e instanceof  MethodNotAllowedHttpException
        ) {
            $error = ['status code'   => $e->getStatusCode(),];
        }

        // return response()->json(['error' => $error]);
        return parent::render($request, $e);
    }
}
