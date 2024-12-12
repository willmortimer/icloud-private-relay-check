import "./bootstrap";

document.addEventListener("DOMContentLoaded", function () {
    fetch("/check-relay")
        .then((response) => response.json())
        .then((data) => {
            const results = document.getElementById("results");
            if (!results) return;

            const statusColor = data.is_private_relay
                ? "bg-apple-green/10 dark:bg-apple-green/20"
                : "bg-apple-yellow/10 dark:bg-apple-yellow/20";
            const statusTextColor = data.is_private_relay
                ? "text-apple-green"
                : "text-apple-yellow";

            results.innerHTML = `
                <div class="space-y-6">
                    <!-- Status Banner -->
                    <div class="rounded-xl ${statusColor} p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                ${
                                    data.is_private_relay
                                        ? '<svg class="w-10 h-10 text-apple-green" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
                                        : '<svg class="w-10 h-10 text-apple-yellow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>'
                                }
                            </div>
                            <div class="ml-4">
                                <h3 class="text-2xl font-semibold ${statusTextColor}">
                                    ${
                                        data.is_private_relay
                                            ? "Private Relay is active"
                                            : "Private Relay not detected"
                                    }
                                </h3>
                                <p class="text-lg text-gray-600 dark:text-gray-300 mt-2">
                                    ${
                                        data.is_private_relay
                                            ? "Your connection is being routed through iCloud Private Relay"
                                            : "Your connection is direct or using a different proxy"
                                    }
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Details Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-6">
                            <div class="space-y-3">
                                <h4 class="text-lg font-medium text-gray-500 dark:text-gray-400">IP Address</h4>
                                <p class="text-xl text-gray-900 dark:text-white font-medium">${
                                    data.ip || "Unknown"
                                }</p>
                            </div>
                            <div class="space-y-3">
                                <h4 class="text-lg font-medium text-gray-500 dark:text-gray-400">Device</h4>
                                <p class="text-xl text-gray-900 dark:text-white font-medium">
                                    ${
                                        data.device?.is_iphone
                                            ? "iPhone"
                                            : data.device?.is_mac
                                            ? "Mac"
                                            : "Other"
                                    }
                                </p>
                                <p class="text-base text-gray-500 dark:text-gray-400">
                                    ${
                                        data.device?.user_agent_display ||
                                        data.device?.user_agent ||
                                        "Unknown"
                                    }
                                </p>
                            </div>
                        </div>
                        <div class="space-y-6">
                            <div class="space-y-3">
                                <h4 class="text-lg font-medium text-gray-500 dark:text-gray-400">Browser</h4>
                                <p class="text-xl text-gray-900 dark:text-white font-medium">
                                    ${data.device?.browser || "Unknown"}
                                </p>
                                ${
                                    Object.keys(data.device?.headers || {})
                                        .length
                                        ? `
                                    <p class="text-base text-gray-500 dark:text-gray-400">
                                        Headers: ${JSON.stringify(
                                            data.device?.headers,
                                            null,
                                            2
                                        )}
                                    </p>
                                `
                                        : ""
                                }
                            </div>
                            <div class="space-y-3">
                                <h4 class="text-lg font-medium text-gray-500 dark:text-gray-400">Private Relay Support</h4>
                                <p class="text-xl text-gray-900 dark:text-white font-medium">
                                    ${
                                        data.can_use_private_relay
                                            ? "Supported"
                                            : "Not Supported"
                                    }
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        })
        .catch((error) => {
            console.error("Error:", error);
            const results = document.getElementById("results");
            if (!results) return;

            results.innerHTML = `
                <div class="rounded-xl bg-apple-red/10 dark:bg-apple-red/20 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-10 h-10 text-apple-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-2xl font-semibold text-apple-red">Error checking Private Relay status</h3>
                            <p class="text-lg text-gray-600 dark:text-gray-300 mt-2">Please try again later</p>
                        </div>
                    </div>
                </div>
            `;
        });
});
