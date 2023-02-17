<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HasApiResponses;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use const PHP_VERSION;

class HealthController extends Controller
{
    use HasApiResponses;

    public function ping(Request $request): JsonResponse
    {
        try {
            DB::connection()->getPdo();

            return $this->respondSuccessWithData([
                'container' => 'ok',
                'database' => 'ok',
            ]);
        } catch (Exception $e) {
            Log::error('Database connection failed health check [' . $e->getMessage() . ']');

            return $this->respondErrorException($e, 'Database connection failed health check', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function info(Request $request): JsonResponse
    {
        logger()->info('Application Info accessed', ['ip' => $request->getClientIp()]);

        try {
            $dbName = DB::connection()->getDatabaseName();

            return $this->respondSuccessWithData([
                'app' => config('app.name'),
                'env' => config('app.env'),
                'debug' => config('app.debug'),
                'php' => PHP_VERSION,
                'logging' => config('logging.default'),
                'filesystem' => config('filesystems.default'),
                'cloud' => config('filesystems.cloud'),
                'cache' => config('cache.default'),
                'session' => config('session.driver'),
                'db' => config('database.default'),
                'db_name' => $dbName,
                'mailer' => config('mail.default'),
                'queue' => config('queue.default'),
                'mail_from' => config('mail.from.address'),
            ]);
        } catch (Exception $e) {
            return $this->respondErrorException($e, 'Connection failed', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
