<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\IpTrie;

class PrivateRelayService
{
    private $relayRangesUrl = 'https://mask-api.icloud.com/egress-ip-ranges.csv';
    private static $ipTrie = null;

    public function __construct()
    {
        if (self::$ipTrie === null) {
            $this->initializeTrie();
        }
    }

    public function initializeTrie(): void
    {
        if (self::$ipTrie === null) {
            self::$ipTrie = new IpTrie();
        }
        
        try {
            $response = Http::get($this->relayRangesUrl);
            if ($response->successful()) {
                $lines = explode("\n", trim($response->body()));
                Log::debug('Fetched IP ranges', [
                    'count' => count($lines),
                    'sample' => array_slice($lines, 0, 3)
                ]);
                
                foreach ($lines as $line) {
                    if (trim($line)) {
                        try {
                            self::$ipTrie->insert(trim($line));
                        } catch (\Exception $e) {
                            Log::warning('Failed to insert IP range', [
                                'range' => $line,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                }
            } else {
                Log::error('Failed to fetch iCloud Private Relay ranges: ' . $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Failed to fetch iCloud Private Relay ranges: ' . $e->getMessage());
        }
    }

    public function isPrivateRelayIP(string $ip): bool
    {
        return self::$ipTrie->contains($ip);
    }

    public function detectDevice(string $userAgent): array
    {
        $isFirefox = (bool) preg_match('/Firefox/i', $userAgent);
        $isChrome = (bool) preg_match('/Chrome/i', $userAgent);
        $isChromium = (bool) preg_match('/Chromium/i', $userAgent);
        $isSafari = (bool) (
            preg_match('/Safari/i', $userAgent) && 
            !$isChrome && 
            !$isChromium
        );
        $isWindows = (bool) preg_match('/Windows/i', $userAgent);

        // Determine browser with more specific matching
        $browser = 'Unknown';
        if ($isSafari) {
            $browser = 'Safari';
        } elseif ($isFirefox) {
            $browser = 'Firefox' . ($isWindows ? ' (Windows)' : '');
        } elseif ($isChrome) {
            $browser = 'Chrome';
        } elseif ($isChromium) {
            $browser = 'Chromium';
        }

        return [
            'is_iphone' => (bool) preg_match('/iPhone/i', $userAgent),
            'is_mac' => (bool) (preg_match('/Macintosh/i', $userAgent) || preg_match('/Mac OS X/i', $userAgent)),
            'is_windows' => $isWindows,
            'is_safari' => $isSafari,
            'is_firefox' => $isFirefox,
            'is_chrome' => $isChrome,
            'is_chromium' => $isChromium,
            'browser' => $browser,
            'user_agent_display' => 'Other - ' . $userAgent
        ];
    }
} 