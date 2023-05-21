<?php

namespace Tests\Feature\Item;

use App\Actions\Item\ItemListAction;
use App\Models\Item;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Mockery;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ItemIndexTest extends TestCase
{
    private const ROUTE = 'item.index';

    public function test_expected_true_when_route_exists()
    {
        $this->assertTrue(Route::has(self::ROUTE));
    }

    public function test_expected_unauthorized_when_user_not_logged_in()
    {
        $response = $this->getJson(route(self::ROUTE));

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    public function test_expected_internal_server_error_when_exception_is_thrown()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        Item::factory()->count(5)->create(['user_id' => $user->id]);

        $itemListActionMock = Mockery::mock(ItemListAction::class);
        $itemListActionMock->shouldReceive('__invoke')
            ->once()
            ->andThrow(new \Exception('Test exception'));

        $this->instance(ItemListAction::class, $itemListActionMock);

        $response = $this->withToken($token)->getJson(route(self::ROUTE));

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson([
                'message' => config('messages.error.server'),
            ]);
    }

    public function test_expected_ok_when_user_has_items()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $item = Item::factory()->create(['user_id' => $user->id]);

        $response = $this->withToken($token)->getJson(route(self::ROUTE));

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'data' => [
                    [
                        'id' => $item->id,
                        'name' => $item->name,
                        'description' => $item->description,
                        'user_id' => $user->id,
                    ],
                ],
                'links' => [

                    'first' => 'http://localhost/api/items?page=1',
                    'last' => 'http://localhost/api/items?page=1',
                    'prev' => null,
                    'next' => null,
                ],
            ]);
    }
}
