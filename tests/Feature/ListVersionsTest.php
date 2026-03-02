<?php



it('loads', function () {
    $response = $this->get(route('versions.index'));

    $response->assertStatus(200);
});
