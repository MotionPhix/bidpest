<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
  ->withRouting(
    web: __DIR__ . '/../routes/web.php',
    commands: __DIR__ . '/../routes/console.php',
    health: '/up',
  )
  ->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
      \App\Http\Middleware\HandleInertiaRequests::class,
      \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
      \Inertia\EncryptHistoryMiddleware::class,
    ]);

    //
  })
  ->withExceptions(function (Exceptions $exceptions) {

    $exceptions->renderable(function (ModelNotFoundException $e) {

      return Inertia::render('Errors/Index', [
        'message' => 'The requested resource was not found.',
        'status' => 404,
      ])->toResponse(request())->setStatusCode(404);

    });

    $exceptions->renderable(function (Throwable $e) {

      if (!app()->environment(['local', 'testing']) &&
        in_array($e->getCode(), [500, 503, 404, 403])) {
        return Inertia::render('Errors/Index', [
          'status' => $e->getCode()
        ])->toResponse(request())
          ->setStatusCode($e->getCode());
      }

      // Handle page expiration
      if ($e->getCode() === 419) {
        return back()->with([
          'message' => 'The page expired, please try again.',
        ]);
      }
    });

  })->create();
