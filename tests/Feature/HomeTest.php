<?php

namespace Tests\Feature;

use Tests\TestCase;

class HomeTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIndex()
    {
        $this->get(route('home.index'))
             ->assertStatus(200);
    }
}
