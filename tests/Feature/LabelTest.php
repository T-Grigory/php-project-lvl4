<?php

namespace Tests\Feature;

use App\Models\Label;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LabelTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function testIndex()
    {
        $this->get(route('labels.index'))
             ->assertStatus(200);
    }

    public function testCreate()
    {
        $this->actingAs($this->user)
             ->get(route('labels.create'))
             ->assertStatus(200);
    }

    public function testStore()
    {
        $data = Label::factory()->make()->toArray();

        $this->actingAs($this->user)
             ->post(route('labels.store', $data))
             ->assertRedirect(route('labels.index'))
             ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('labels', $data);
    }

    public function testEdit()
    {
        $label = Label::factory()->create();

        $this->actingAs($this->user)
             ->get(route('labels.edit', $label))
             ->assertStatus(200);
    }

    public function testUpdate()
    {
        $label = Label::factory()->create();
        $data = Label::factory()->make()->toArray();

        $this->actingAs($this->user)
             ->patch(route('labels.update', $label), $data)
             ->assertRedirect(route('labels.index'))
             ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('labels', $data);
    }

    public function testDestroy()
    {
        $label = Label::factory()->create();

        $this->actingAs($this->user)
             ->delete(route('labels.destroy', $label))
             ->assertRedirect(route('home.index'))
             ->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('labels', ['id' => $label->only('id')]);
    }
}
