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
             ->assertSee('Метки')
             ->assertStatus(200);
    }

    public function testCreate()
    {
        $this->actingAs($this->user)
             ->get(route('labels.create'))
             ->assertSee('Создать метку')
             ->assertStatus(200);
    }

    public function testCreateUnauthorizedUser()
    {
        $this->get(route('labels.create'))
             ->assertSee('This action is unauthorized.')
             ->assertStatus(403);
    }

    public function testStore()
    {
        $data = Label::factory()->make()->attributesToArray();

        $this->actingAs($this->user)
             ->post(route('labels.store', $data))
             ->assertRedirect(route('labels.index'))
             ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('labels', $data);
    }

    public function testStoreUnauthorizedUser()
    {
        $data = Label::factory()->make()->attributesToArray();

        $this->post(route('labels.store', $data))
            ->assertSee('This action is unauthorized.')
            ->assertStatus(403);

        $this->assertDatabaseMissing('labels', $data);
    }

    public function testStoreEmptyData()
    {
        $data = ['name' => ''];

        $this->actingAs($this->user)
            ->post(route('labels.store', $data))
            ->assertSessionHasErrors([
                'name' => 'Это обязательное поле'
            ]);

        $this->assertDatabaseMissing('labels', $data);
    }

    public function testStoreUniqueFieldName()
    {
        $data = ['name' => 'новый'];
        Label::factory()->create($data);

        $this->actingAs($this->user)
             ->from('labels/create')
             ->post(route('labels.store', $data))
             ->assertStatus(302)
             ->assertSessionHasErrors(['name' => ':Entity с таким именем уже существует'])
             ->assertRedirect(route('labels.create'));
    }

    public function testEdit()
    {
        $label = Label::factory()->create();

        $this->actingAs($this->user)
             ->get(route('labels.edit', $label))
             ->assertSee('Изменение метки')
             ->assertStatus(200);
    }

    public function testEditUnauthorizedUser()
    {
        $taskStatus = Label::factory()->create();

        $this->get(route('labels.edit', $taskStatus))
             ->assertSee('This action is unauthorized.')
             ->assertStatus(403);
    }

    public function testUpdate()
    {
        $label = Label::factory()->create();
        $data = Label::factory()->make()->attributesToArray();

        $this->actingAs($this->user)
             ->patch(route('labels.update', $label), $data)
             ->assertRedirect(route('labels.index'))
             ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('labels', $data);
    }

    public function testUpdateUnauthorizedUser()
    {
        $taskStatus = Label::factory()->create();
        $data = $taskStatus::factory()->make()->attributesToArray();

        $this->patch(route('labels.update', $taskStatus), $data)
             ->assertSee('This action is unauthorized.')
             ->assertStatus(403);

        $this->assertDatabaseMissing('labels', $data);
    }

    public function testUpdateEmptyData()
    {
        $label = Label::factory()->create();
        $data = ['name' => ''];

        $this->actingAs($this->user)
             ->from(route('labels.edit', $label))
             ->patch(route('labels.update', $label), $data)
             ->assertStatus(302)
             ->assertSessionHasErrors([
                'name' => 'Это обязательное поле'
             ])
             ->assertRedirect(route('labels.edit', $label));
    }

    public function testUpdateUniqueFieldName()
    {
        $data = ['name' => 'новый'];
        Label::factory()->create($data);
        $label = Label::factory()->create();

        $this->actingAs($this->user)
             ->from(route('labels.edit', $label))
             ->patch(route('labels.update', $label), $data)
             ->assertStatus(302)
             ->assertSessionHasErrors([
                'name' => ':Entity с таким именем уже существует'
             ])
             ->assertRedirect(route('labels.edit', $label));
    }

    public function testDestroy()
    {
        $label = Label::factory()->create();

        $this->actingAs($this->user)
             ->delete(route('labels.destroy', $label))
             ->assertRedirect(route('labels.index'))
             ->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('labels', $label->only('id'));
    }

    public function testDestroyUnauthorizedUser()
    {
        $label = Label::factory()->create();

        $this->delete(route('labels.destroy', $label))
             ->assertSee('This action is unauthorized.')
             ->assertStatus(403);

        $this->assertDatabaseHas('labels', ['id' => $label->id]);
    }
}
