<?php

namespace App\Http\Controllers;

use App\Services\PrivateRelayService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class PrivateRelayController extends Controller
{
    private $relayService;

    public function __construct(PrivateRelayService $relayService)
    {
        $this->relayService = $relayService;
    }

    public function index(): View
    {
        return view('private-relay.check');
    }

    public function check(Request $request)
    {
        try {
            // Get the enhanced device info and relay status from middleware
            $deviceInfo = $request->attributes->get('device_info', []);
            $isPrivateRelay = $request->attributes->get('is_private_relay', false);
            $clientIp = $request->attributes->get('raw_ip', '127.0.0.1');

            // Log the raw data for debugging
            Log::debug('Private Relay Check', [
                'ip' => $clientIp,
                'device_info' => $deviceInfo,
                'is_private_relay' => $isPrivateRelay,
                'all_attributes' => $request->attributes->all(),
                'all_headers' => $request->headers->all()
            ]);

            // Ensure deviceInfo is an array and has required keys
            if (!is_array($deviceInfo)) {
                $deviceInfo = [];
            }

            $deviceInfo = array_merge([
                'is_safari' => false,
                'is_firefox' => false,
                'is_chrome' => false,
                'is_chromium' => false,
                'is_iphone' => false,
                'is_mac' => false,
                'browser' => 'Unknown',
                'user_agent' => $request->userAgent() ?? 'Unknown',
                'headers' => []
            ], $deviceInfo);

            // Re-detect browser if needed
            if ($deviceInfo['browser'] === 'Unknown' && $deviceInfo['user_agent'] !== 'Unknown') {
                $redetected = $this->relayService->detectDevice($deviceInfo['user_agent']);
                $deviceInfo = array_merge($deviceInfo, $redetected);
            }

            return response()->json([
                'ip' => $clientIp,
                'is_private_relay' => $isPrivateRelay,
                'device' => $deviceInfo,
                'can_use_private_relay' => $deviceInfo['is_safari'] && ($deviceInfo['is_iphone'] || $deviceInfo['is_mac']),
                'raw_data' => [
                    'headers' => $deviceInfo['headers'],
                    'user_agent' => $deviceInfo['user_agent']
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error in PrivateRelayController: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Failed to process request',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
