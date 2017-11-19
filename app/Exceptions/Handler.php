<?php

namespace App\Exceptions;

use App\Models\Error\ErrorException;
use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Config;
use Redirect;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
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
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        /*Exclude uri from generating exceptions*/
        $exclude_uri_array = Config::get('url')['exclude_exception_uri'];

        $uri = $request->getRequestUri();

        if(!isset($exclude_uri_array[$uri])) {

            /*Save exception*/
            $this->saveException($e, $uri);

            /*Redirect*/
           // return Redirect::action('Auth\AuthController@error');
        }

        return parent::render($request, $e);
    }

    /**
     * Save the exception
     *
     * @param  \Exception  $e
     * @param $uri string
     *
     * @return bool
     */
    protected function saveException(\Exception $e, $uri)
    {
        $exception = FlattenException::create($e);

        $array = array(
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
            'uri' => $uri,
            'json' => json_encode($exception->toArray())
        );

        $ErrorException = ErrorException::create($array);

        if($ErrorException != null) {
            return true;
        }

        return false;
    }
}
