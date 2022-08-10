<?php

use Ezzaze\Airlabs\Exceptions\AirlabsException;
use Ezzaze\Airlabs\Facades\Airlabs;
use Illuminate\Support\Facades\Config;

it('can throw invalid API key exception', function () {
    Config::set('airlabs.version', 'v9');
    Config::set('airlabs.api_key', 'ea355cf5-7ed9-4d57-9e75-acf544ee2deee');
    Config::set('airlabs.cache.enabled', true);
    $result = Airlabs::verifyPeer(false)->ping();
})->throws(AirlabsException::class, 'Provided API Key is invalid.');

it('can throw not supported method exception', function () {
    Config::set('airlabs.version', 'v9');
    Config::set('airlabs.api_key', 'ea355cf5-7ed9-4d57-9e75-acf544ee2dee');
    Config::set('airlabs.cache.enabled', true);
    $result = Airlabs::verifyPeer(false)->unkownMethod();
})->throws(AirlabsException::class, 'Provided method is not supported.');

it('can throw wrong parameters exception', function () {
    Config::set('airlabs.version', 'v9');
    Config::set('airlabs.api_key', 'ea355cf5-7ed9-4d57-9e75-acf544ee2dee');
    Config::set('airlabs.cache.enabled', true);
    $result = Airlabs::verifyPeer(false)->schedules();
})->throws(AirlabsException::class, 'Some parameters are wrong.');
