<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('dashboard', function (BreadcrumbTrail $trail) {
    $trail->push(__('Dashboard'), route('dashboard'));
});

Breadcrumbs::for('profile.edit', function (BreadcrumbTrail $trail) {
    $trail->push(__('Profile settings'), route('profile.edit'));
});

Breadcrumbs::for('password.edit', function (BreadcrumbTrail $trail) {
    $trail->push(__('Password settings'), route('password.edit'));
});

Breadcrumbs::for('appearance.edit', function (BreadcrumbTrail $trail) {
    $trail->push(__('Appearance settings'), route('appearance.edit'));
});

Breadcrumbs::for('two-factor.show', function (BreadcrumbTrail $trail) {
    $trail->push(__('Two-Factor Authentication'), route('two-factor.show'));
});
