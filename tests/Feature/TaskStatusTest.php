<?php

namespace Tests\Feature;

use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskStatusTest extends TestCase
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
        $this->get(route('task_statuses.index'))
             ->assertStatus(200);
    }

    public function testCreate()
    {
        $this->actingAs($this->user)
             ->get(route('task_statuses.create'))
             ->assertSee('Создать статус')
             ->assertStatus(200);
    }

    public function testCreateUnauthorizedUser()
    {
        $this->get(route('task_statuses.create'))
             ->assertSee('This action is unauthorized.')
             ->assertStatus(403);
    }
    public function testStore()
    {
        $data = TaskStatus::factory()->make()->only('name');

        $this->actingAs($this->user)
             ->post(route('task_statuses.store', $data))
             ->assertRedirect(route('task_statuses.index'))
             ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('task_statuses', $data);
    }

    public function testStoreUnauthorizedUser()
    {
        $data = TaskStatus::factory()->make()->only('name');

        $this->post(route('task_statuses.store', $data))
             ->assertSee('This action is unauthorized.')
             ->assertStatus(403);

        $this->assertDatabaseMissing('task_statuses', $data);
    }

    public function testStoreEmptyData()
    {
        $data = ['name' => ''];

        $this->actingAs($this->user)
             ->post(route('task_statuses.store', $data))
             ->assertSessionHasErrors([
                 'name' => 'Это обязательное поле'
             ]);

        $this->assertDatabaseMissing('task_statuses', $data);
    }

    public function testStoreUniqueFieldName()
    {
        $data = ['name' => 'новый'];
        TaskStatus::factory()->create($data);

        $this->actingAs($this->user)
             ->from('task_statuses/create')
             ->post(route('task_statuses.store', $data))
             ->assertStatus(302)
             ->assertSessionHasErrors(['name' => ':Entity с таким именем уже существует'])
             ->assertRedirect(route('task_statuses.create'));
    }

    public function testEdit()
    {
        $taskStatus = TaskStatus::factory()->create();

        $this->actingAs($this->user)
             ->get(route('task_statuses.edit', $taskStatus))
             ->assertSee('Изменение статуса')
             ->assertStatus(200);
    }

    public function testEditUnauthorizedUser()
    {
        $taskStatus = TaskStatus::factory()->create();

        $this->get(route('task_statuses.edit', $taskStatus))
             ->assertSee('This action is unauthorized.')
             ->assertStatus(403);
    }

    public function testUpdate()
    {
        $taskStatus = TaskStatus::factory()->create();
        $data = TaskStatus::factory()->make()->only('name');

        $this->actingAs($this->user)
             ->patch(route('task_statuses.update', $taskStatus), $data)
             ->assertRedirect(route('task_statuses.index'))
             ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('task_statuses', $data);
    }

    public function testUpdateUnauthorizedUser()
    {
        $taskStatus = TaskStatus::factory()->create();
        $data = $taskStatus::factory()->make()->only('name');

        $this->patch(route('task_statuses.update', $taskStatus), $data)
             ->assertSee('This action is unauthorized.')
             ->assertStatus(403);

        $this->assertDatabaseMissing('task_statuses', $data);
    }

    public function testUpdateEmptyData()
    {
        $taskStatus = TaskStatus::factory()->create();
        $data = ['name' => ''];

        $this->actingAs($this->user)
             ->from(route('task_statuses.edit', $taskStatus))
             ->patch(route('task_statuses.update', $taskStatus), $data)
             ->assertStatus(302)
             ->assertSessionHasErrors([
                'name' => 'Это обязательное поле'
             ])
             ->assertRedirect(route('task_statuses.edit', $taskStatus));
    }

    public function testUpdateOnUniqueFieldName()
    {
        $data = ['name' => 'новый'];
        TaskStatus::factory()->create($data);
        $taskStatus = TaskStatus::factory()->create();

        $this->actingAs($this->user)
             ->from(route('task_statuses.edit', $taskStatus))
             ->patch(route('task_statuses.update', $taskStatus), $data)
             ->assertStatus(302)
             ->assertSessionHasErrors([
                'name' => ':Entity с таким именем уже существует'
             ])
             ->assertRedirect(route('task_statuses.edit', $taskStatus));
    }

    public function testDestroy()
    {
        $taskStatus = TaskStatus::factory()->create();

        $this->actingAs($this->user)
             ->delete(route('task_statuses.destroy', $taskStatus))
             ->assertRedirect(route('task_statuses.index'))
             ->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('task_statuses', $taskStatus->only('id'));
    }

    public function testDestroyUnauthorizedUser()
    {
        $taskStatus = TaskStatus::factory()->create();

        $this->delete(route('task_statuses.destroy', $taskStatus))
             ->assertSee('This action is unauthorized.')
             ->assertStatus(403);

        $this->assertDatabaseHas('task_statuses', ['id' => $taskStatus->id]);
    }
}
