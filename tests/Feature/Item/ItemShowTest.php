<?php

namespace Tests\Feature\Item;

use App\Actions\Item\ItemGetAction;
use App\Models\Item;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Mockery;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ItemShowTest extends TestCase
{
    private const ROUTE = 'item.show';

    public function test_expected_true_when_route_exists()
    {
        $this->assertTrue(Route::has(self::ROUTE));
    }

    public function test_expected_unauthorized_when_user_not_logged_in()
    {
        $response = $this->getJson(route(self::ROUTE, ['itemId' => 999999]));

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    public function test_expected_not_found_when_item_does_not_exist()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withToken($token)->getJson(route(self::ROUTE, ['itemId' => 999999]));

        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'message' => 'Item not found',
            ]);
    }

    public function test_expected_not_found_when_item_does_not_belong_to_the_logged_in_user()
    {
        $item = Item::factory()->create();
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withToken($token)->getJson(route(self::ROUTE, ['itemId' => $item->id]));

        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'message' => 'Item not found',
            ]);
    }

    public function test_expected_internal_server_error_when_exception_is_thrown()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $user->id]);
        $token = JWTAuth::fromUser($user);

        $itemGetActionMock = Mockery::mock(ItemGetAction::class);
        $itemGetActionMock->shouldReceive('__invoke')
            ->once()
            ->andThrow(new \Exception('Test exception'));

        $this->instance(ItemGetAction::class, $itemGetActionMock);

        $response = $this->withToken($token)->getJson(route(self::ROUTE, ['itemId' => $item->id]));

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson([
                'message' => config('messages.error.server'),
            ]);
    }

    public function test_expected_ok_when_item_exists()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $user->id]);
        $token = JWTAuth::fromUser($user);

        $response = $this->withToken($token)->getJson(route(self::ROUTE, ['itemId' => $item->id]));

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'data' => [
                    'id' => $item->id,
                    'name' => $item->name,
                    'description' => $item->description,
                ]
            ]);
    }
}
