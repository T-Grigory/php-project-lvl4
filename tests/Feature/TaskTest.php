<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        User::factory(10)->create();
        TaskStatus::factory()->count(4)->create();
        $this->user = User::factory()->create();
    }
    public function testIndex()
    {
        $this->get(route('tasks.index'))
             ->assertStatus(200);
    }

    public function testCreate()
    {
        $this->actingAs($this->user)
             ->get(route('tasks.create'))
             ->assertStatus(200);
    }

    public function testStore()
    {
        $data = Task::factory()->make()->toArray();

        $this->actingAs($this->user)
             ->post(route('tasks.store', $data))
             ->assertRedirect(route('tasks.index'))
             ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('tasks', $data);
    }

    public function testShow()
    {
        $task = Task::factory()
             ->for($this->user, 'createdBy')
             ->create();

        $this->actingAs($this->user)
             ->get(route('tasks.show', $task))
             ->assertStatus(200);
    }

    public function testEdit()
    {
        $task = Task::factory()
             ->for($this->user, 'createdBy')
             ->create();

        $this->actingAs($this->user)
             ->get(route('tasks.edit', $task))
             ->assertStatus(200);
    }

    public function testUpdate()
    {
        $data = Task::factory()->make()->toArray();
        $task = Task::factory()
             ->for($this->user, 'createdBy')
             ->create();

        $this->actingAs($this->user)
             ->patch(route('tasks.update', $task), $data)
             ->assertRedirect(route('tasks.index'))
             ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('tasks', $data);
    }

    public function testDestroy()
    {
        $task = Task::factory()
             ->for($this->user, 'createdBy')
             ->create();

        $this->actingAs($this->user)
             ->delete(route('tasks.destroy', $task))
             ->assertRedirect(route('tasks.index'))
             ->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('tasks', ['id' => $task->only('id')]);
    }
}
