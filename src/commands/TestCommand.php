<?php

namespace BugHive\Commands;

use Exception;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    protected $signature = 'bughive:test {exception?}';

    protected $description = 'Generate a test exception and send it to bughive';

    public function handle()
    {
        try {
            /** @var bughive $bughive */
            $bughive = app('bughive');

            if (config('bughive.login_key')) {
                $this->info('✓ [bughive] Found login key');
            } else {
                $this->error('✗ [bughive] Could not find your login key, set this in your .env');
            }

            if (config('bughive.project_key')) {
                $this->info('✓ [bughive] Found project key');
            } else {
                $this->error('✗ [bughive] Could not find your project key, set this in your .env');
                $this->info('More information on setting your project key can be found in installation/usage in your project');
            }

            if (in_array(config('app.env'), config('bughive.environments'))) {
                $this->info('✓ [bughive] Correct environment found (' . config('app.env') . ')');
            } else {
                $this->error('✗ [bughive] Environment (' . config('app.env') . ') not allowed to send errors to bughive, set this in your config');
                $this->info('More information about environment configuration in bughive.php');
            }

            $response = $bughive->handle(
                $this->generateException()
            );

            if (isset($response->id)) {
                $this->info('✓ [bughive] Sent exception to bughive with ID: '.$response->id);
            } elseif (is_null($response)) {
                $this->info('✓ [bughive] Sent exception to bughive!');
            } else {
                $this->error('✗ [bughive] Failed to send exception to bughive');
            }
        } catch (\Exception $ex) {
            $this->error("✗ [bughive] {$ex->getMessage()}");
        }
    }

    public function generateException(): ?Exception
    {
        try {

            throw new Exception($this->argument('exception') ?? 'This is a test exception from the bughive console');
        } catch (Exception $ex) {

            return $ex;
        }
    }
}