<!DOCTYPE html>
<html lang="en" class="light">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>iCloud Private Relay Status</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=SF+Pro+Display:wght@400;500;600&display=swap" rel="stylesheet">
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    </head>
    <body class="h-screen bg-[#F5F5F7] dark:bg-[#1C1C1E] transition-colors duration-200 text-base flex flex-col justify-center items-center overflow-hidden">
        <div class="container mx-auto px-6 w-full max-w-4xl space-y-6">
            <!-- Header -->
            <div class="text-center mb-2">
                <h1 class="text-4xl font-medium text-gray-900 dark:text-white mb-1">iCloud Private Relay Status</h1>
                <p class="text-xl text-gray-500 dark:text-gray-400">Check if you're using iCloud Private Relay</p>
            </div>

            <!-- Results Card -->
            <div id="results" class="glass rounded-2xl p-6 shadow-lg mb-2">
                <div class="animate-pulse">
                    <div class="h-5 bg-gray-200 dark:bg-gray-700 rounded w-3/4"></div>
                    <div class="space-y-4 mt-4">
                        <div class="h-5 bg-gray-200 dark:bg-gray-700 rounded"></div>
                        <div class="h-5 bg-gray-200 dark:bg-gray-700 rounded"></div>
                        <div class="h-5 bg-gray-200 dark:bg-gray-700 rounded"></div>
                    </div>
                </div>
            </div>

            <!-- Info Card -->
            <div class="glass rounded-2xl p-6 shadow-lg">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-2">About iCloud Private Relay</h2>
                <div class="prose dark:prose-invert">
                    <p class="text-lg text-gray-600 dark:text-gray-300 mb-2">iCloud Private Relay is an iCloud+ service that prevents websites from tracking your internet activity by:</p>
                    <ul class="list-none space-y-3 mb-2">
                        <li class="flex items-center text-lg text-gray-600 dark:text-gray-300">
                            <svg class="w-6 h-6 mr-3 text-apple-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Hiding your IP address
                        </li>
                        <li class="flex items-center text-lg text-gray-600 dark:text-gray-300">
                            <svg class="w-6 h-6 mr-3 text-apple-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Encrypting your internet traffic
                        </li>
                        <li class="flex items-center text-lg text-gray-600 dark:text-gray-300">
                            <svg class="w-6 h-6 mr-3 text-apple-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Using two separate relays
                        </li>
                    </ul>
                    <p class="text-base text-gray-500 dark:text-gray-400">Available on iOS 15+, iPadOS 15+, and macOS Monterey+ with Safari</p>
                    <p class="text-base text-gray-500 dark:text-gray-400 mt-2">
                        <a href="https://www.apple.com/legal/privacy/data/en/icloud-relay/" class="text-apple-blue hover:underline" target="_blank" rel="noopener noreferrer">
                            Learn more about iCloud Private Relay →
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </body>
</html> 