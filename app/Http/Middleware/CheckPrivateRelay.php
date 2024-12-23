<?php

namespace App\Http\Middleware;

use App\Services\PrivateRelayService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CheckPrivateRelay
{
    private $relayService;

    public function __construct(PrivateRelayService $relayService)
    {
        $this->relayService = $relayService;
    }

    public function handle(Request $request, Closure $next)
    {
        try {
            // Try different methods to get the client IP
            $clientIp = $request->header('x-forwarded-for') ?? 
                       $request->server('REMOTE_ADDR') ?? 
                       $request->ip() ?? 
                       '127.0.0.1'; // Fallback to localhost if nothing else works
            
            $userAgent = $request->userAgent() ?? 'Unknown';
            
            // Log incoming request data
            Log::debug('Private Relay Middleware - Request Data', [
                'ip' => $clientIp,
                'user_agent' => $userAgent,
                'headers' => $request->headers->all(),
                'server' => $request->server->all()
            ]);
            
            // Get relevant headers for Private Relay detection
            $relevantHeaders = [
                'client-connection',
                'x-forwarded-for',
                'forwarded',
                'via',
                'user-agent'
            ];

            $headers = collect($relevantHeaders)->mapWithKeys(function ($header) use ($request) {
                return [$header => $request->header($header)];
            })->filter()->all();

            // Get device info with raw values
            $deviceInfo = $this->relayService->detectDevice($userAgent);
            Log::debug('Device Detection', [
                'user_agent' => $userAgent,
                'device_info' => $deviceInfo
            ]);

            // Add raw values to device info
            $deviceInfo['user_agent'] = $userAgent;
            $deviceInfo['headers'] = $headers;

            $isPrivateRelay = $this->relayService->isPrivateRelayIP($clientIp);

            // Log the processed data
            Log::debug('Private Relay Middleware - Processed Data', [
                'is_private_relay' => $isPrivateRelay,
                'device_info' => $deviceInfo,
                'headers' => $headers
            ]);

            // Add relay status and enhanced device info to the request attributes
            $request->attributes->add([
                'is_private_relay' => $isPrivateRelay,
                'device_info' => $deviceInfo,
                'raw_ip' => $clientIp
            ]);

            return $next($request);
        } catch (\Exception $e) {
            Log::error('Private Relay Middleware Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Continue the request even if there's an error, but with default values
            $request->attributes->add([
                'is_private_relay' => false,
                'device_info' => [
                    'is_iphone' => false,
                    'is_mac' => false,
                    'is_safari' => false,
                    'is_firefox' => false,
                    'is_chrome' => false,
                    'is_chromium' => false,
                    'browser' => 'Unknown',
                    'user_agent' => $request->userAgent() ?? 'Unknown',
                    'headers' => []
                ],
                'raw_ip' => $request->ip() ?? '127.0.0.1'
            ]);

            return $next($request);
        }
    }
}
