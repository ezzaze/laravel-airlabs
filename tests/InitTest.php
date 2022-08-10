<?php

use Ezzaze\Airlabs\Facades\Airlabs;
use Illuminate\Support\Facades\Config;

it('can ping remote api', function () {
    Config::set('airlabs.version', 'v9');
    Config::set('airlabs.api_key', '');
    Config::set('airlabs.cache.enabled', true);
    $result = Airlabs::verifyPeer(false)->ping();
    expect($result)->toBeTrue();
});
