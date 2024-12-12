<?php

namespace App\Services;

class IpTrie
{
    private $root;

    public function __construct()
    {
        $this->root = new TrieNode();
    }

    public function insert(string $cidr): void
    {
        list($network, $bits) = explode('/', $cidr);
        
        // Validate IP and bits
        if (!filter_var($network, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return;
        }
        
        $bits = (int)$bits;
        if ($bits < 0 || $bits > 32) {
            return;
        }
        
        $ip = ip2long($network);
        if ($bits === 32) {
            $mask = -1;
        } else {
            $mask = -1 << (32 - $bits);
        }
        $network = $ip & $mask;
        
        $node = $this->root;
        for ($i = 0; $i < 32; $i++) {
            $bit = ($network >> (31 - $i)) & 1;
            if (!isset($node->children[$bit])) {
                $node->children[$bit] = new TrieNode();
            }
            $node = $node->children[$bit];
            if ($i == $bits - 1) {
                $node->isEnd = true;
                break;
            }
        }
    }

    public function contains(string $ip): bool
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return false;
        }

        $ipLong = ip2long($ip);
        $node = $this->root;
        
        for ($i = 0; $i < 32; $i++) {
            if ($node->isEnd) {
                return true;
            }
            
            $bit = ($ipLong >> (31 - $i)) & 1;
            if (!isset($node->children[$bit])) {
                return false;
            }
            $node = $node->children[$bit];
        }
        
        return $node->isEnd;
    }
}

class TrieNode
{
    public $children;
    public $isEnd;

    public function __construct()
    {
        $this->children = [];
        $this->isEnd = false;
    }
} 