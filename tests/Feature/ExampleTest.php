<?php



/**
 * A basic test example.
 */
test('basic test', function () {
    $response = $this->get('/en');

    $response->assertStatus(200);
});

/**
 * A basic test example.
 */
test('redirection test', function () {
    $response = $this->get('/');

    $response->assertStatus(301);
});
