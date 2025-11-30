<?php

use Orchestra\Testbench\TestCase;
use LaravelReady\ModelSupport\ServiceProvider;

uses(TestCase::class)->in(__DIR__);

// Set up the package service provider for testing
uses()->beforeEach(function () {
    //
})->in(__DIR__);

function getPackageProviders($app)
{
    return [
        ServiceProvider::class,
    ];
}
