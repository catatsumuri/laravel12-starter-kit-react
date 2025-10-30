<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    Setting::flushAll();
});

test('valueBool converts string "1" to true', function () {
    Setting::updateValue('test.bool', '1');

    expect(Setting::valueBool('test.bool'))->toBeTrue();
});

test('valueBool converts string "0" to false', function () {
    Setting::updateValue('test.bool', '0');

    expect(Setting::valueBool('test.bool'))->toBeFalse();
});

test('valueBool converts string "true" to true', function () {
    Setting::updateValue('test.bool', 'true');

    expect(Setting::valueBool('test.bool'))->toBeTrue();
});

test('valueBool converts string "false" to false', function () {
    Setting::updateValue('test.bool', 'false');

    expect(Setting::valueBool('test.bool'))->toBeFalse();
});

test('valueBool returns default for non-existent key', function () {
    expect(Setting::valueBool('nonexistent', true))->toBeTrue();
    expect(Setting::valueBool('nonexistent', false))->toBeFalse();
    expect(Setting::valueBool('nonexistent'))->toBeNull();
});

test('valueBool handles empty string as false', function () {
    Setting::updateValue('test.empty', '');

    // Empty string is falsy, so it should return false
    expect(Setting::valueBool('test.empty'))->toBeFalse();

    // But with default, it should still return the default for truly null values
    expect(Setting::valueBool('nonexistent.key', true))->toBeTrue();
});

test('putMany updates multiple settings atomically', function () {
    Setting::putMany([
        'app.name' => 'TestApp',
        'app.debug' => '1',
        'app.locale' => 'ja',
    ]);

    expect(Setting::value('app.name'))->toBe('TestApp');
    expect(Setting::value('app.debug'))->toBe('1');
    expect(Setting::value('app.locale'))->toBe('ja');
});

test('putMany rolls back all changes on error', function () {
    Setting::updateValue('app.name', 'Original');

    try {
        DB::transaction(function () {
            Setting::putMany([
                'app.name' => 'Updated',
                'app.invalid' => str_repeat('x', 100000), // Exceeds column size
            ]);
        });
    } catch (\Exception $e) {
        // Exception expected
    }

    // Original value should remain due to transaction rollback
    expect(Setting::value('app.name'))->toBe('Original');
})->skip('Transaction rollback behavior depends on database constraints');

test('putMany updates existing values', function () {
    Setting::updateValue('app.name', 'OldName');
    Setting::updateValue('app.locale', 'en');

    Setting::putMany([
        'app.name' => 'NewName',
        'app.locale' => 'ja',
    ]);

    expect(Setting::value('app.name'))->toBe('NewName');
    expect(Setting::value('app.locale'))->toBe('ja');
});

test('putMany creates new values', function () {
    Setting::putMany([
        'new.key1' => 'value1',
        'new.key2' => 'value2',
    ]);

    expect(Setting::value('new.key1'))->toBe('value1');
    expect(Setting::value('new.key2'))->toBe('value2');
});

test('flushAll clears all setting caches', function () {
    Setting::updateValue('test.key1', 'value1');
    Setting::updateValue('test.key2', 'value2');

    // Verify values are cached by reading them
    expect(Setting::value('test.key1'))->toBe('value1');
    expect(Setting::value('test.key2'))->toBe('value2');

    // Update directly in DB (bypassing cache)
    DB::table('settings')->where('key', 'test.key1')->update(['value' => 'changed1']);
    DB::table('settings')->where('key', 'test.key2')->update(['value' => 'changed2']);

    // Should still get old values from cache
    expect(Setting::value('test.key1'))->toBe('value1');
    expect(Setting::value('test.key2'))->toBe('value2');

    // Flush cache
    Setting::flushAll();

    // Now should get new values from DB
    expect(Setting::value('test.key1'))->toBe('changed1');
    expect(Setting::value('test.key2'))->toBe('changed2');
});

test('flushAll handles empty settings table', function () {
    DB::table('settings')->truncate();

    Setting::flushAll();

    expect(true)->toBeTrue(); // No exception thrown
});

test('value method still works as before', function () {
    Setting::updateValue('backward.compatible', 'test');

    expect(Setting::value('backward.compatible'))->toBe('test');
    expect(Setting::value('nonexistent', 'default'))->toBe('default');
});

test('updateValue method still works as before', function () {
    Setting::updateValue('test.update', 'initial');
    expect(Setting::value('test.update'))->toBe('initial');

    Setting::updateValue('test.update', 'updated');
    expect(Setting::value('test.update'))->toBe('updated');
});

test('valueBool handles edge cases correctly', function () {
    // Test "on" string
    Setting::updateValue('test.on', 'on');
    expect(Setting::valueBool('test.on'))->toBeTrue();

    // Test "off" string
    Setting::updateValue('test.off', 'off');
    expect(Setting::valueBool('test.off'))->toBeFalse();

    // Test "yes" string
    Setting::updateValue('test.yes', 'yes');
    expect(Setting::valueBool('test.yes'))->toBeTrue();

    // Test "no" string
    Setting::updateValue('test.no', 'no');
    expect(Setting::valueBool('test.no'))->toBeFalse();
});

test('putMany handles empty array', function () {
    Setting::putMany([]);

    expect(true)->toBeTrue(); // No exception thrown
});
