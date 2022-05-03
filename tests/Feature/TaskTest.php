<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use Database\Seeders\TaskStatusSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            TaskStatusSeeder::class,
            UserSeeder::class,
        ]);

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
             ->assertSee('Создать задачу')
             ->assertStatus(200);
    }

    public function testStore()
    {
        $data = Task::factory()->make()->attributesToArray();

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
             ->assertSee('Просмотр задачи')
             ->assertStatus(200);
    }

    public function testEdit()
    {
        $task = Task::factory()
             ->for($this->user, 'createdBy')
             ->create();

        $this->actingAs($this->user)
             ->get(route('tasks.edit', $task))
             ->assertSee('Изменить задачу')
             ->assertStatus(200);
    }

    public function testUpdate()
    {
        $data = Task::factory()->make()->attributesToArray();
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

        $this->assertDatabaseMissing('tasks', $task->only('id'));
    }
}
