<?php

use GuzzleHttp\Psr7\Response;

beforeEach(function () {
    setTestConfigFile();

    $this->clientMock->shouldReceive('request')->with('GET', 'servers/1', [])->andReturn(
        new Response(200, [], json_encode(['data' => ['id' => 1, 'name' => 'hellfish-media']]))
    );

    $this->clientMock->shouldReceive('request')->with('POST', 'servers/1/reboot', [])->andReturn(
        new Response(200, [], json_encode(['event_id' => '100']))
    );
});

afterEach(function () {
    deleteTestConfigFile();
});

test('reboot a server', function () {
    $this->artisan('servers:reboot 1')->expectsOutput('Server reboot in progress. Event ID: 100');
});
