<?php

it('returns a successful response', function () {
    config(['ui.disable_welcome_page' => false]);

    $response = $this->get('/');

    $response->assertStatus(200);
});
