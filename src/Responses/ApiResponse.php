<?php declare(strict_types=1);

namespace App\Responses;

use Carbon\Carbon;
use Teapot\StatusCode;
use Zend\Diactoros\Response;

class ApiResponse
{
    private const CONTENT_TYPE = 'application/json';

    private static function buildResponse($statusCode): Response
    {
        return (new Response())
            ->withStatus($statusCode)
            ->withHeader('Content-type', self::CONTENT_TYPE);
    }

    private static function buildData(bool $success, $payload = null, $message = null): string
    {
        return json_encode([
            'status' => $success,
            'payload' => $payload,
            'rendered' => Carbon::now()->timestamp
        ]);
    }

    public static function success($payload, $statusCode = StatusCode::OK): Response
    {
        $response = self::buildResponse($statusCode);
        $response->getBody()->write(
            self::buildData(true, $payload)
        );

        return $response;
    }

    public static function error($message, $statusCode = StatusCode::OK): Response
    {
        $response = self::buildResponse($statusCode);
        $response->getBody()->write(
            self::buildData(false, null, $message)
        );

        return $response;
    }
}
