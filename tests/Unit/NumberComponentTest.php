<?php

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

it('formats display numbers in the active UI locale', function () {
    app()->setLocale('de');
    expect(Blade::render('<x-ui.number :value="123456" />'))->toContain('123.456');
    app()->setLocale('en');
    expect(Blade::render('<x-ui.number :value="123456" />'))->toContain('123,456');
});

it('supports locale and decimal overrides and preserves zero', function () {
    app()->setLocale('en');
    expect(Blade::render('<x-ui.number value="123456.5" locale="de-DE" precision="2" />'))->toContain('123.456,50');
    expect(Blade::render('<x-ui.number :value="0" />'))->toContain('>0</span>');
    expect(Blade::render('<x-ui.number :value="null" empty="No value" />'))->toContain('No value');
});
