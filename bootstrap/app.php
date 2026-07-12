<?php

use Domain\Beneficiaries\Exceptions\BeneficiaryAlreadyExistsException;
use Domain\Beneficiaries\Exceptions\BeneficiaryAttributeException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Shared\Exceptions\EntityNotFoundException;
use Shared\Exceptions\InvalidIdentifierException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (EntityNotFoundException $e) {
            return response()->json([
                'error' => 'Not Found',
                'message' => $e->getMessage(),
            ], 404);
        });

        $exceptions->render(function (BeneficiaryAlreadyExistsException $e) {
            return response()->json([
                'error' => 'Conflict',
                'message' => $e->getMessage(),
            ], 409);
        });

        $exceptions->render(function (BeneficiaryAttributeException $e) {
            return response()->json([
                'error' => 'Unprocessable Entity',
                'message' => $e->getMessage(),
            ], 422);
        });

        $exceptions->render(function (InvalidIdentifierException $e) {
            return response()->json([
                'error' => 'Bad Request',
                'message' => $e->getMessage(),
            ], 400);
        });

        $exceptions->render(function (\InvalidArgumentException $e) {
            return response()->json([
                'error' => 'Unprocessable Entity',
                'message' => $e->getMessage(),
            ], 422);
        });

        $exceptions->render(function (\DomainException $e) {
            return response()->json([
                'error' => 'Unprocessable Entity',
                'message' => $e->getMessage(),
            ], 422);
        });
    })->create();
