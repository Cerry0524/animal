<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use App\Traits\ApiResponseTrait;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    use ApiResponseTrait; //引用特徵
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
    }

    public function render($request, Throwable $e)
    {
        Log::info("Exception detected: " . get_class($e));

        // dd($e);
        if ($request->expectsJson()) {
            //1.Model找不到資源
            if ($e instanceof NotFoundHttpException) {
                // Log::info("Returning custom 404-1 response");
                return $this->errorResponse(
                    '找不到資源',
                    Response::HTTP_NOT_FOUND
                );
            }
            //2.網址輸入錯誤
            if ($e instanceof NotFoundHttpException) {
                // Log::info("Returning custom 405 response");
                return $this->errorResponse(
                    '無法找到此網址',
                    Response::HTTP_NOT_FOUND
                );
            }
            //3.網址不允許請求動詞
            if ($e instanceof MethodNotAllowedHttpException) {
                // Log::info("Returning custom 404-3 response");
                return $this->errorResponse(
                    $e->getMessage(),
                    Response::HTTP_METHOD_NOT_ALLOWED
                );
            }
        }
        return parent::render($request, $e);
    }
}
