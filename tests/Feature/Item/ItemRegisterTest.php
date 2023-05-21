<?php

namespace Tests\Feature\Item;

use App\Actions\Item\ItemRegisterAction;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Mockery;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ItemRegisterTest extends TestCase
{
    private const ROUTE = 'item.create';

    public function test_expected_true_when_route_exists()
    {
        $this->assertTrue(Route::has(self::ROUTE));
    }

    public function test_expected_unauthorized_when_user_not_logged_in()
    {
        $response = $this->postJson(route(self::ROUTE, []));

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    public function test_expected_unprocessable_entity_exception_when_name_is_null()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $response = $this->withToken($token)->postJson(route(self::ROUTE, ['name' => null]));

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
        $response = $this->withToken($token)->postJson(route(self::ROUTE, ['description' => null]));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['description'])
            ->assertJson([
                'errors' => [
                    'description' => ['The description field is required.'],
                ],
            ]);
    }

    public function test_expected_internal_server_error_when_exception_is_thrown()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $request = [
            'name' => 'Item name',
            'description' => 'Item description',
        ];
        $itemRegisterActionMock = Mockery::mock(ItemRegisterAction::class);
        $itemRegisterActionMock->shouldReceive('__invoke')
            ->once()
            ->andThrow(new \Exception('Test exception'));

        $this->instance(ItemRegisterAction::class, $itemRegisterActionMock);

        $response = $this->withToken($token)->postJson(route(self::ROUTE, $request));

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson([
                'message' => config('messages.error.server'),
            ]);
    }

    public function test_expected_created_when_valid_request_is_provided()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $request = [
            'name' => 'Item name',
            'description' => 'Item description',
        ];

        $response = $this->withToken($token)->postJson(route(self::ROUTE, $request));

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'data' => [
                    'name' => $request['name'],
                    'description' => $request['description'],
                    'user_id' => $user->id
                ]
            ]);

        $this->assertDatabaseHas('items', [
            'name' => $request['name'],
            'description' => $request['description'],
            'user_id' => $user->id
        ]);
    }
}
