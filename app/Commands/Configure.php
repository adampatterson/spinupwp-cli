<?php

namespace App\Commands;

class Configure extends BaseCommand
{
    protected $signature = 'configure {--profile=}';

    protected $description = 'Configure SpinupWP CLI';

    public function handle()
    {
        $profile = $this->option('profile') ?? 'default';

        if (!empty($this->config->get('api_token', $profile))) {
            $this->alert("A profile named {$profile} is already configured");
            $response = $this->ask('Do you want to overwrite the existing configuration? (y/n)', 'y');
            while (!in_array($response, ['y', 'n'])) {
                $this->error("Please type 'y' or 'n'");
                $response = $this->ask('Do you want to overwrite the existing configuration? (y/n)', 'y');
            }
            if ($response === 'n') {
                return 0;
            }
        }

        $apiKey = null;

        while (!$apiKey) {
            $apiKey = $this->ask('SpinupWP API token');
        }

        $defaultFormat = null;

        while (!in_array($defaultFormat, config('app.output_formats'))) {
            $defaultFormat = $this->ask('Default output format (json/table)', null);
        }

        $this->config->saveConfig($apiKey, $defaultFormat, $profile);
        $this->info('SpinupWP CLI configured successfully');
        return 0;
    }
}
