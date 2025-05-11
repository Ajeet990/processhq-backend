<?php

namespace App\Http\Controllers;
use App\Models\ErrorLog;
use Throwable;
use Illuminate\Support\Facades\Log;



abstract class Controller
{
    protected function logError(Throwable $e, $request = null)
    {
        try {
            $request = $request ?? request(); // Use provided request or fall back to current request
    
            ErrorLog::create([
                'code' => method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500,
                'message' => $e->getMessage(),
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'user_id' => auth()->id(),
            ]);
        } catch (Throwable $loggingError) {
            // Fallback to logging to Laravel's default log if DB logging fails
            Log::error('Failed to log error to database', [
                'original_error' => $e->getMessage(),
                'logging_error' => $loggingError->getMessage()
            ]);
        }
    }
}
