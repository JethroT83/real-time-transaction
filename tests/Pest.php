<?php

use Illuminate\Foundation\Testing\TestCase;
use Tests\TestCase as BaseTestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
*/

uses(
    Tests\TestCase::class,
// Illuminate\Foundation\Testing\RefreshDatabase::class,
)->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
*/

expect()->extend('toBeWithinRange', function (int $min, int $max) {
    return $this->toBeGreaterThanOrEqual($min)->toBeLessThanOrEqual($max);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
*/

function createTransaction(array $attributes = [])
{
    return \App\Models\Transaction::factory()->create($attributes);
}
