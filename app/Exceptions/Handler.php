<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Spatie\Permission\Exceptions\UnauthorizedException;
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
    public function render($request, Throwable $exception)
    {
        // Redireciona se o usuário não estiver autenticado (sessão expirada)
        if ($exception instanceof AuthenticationException) {
            return redirect()->guest(route('login'))->withErrors([
                'Sessão expirada. Faça login novamente.',
            ]);
        }

        // Redireciona se for erro de permissão (usuário logado, mas sem role/permissão)
        if ($exception instanceof UnauthorizedException) {
            return redirect()->route('home')->with(['error' => 'Você não tem permissão para acessar esta página.']);
        }

        if ($exception instanceof \Illuminate\Session\TokenMismatchException) {
            return redirect()->guest(route('login'))->withErrors([
                'Sessão expirada. Faça login novamente.',
            ]);
        }

        return parent::render($request, $exception);
    }
}
