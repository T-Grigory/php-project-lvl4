<?php

namespace Tests\Feature;

use App\Models\Task;
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

    public function testCreateUnauthorizedUser()
    {
        $this->get(route('tasks.create'))
            ->assertSee('This action is unauthorized.')
            ->assertStatus(403);
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

    public function testStoreUnauthorizedUser()
    {
        $data = Task::factory()->make()->attributesToArray();

        $this->post(route('tasks.store', $data))
            ->assertSee('This action is unauthorized.')
            ->assertStatus(403);

        $this->assertDatabaseMissing('tasks', $data);
    }

    public function testStoreEmptyData()
    {
        $data = ['name' => ''];

        $this->actingAs($this->user)
            ->post(route('tasks.store', $data))
            ->assertSessionHasErrors([
                'name' => 'Это обязательное поле',
                'status_id' => 'Это обязательное поле'
            ]);

        $this->assertDatabaseMissing('tasks', $data);
    }

    public function testStoreRequiredField()
    {
        $data = ['name' => 'задача'];

        $this->actingAs($this->user)
            ->post(route('tasks.store', $data))
            ->assertSessionHasErrors([
                'status_id' => 'Это обязательное поле'
            ]);

        $this->assertDatabaseMissing('tasks', $data);
    }

    public function testStoreUniqueFieldName()
    {
        $data = ['name' => 'новый', 'status_id' => 1];
        Task::factory()
            ->for($this->user, 'createdBy')
            ->create($data);

        $task = new Task();
        $this->actingAs($this->user)
             ->from(route('tasks.create', $task))
             ->post(route('tasks.store', $task), $data)
             ->assertStatus(302)
             ->assertSessionHasErrors(['name' => ':Entity с таким именем уже существует'])
             ->assertRedirect(route('tasks.create', $task));
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

    public function testShowUnauthorizedUser()
    {
        $task = Task::factory()
             ->for($this->user, 'createdBy')
             ->create();

        $this->get(route('tasks.show', $task))
             ->assertSee('This action is unauthorized.')
             ->assertStatus(403);
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

    public function testEditUnauthorizedUser()
    {
        $task = Task::factory()
            ->for($this->user, 'createdBy')
            ->create();

        $this->get(route('tasks.edit', $task))
             ->assertSee('This action is unauthorized.')
             ->assertStatus(403);
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

    public function testUpdateUnauthorizedUser()
    {
        $data = Task::factory()->make()->attributesToArray();
        $task = Task::factory()
             ->for($this->user, 'createdBy')
             ->create();

        $this->patch(route('tasks.update', $task), $data)
             ->assertSee('This action is unauthorized.')
             ->assertStatus(403);
    }

    public function testUpdateEmptyData()
    {
        $data = ['name' => ''];
        $task = Task::factory()
             ->for($this->user, 'createdBy')
             ->create();

        $this->actingAs($this->user)
             ->from(route('tasks.edit', $task))
             ->patch(route('tasks.update', $task), $data)
             ->assertRedirect(route('tasks.edit', $task))
             ->assertSessionHasErrors([
                 'name' => 'Это обязательное поле',
                 'status_id' => 'Это обязательное поле'
             ]);

        $this->assertDatabaseMissing('tasks', $data);
    }

    public function testUpdateRequiredField()
    {
        $data = ['name' => 'новая задача'];
        $task = Task::factory()
             ->for($this->user, 'createdBy')
             ->create();

        $this->actingAs($this->user)
             ->from(route('tasks.edit', $task))
             ->patch(route('tasks.update', $task), $data)
             ->assertRedirect(route('tasks.edit', $task))
             ->assertSessionHasErrors([
                'status_id' => 'Это обязательное поле',
             ]);

        $this->assertDatabaseMissing('tasks', $data);
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

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function testDestroyUnauthorizedUser()
    {
        $task = Task::factory()
             ->for($this->user, 'createdBy')
             ->create();

        $this->delete(route('tasks.destroy', $task))
             ->assertSee('This action is unauthorized.')
             ->assertStatus(403);

        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }

    public function testDestroyUserNotCreateTheTask()
    {
        $task = Task::factory()
             ->for($this->user, 'createdBy')
             ->create();
        $user = User::factory()->create();

        $this->actingAs($user)
             ->delete(route('tasks.destroy', $task))
             ->assertSee('This action is unauthorized.')
             ->assertStatus(403);

        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }
}
