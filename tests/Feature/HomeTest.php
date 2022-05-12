<?php

namespace Tests\Feature;

use Tests\TestCase;

class HomeTest extends TestCase
{
    public function testIndex()
    {
        $this->get(route('home.index'))
             ->assertStatus(200);
    }
}
