<?php

namespace Tests\Feature\Item;

use App\Actions\Item\ItemUpdateAction;
use App\Models\Item;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Mockery;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ItemUpdateTest extends TestCase
{
    private const ROUTE = 'item.update';

    public function test_expected_true_when_route_exists()
    {
        $this->assertTrue(Route::has(self::ROUTE));
    }

    public function test_expected_unauthorized_when_user_not_logged_in()
    {
        $response = $this->putJson(route(self::ROUTE, ['itemId' => 1]), []);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    public function test_expected_unprocessable_entity_exception_when_name_is_null()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withToken($token)->putJson(route(self::ROUTE, ['itemId' => $item->id]), ['name' => null]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['name'])
            ->assertJson([
                'errors' => [
                    'name' => ['The name field is required.'],
                ],
            ]);
    }

    public function test_expected_unprocessable_entity_exception_when_description_is_null()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $item = Item::factory()->create();

        $response = $this->withToken($token)->putJson(route(self::ROUTE, ['itemId' => $item->id]), ['description' => null]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['description'])
            ->assertJson([
                'errors' => [
                    'description' => ['The description field is required.'],
                ],
            ]);
    }


    public function test_expected_not_found_when_item_does_not_exist()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $request = [
            'name' => 'Item name',
            'description' => 'Item description',
        ];

        $response = $this->withToken($token)->putJson(route(self::ROUTE, ['itemId' => 999999]), $request);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_expected_not_found_when_item_does_not_belong_to_the_logged_in_user()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $item = Item::factory()->create();

        $request = [
            'name' => 'Item name',
            'description' => 'Item description',
        ];

        $response = $this->withToken($token)->putJson(route(self::ROUTE, ['itemId' => $item->id]), $request);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_expected_internal_server_error_when_exception_is_thrown()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $item = Item::factory()->create(['user_id' => $user->id]);

        $request = [
            'name' => 'Item name',
            'description' => 'Item description',
        ];
        $itemUpdateActionMock = Mockery::mock(ItemUpdateAction::class);
        $itemUpdateActionMock->shouldReceive('__invoke')
            ->once()
            ->andThrow(new \Exception('Test exception'));

        $this->instance(ItemUpdateAction::class, $itemUpdateActionMock);

        $response = $this->withToken($token)->putJson(route(self::ROUTE, ['itemId' => $item->id]), $request);

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson([
                'message' => config('messages.error.server'),
            ]);
    }

    public function test_expected_no_content_when_item_is_updated()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $item = Item::factory()->create(['user_id' => $user->id]);

        $request = [
            'name' => 'Updated name',
            'description' => 'Updated description',
        ];

        $response = $this->withToken($token)->putJson(route(self::ROUTE, ['itemId' => $item->id]), $request);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'name' => $request['name'],
            'description' => $request['description'],
            'user_id' => $user->id
        ]);
    }
}
