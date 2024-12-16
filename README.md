# iCloud Private Relay Check

A web application to check if your connection is using iCloud Private Relay.

## Features

- Detects if your connection is using iCloud Private Relay
- Shows your current IP address
- Displays device and browser information
- Dark mode support
- Powered by Laravel Octane with Swoole for high performance

## Requirements

- PHP 8.2+
- Node.js 20+
- Podman or Docker
- Composer

## Local Development

1. Clone the repository

```bash
git clone https://github.com/yourusername/icloud-private-relay-check.git
cd icloud-private-relay-check
```

2. Install dependencies

```bash
composer install
npm install
```

3. Set up environment

```bash
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
```

4. Build assets

```bash
npm run build
```

5. Start the server

```bash
php artisan serve
```

## Production Deployment with Podman

1. Build the container

```bash
podman build -t icloud-relay-check .
```

2. Run the container

```bash
podman run -d \
  --name laravel-app \
  -p 8000:8000 \
  -v ./storage/logs:/var/www/storage/logs \
  -v ./storage/app:/var/www/storage/app \
  -v ./database:/var/www/database \
  --env-file .env.production \
  icloud-relay-check
```

### Setting Up the Scheduler

The Laravel scheduler can be run from the host system using either cron or systemd.

#### Using Cron

Add to your host system's crontab:

```bash
* * * * * podman exec laravel-app php artisan schedule:run >> /var/log/laravel-scheduler.log 2>&1
```

#### Using Systemd

1. Create the service file:

```ini
# /etc/systemd/system/laravel-scheduler.service
[Unit]
Description=Run Laravel Scheduler
Requires=podman.service
After=podman.service

[Service]
Type=oneshot
ExecStart=/usr/bin/podman exec laravel-app php artisan schedule:run

[Install]
WantedBy=multi-user.target
```

2. Create the timer:

```ini
# /etc/systemd/system/laravel-scheduler.timer
[Unit]
Description=Run Laravel Scheduler Every Minute

[Timer]
OnBootSec=1min
OnUnitActiveSec=1min

[Install]
WantedBy=timers.target
```

3. Enable and start the timer:

```bash
sudo systemctl enable laravel-scheduler.timer
sudo systemctl start laravel-scheduler.timer
```

## License

MIT License
