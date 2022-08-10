<?php

use Ezzaze\Airlabs\Facades\Airlabs;
use Illuminate\Support\Facades\Config;

it('can ping remote api', function () {
    Config::set('airlabs.version', 'v9');
    Config::set('airlabs.api_key', 'ea355cf5-7ed9-4d57-9e75-acf544ee2dee');
    Config::set('airlabs.cache.enabled', true);
    $result = Airlabs::verifyPeer(false)->ping();
    expect($result)->toBeTrue();
});
