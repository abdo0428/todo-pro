<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_landing_page_is_accessible(): void
    {
        $response = $this->get('/');

        $response->assertOk();
    }
}
