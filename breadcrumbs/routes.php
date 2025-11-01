<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('dashboard', function (BreadcrumbTrail $trail) {
    $trail->push(__('frontend.common.dashboard'), route('dashboard'));
});

Breadcrumbs::for('profile.edit', function (BreadcrumbTrail $trail) {
    $trail->push(__('frontend.settings.profile.breadcrumb'), route('profile.edit'));
});

Breadcrumbs::for('user-password.edit', function (BreadcrumbTrail $trail) {
    $trail->push(__('frontend.settings.password.breadcrumb'), route('user-password.edit'));
});

Breadcrumbs::for('appearance.edit', function (BreadcrumbTrail $trail) {
    $trail->push(__('frontend.settings.appearance.breadcrumb'), route('appearance.edit'));
});

Breadcrumbs::for('two-factor.show', function (BreadcrumbTrail $trail) {
    $trail->push(__('frontend.settings.two_factor.breadcrumb'), route('two-factor.show'));
});

Breadcrumbs::for('admin.dashboard', function (BreadcrumbTrail $trail) {
    $trail->push(__('frontend.admin.dashboard.breadcrumb'), route('admin.dashboard'));
});

Breadcrumbs::for('admin.settings.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push(__('frontend.admin.settings.breadcrumb'), route('admin.settings.index'));
});

Breadcrumbs::for('admin.settings.environment.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.settings.index');
    $trail->push(__('frontend.admin.environment.breadcrumb'), route('admin.settings.environment.index'));
});

Breadcrumbs::for('admin.users.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push(__('frontend.admin.users.breadcrumb'), route('admin.users.index'));
});

Breadcrumbs::for('admin.users.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.users.index');
    $trail->push(__('frontend.admin.users.breadcrumb_create'), route('admin.users.create'));
});

Breadcrumbs::for('admin.users.show', function (BreadcrumbTrail $trail, $user) {
    $trail->parent('admin.users.index');
    $trail->push($user->name, route('admin.users.show', $user));
});

Breadcrumbs::for('admin.users.edit', function (BreadcrumbTrail $trail, $user) {
    $trail->parent('admin.users.index');
    $trail->push(__('frontend.admin.users.breadcrumb_edit'), route('admin.users.edit', $user));
});

Breadcrumbs::for('admin.users.activities', function (BreadcrumbTrail $trail, $user) {
    $trail->parent('admin.users.show', $user);
    $trail->push(__('frontend.admin.users.breadcrumb_activity_log'), route('admin.users.activities', $user));
});
