<?php

function run($command, $inDocker = true) {
    $fullCommand = $inDocker
        ? "docker compose exec app " . implode(' ', $command)
        : implode(' ', $command);

    echo "\n>> $fullCommand\n";

    passthru($fullCommand, $exitCode);
    if ($exitCode !== 0) {
        echo "Command failed with exit code $exitCode\n";
        exit($exitCode);
    }
}

function basePath($path = '') {
    return __DIR__ . ($path ? '/' . ltrim($path, '/') : '');
}

function info($text) {
    echo "\n\033[32m$text\033[0m\n";
}

function optionEnabled($optionName, $argv) {
    return in_array("--$optionName", $argv);
}

// Parse options
$argvOptions = $argv ?? [];

$force = optionEnabled('force', $argvOptions);
$withDemoData = optionEnabled('with-demo-data', $argvOptions);

info('Starting application setup...');

// Step 1: .env setup
info('Setting up environment file...');
if (!file_exists(basePath('.env')) || $force) {
    copy(basePath('.env.example'), basePath('.env'));
}

// Step 2: Docker build & up
info('Building and starting Docker containers...');
run(['docker', 'compose', 'up', '-d', '--build'], false);
sleep(10); // Wait for containers to be ready

// Step 3: Composer install
info('Installing Composer dependencies...');
run(['composer', 'install']);

// Step 4: generate key
run(['php', 'artisan', 'key:generate']);
info('.env file created and app key generated.');


// Step 5: Migrate database
info('Setting up database...');
$migrateCommand = ['php', 'artisan', 'migrate:fresh', '--seed'];
run($migrateCommand);

// Step 6: Optimize
info('Optimizing application...');
run(['php', 'artisan', 'optimize']);

info('🎉 Application setup completed!');
info('Visit your app at http://localhost:8000');

// Step 7: Frontend setup
info('Setting up frontend...');
run(['npm', 'install']);
run(['npm', 'run', 'build']);
