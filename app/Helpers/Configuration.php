<?php

namespace App\Helpers;

use Illuminate\Support\Arr;

class Configuration
{
    protected string $path;

    protected array $config;

    public function __construct(string $path)
    {
        $this->path   = $path;
        $this->config = $this->readConfig();
    }

    public function isConfigured(): bool
    {
        return file_exists($this->configFilePath());
    }

    public function get(string $key, string $profile = 'default', string $default = ''): string
    {
        $this->config = $this->readConfig();
        if (empty($this->config)) {
            return $default;
        }

        if (!$this->teamExists($profile)) {
            return $default;
        }

        $profileConfig = $this->config[$profile];

        if (!isset($profileConfig[$key])) {
            return $default;
        }

        return $profileConfig[$key];
    }

    public function set(string $key, string $value, string $profile = 'default'): void
    {
        $config = $this->config;

        Arr::set($config, "{$profile}.{$key}", $value);

        file_put_contents($this->configFilePath(), json_encode($config, JSON_PRETTY_PRINT));

        $this->config = $config;
    }

    public function teamExists(string $profile): bool
    {
        return isset($this->config[$profile]);
    }

    protected function readConfig(): array
    {
        if (!$this->isConfigured()) {
            return [];
        }

        $configFile = file_get_contents($this->configFilePath());

        if (!$configFile) {
            return [];
        }

        return json_decode($configFile, true);
    }

    public function configFilePath(): string
    {
        if (!file_exists($this->path)) {
            mkdir($this->path);
        }

        return $this->path . 'config.json';
    }
}
