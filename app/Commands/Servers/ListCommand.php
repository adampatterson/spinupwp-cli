<?php

namespace App\Commands\Servers;

use App\Commands\BaseCommand;

class ListCommand extends BaseCommand
{
    protected $signature = 'servers:list {--format=} {--profile=}';

    protected $description = 'Retrieves a list of servers';

    protected function action()
    {
        $servers = collect($this->spinupwp->servers->list());

        if ($servers->isEmpty()) {
            $this->warn('No servers found.');
            return $servers;
        }

        if ($this->displayFormat() === 'json') {
            return $servers;
        }

        return $servers->map(fn ($server) => [
            'ID'         => $server->id,
            'Name'       => $server->name,
            'IP Address' => $server->ip_address,
            'Ubuntu'     => $server->ubuntu_version,
            'Database'   => $server->database['server'],
        ]);
    }
}
