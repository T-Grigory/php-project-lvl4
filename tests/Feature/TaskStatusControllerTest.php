<?php

namespace Tests\Feature;

use App\Models\TaskStatus;
use App\Models\User;
use Tests\TestCase;

class TaskStatusControllerTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function testIndex()
    {
        $this->get(route('task_statuses.index'))
             ->assertStatus(200);
    }

    public function testCreate()
    {
        $this->actingAs($this->user)
             ->get(route('task_statuses.create'))
             ->assertStatus(200);
    }

    public function testStore()
    {
        $data = TaskStatus::factory()->make()->toArray();

        $this->actingAs($this->user)
             ->post(route('task_statuses.store', $data))
             ->assertRedirect(route('task_statuses.index'))
             ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('task_statuses', $data);
    }

    public function testEdit()
    {
        $taskStatus = TaskStatus::factory()->create();

        $this->actingAs($this->user)
             ->get(route('task_statuses.edit', $taskStatus))
             ->assertStatus(200);
    }

    public function testUpdate()
    {
        $taskStatus = TaskStatus::factory()->create();
        $data = TaskStatus::factory()->make()->toArray();

        $this->actingAs($this->user)
             ->patch(route('task_statuses.update', $taskStatus), $data)
             ->assertRedirect(route('task_statuses.index'))
             ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('task_statuses', $data);
    }

    public function testDestroy()
    {
        $taskStatus = TaskStatus::factory()->create();

        $this->actingAs($this->user)
             ->delete(route('task_statuses.destroy', $taskStatus))
             ->assertRedirect(route('task_statuses.index'))
             ->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('task_statuses', ['id' => $taskStatus->only('id')]);
    }
}
