<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class Setup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:setup
        {--force : Force setup even if files exist}
        {--with-demo-data : Seed the database with demo data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup the application with Docker environment';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting application setup...');

        // Step 1: Build and start Docker containers
        $this->dockerComposeUpBuild();

        // Step 2: Environment File
        $this->setupEnvironmentFile();

        // Step 3: Install dependencies
        $this->installDependencies();

        // Step 4: Set up database
        $this->setupDatabase();

        // Step 5: Set up frontend
        $this->setupFrontend();

        // Step 6: Cache configuration
        $this->optimizeApplication();

        $this->info('🎉 Application setup completed successfully!');
        $this->info('You can now access your application at: http://localhost:8000');
    }

    private function dockerComposeUpBuild()
    {
        $this->info('Building and starting Docker containers...');
        $this->dockerCompose(['docker', 'compose', 'up', '-d', '--build'], false);
        sleep(10); // Wait for containers to be ready
    }

    private function setupEnvironmentFile()
    {
        $this->info('Setting up environment file...');

        if (!file_exists(base_path('.env')) || $this->option('force')) {
            copy(base_path('.env.example'), base_path('.env'));
            $this->dockerCompose(['php', 'artisan', 'key:generate']);
            $this->info('Environment file created and application key set.');
        }
    }

    private function installDependencies()
    {
        $this->info('Installing Composer dependencies...');
        $this->dockerCompose(['composer', 'install']);
    }

    private function setupDatabase()
    {
        $this->info('Setting up database...');

        // Run migrations
        $this->dockerCompose(['php', 'artisan', 'migrate:fresh', '--seed']);;
    }

    private function setupFrontend()
    {
        $this->info('Setting up frontend assets...');
        $this->dockerCompose(['npm', 'install']);
        $this->dockerCompose(['npm', 'run', 'build']);
    }

    private function optimizeApplication()
    {
        $this->info('Optimizing application...');
        $this->dockerCompose(['php', 'artisan', 'optimize']);
    }

    private function dockerCompose(array $command, $inDocker = true)
    {
        $process = new Process(
            array_merge(($inDocker ? ['docker', 'compose', 'exec', 'app'] : []), $command),
            base_path(),
            null,
            null,
            300
        );

        $process->setTimeout(null); // No timeout
        $process->setTty(true);    // Enable TTY mode if available

        try {
            $process->mustRun(function ($type, $buffer) {
                // Process::ERR is for stderr, Process::OUT is for stdout
                $this->output->write($buffer);
            });
        } catch (ProcessFailedException $exception) {
            throw new \RuntimeException('Docker Compose command failed: ' . $exception->getMessage());
        }


//        $process = new Process(
//            array_merge($inDocker ? [] $command),
//            base_path()
//        );
//
//        $process->run(function ($type, $buffer) {
//            $this->output->write($buffer);
//        });
//
//        if (!$process->isSuccessful()) {
//            throw new \RuntimeException('Docker Compose command failed');
//        }
    }
}
