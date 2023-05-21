<?php

namespace Tests\Feature\Item;

use App\Actions\Item\ItemDeleteAction;
use App\Models\Item;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Mockery;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ItemDeleteTest extends TestCase
{
    private const ROUTE = 'item.destroy';

    public function test_expected_true_when_route_exists()
    {
        $this->assertTrue(Route::has(self::ROUTE));
    }

    public function test_expected_unauthorized_when_user_not_logged_in()
    {
        $response = $this->deleteJson(route(self::ROUTE, ['itemId' => 999]));

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    public function test_expected_not_found_when_item_does_not_exist()
    {
        $token = JWTAuth::fromUser(User::factory()->create());
        $response = $this->withToken($token)->deleteJson(route(self::ROUTE, ['itemId' => 999]));

        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'message' => 'Item not found',
            ]);
    }

    public function test_expected_not_found_when_item_does_not_belong_to_the_logged_in_user()
    {
        $item = Item::factory()->create();
        $token = JWTAuth::fromUser(User::factory()->create());

        $response = $this->withToken($token)->deleteJson(route(self::ROUTE, ['itemId' => $item->id]));

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

        $itemDeleteActionMock = Mockery::mock(ItemDeleteAction::class);
        $itemDeleteActionMock->shouldReceive('__invoke')
            ->once()
            ->andThrow(new \Exception('Test exception'));

        $this->instance(ItemDeleteAction::class, $itemDeleteActionMock);

        $response = $this->withToken($token)->deleteJson(route(self::ROUTE, ['itemId' => $item->id]));

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson([
                'message' => config('messages.error.server'),
            ]);
    }

    public function test_expected_no_content_when_item_deleted()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $user->id]);
        $token = JWTAuth::fromUser($user);

        $response = $this->withToken($token)
            ->deleteJson(route(self::ROUTE, ['itemId' => $item->id]));

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('items', ['id' => $item->id]);
    }
}
