<?php

test('example', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

test('user can login with valid credentials', function () {
    
});