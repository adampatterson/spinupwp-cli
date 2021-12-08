<?php

namespace App\Commands\Servers;

use App\Commands\BaseCommand;
use App\Commands\Concerns\SpecifyFields;

class GetCommand extends BaseCommand
{
    use SpecifyFields;

    protected $signature = 'servers:get
                            {server_id : The server to output}
                            {--format=}
                            {--profile=}
                            {--fields=}';

    protected $description = 'Get a server';

    protected function setup()
    {
        $this->largeOutput = true;
        $this->fieldsMap   = [
            'ID'            => 'id',
            'Name'          => 'name',
            'Provider Name' => 'provider_name',
            'IP Address'    => 'ip_address',
            'SSH Port'      => 'ssh_port',
            'Ubuntu'        => 'ubuntu_version',
            'Timezone'      => 'timezone',
            'Region'        => 'region',
            'Size'          => 'size',
            'Disk Space'    => [
                'property' => 'disk_space',
                'filter'   => fn ($value)   => $this->formatBytes($value['used']) . ' of ' . $this->formatBytes($value['total'], 0) . ' used',
            ],
            'Database Server' => [
                'property' => 'database',
                'filter'   => fn ($value)   => $value['server'],
            ],
            'Database Host' => [
                'property' => 'database',
                'filter'   => fn ($value)   => $value['host'],
            ],
            'Database Port' => [
                'property' => 'database',
                'filter'   => fn ($value)   => $value['port'],
            ],
            'SSH Public Key'    => 'ssh_publickey',
            'Git Public Key'    => 'git_publickey',
            'Connection Status' => [
                'property' => 'connection_status',
                'filter'   => fn ($value)   => ucfirst($value),
            ],
            'Reboot Required' => [
                'property' => 'reboot_required',
                'filter'   => fn ($value)   => $value ? 'Yes' : 'No',
            ],
            'Upgrade Required' => [
                'property' => 'upgrade_required',
                'filter'   => fn ($value)   => $value ? 'Yes' : 'No',
            ],
            'Install Notes' => 'install_notes',
            'Created At'    => 'created_at',
            'Status'        => [
                'property' => 'status',
                'filter'   => fn ($value)   => ucfirst($value),
            ],
        ];
    }

    public function action(): int
    {
        $serverId = $this->argument('server_id');
        $server   = $this->spinupwp->servers->get((int) $serverId);

        if ($this->option('fields')) {
            $this->saveFieldsFilter();
        }

        if ($this->displayFormat() === 'table') {
            $server = $this->specifyFields($server);
        }

        $this->format($server);

        return self::SUCCESS;
    }
}
