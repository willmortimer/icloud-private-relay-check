<?php

namespace App\Console\Commands;

use App\Services\PrivateRelayService;
use Illuminate\Console\Command;

class UpdateRelayIPs extends Command
{
    protected $signature = 'relay:update-ips';
    protected $description = 'Update iCloud Private Relay IP ranges from Apple\'s CSV';

    private $relayService;

    public function __construct(PrivateRelayService $relayService)
    {
        parent::__construct();
        $this->relayService = $relayService;
    }

    public function handle()
    {
        $this->info('Updating iCloud Private Relay IP ranges...');
        
        try {
            $this->relayService->initializeTrie();
            $this->info('Successfully updated IP ranges.');
        } catch (\Exception $e) {
            $this->error('Failed to update IP ranges: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
} 