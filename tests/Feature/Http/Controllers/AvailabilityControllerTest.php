<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AvailabilityControllerTest extends TestCase
{
    //use RefreshDatabase;// need model and migration first
    use WithFaker;

    protected string $appVersion;

    protected function setUp(): void
    {
        parent::setUp();

        $this->appVersion = $this->app->version();
    }

    public function test_example(): void
    {
        $request = $this->get('/');

        $request->assertStatus(200);

        static::assertEquals($this->appVersion, $request->content());
    }
}
