<?php

declare(strict_types=1);

namespace Infrastructure\Security\Console;

use Illuminate\Console\Command;

final class GenerateSecureIdKey extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'security:generate-key
                           {--show : Display the key instead of modifying files}
                           {--env= : The environment file to update (default: .env)}';

    /**
     * The console command description.
     */
    protected $description = 'Generate a secure encryption key for SecureIdService (URL ID encryption)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $key = 'base64:'.base64_encode(random_bytes(32));

        if ($this->option('show')) {
            $this->line('SECURE_ID_KEY='.$key);

            return self::SUCCESS;
        }

        $envPath = $this->option('env') ?: $this->laravel->environmentPath().'/'.$this->laravel->environmentFile();

        if (! file_exists($envPath)) {
            $this->error('Environment file not found: '.$envPath);

            return self::FAILURE;
        }

        $contents = file_get_contents($envPath);
        $keyPattern = '/^SECURE_ID_KEY=.*/m';

        if (preg_match($keyPattern, $contents)) {
            // Replace existing key
            $contents = preg_replace($keyPattern, 'SECURE_ID_KEY='.$key, $contents);
        } else {
            // Append after APP_KEY or at the end
            $appKeyPattern = '/^APP_KEY=.*/m';
            if (preg_match($appKeyPattern, $contents)) {
                $contents = preg_replace(
                    $appKeyPattern,
                    '$0'.PHP_EOL.('SECURE_ID_KEY='.$key),
                    $contents,
                    1, // limit to first occurrence
                );
            } else {
                $contents = rtrim($contents).PHP_EOL.PHP_EOL.('SECURE_ID_KEY='.$key).PHP_EOL;
            }
        }

        file_put_contents($envPath, $contents);

        $this->info('Secure ID encryption key [SECURE_ID_KEY] generated successfully.');
        $this->line('File: '.$envPath);

        return self::SUCCESS;
    }
}
