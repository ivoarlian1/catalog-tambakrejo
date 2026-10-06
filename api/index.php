<?php
// Set required environment variables for Vercel's read-only filesystem
$_ENV['IS_VERCEL'] = 'true';
$_ENV['APP_CONFIG_CACHE'] = '/tmp/config.php';
$_ENV['APP_EVENTS_CACHE'] = '/tmp/events.php';
$_ENV['APP_PACKAGES_CACHE'] = '/tmp/packages.php';
$_ENV['APP_ROUTES_CACHE'] = '/tmp/routes.php';
$_ENV['APP_SERVICES_CACHE'] = '/tmp/services.php';
$_ENV['VIEW_COMPILED_PATH'] = '/tmp';

// Set safe fallback drivers for Serverless
if (empty($_ENV['SESSION_DRIVER'])) $_ENV['SESSION_DRIVER'] = 'cookie';
if (empty($_ENV['LOG_CHANNEL'])) $_ENV['LOG_CHANNEL'] = 'stderr';
if (empty($_ENV['CACHE_STORE'])) $_ENV['CACHE_STORE'] = 'array';

require __DIR__ . '/../public/index.php';
