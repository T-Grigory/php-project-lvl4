<?php

namespace Tests\Feature;

use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    public function testIndex()
    {
        $this->get(route('home.index'))
             ->assertStatus(200);
    }
}
