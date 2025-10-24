<?php

use App\Http\Middleware\CheckFeatureEnabled;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

test('middleware allows request when feature is enabled', function () {
    config(['user.registration_enabled' => true]);

    $request = Request::create('/test', 'GET');
    $middleware = new CheckFeatureEnabled;

    $response = $middleware->handle($request, function ($req) {
        return new Response('Success', 200);
    }, 'registration');

    expect($response->getStatusCode())->toBe(200);
    expect($response->getContent())->toBe('Success');
});

test('middleware blocks request when feature is disabled', function () {
    config(['user.registration_enabled' => false]);

    $request = Request::create('/test', 'GET');
    $middleware = new CheckFeatureEnabled;

    expect(fn () => $middleware->handle($request, function ($req) {
        return new Response('Success', 200);
    }, 'registration'))
        ->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);
});

test('middleware works with account deletion feature', function () {
    config(['user.account_deletion_enabled' => true]);

    $request = Request::create('/test', 'DELETE');
    $middleware = new CheckFeatureEnabled;

    $response = $middleware->handle($request, function ($req) {
        return new Response('Success', 200);
    }, 'account-deletion');

    expect($response->getStatusCode())->toBe(200);
});

test('middleware blocks account deletion when disabled', function () {
    config(['user.account_deletion_enabled' => false]);

    $request = Request::create('/test', 'DELETE');
    $middleware = new CheckFeatureEnabled;

    expect(fn () => $middleware->handle($request, function ($req) {
        return new Response('Success', 200);
    }, 'account-deletion'))
        ->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);
});

test('middleware ignores unknown features', function () {
    $request = Request::create('/test', 'GET');
    $middleware = new CheckFeatureEnabled;

    $response = $middleware->handle($request, function ($req) {
        return new Response('Success', 200);
    }, 'unknown-feature');

    expect($response->getStatusCode())->toBe(200);
});
