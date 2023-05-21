<?php

namespace Tests\Unit\Item;

use App\Actions\Item\ItemExistsVerify;
use App\Actions\Item\ItemUpdateAction;
use App\Repositories\Item\ItemInterfaceRepository;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;

class ItemUpdateActionTest extends TestCase
{
    public function test_item_update_successfully()
    {
        $userId = 1;
        Auth::shouldReceive('id')
            ->andReturn($userId);
        $itemId = 1;
        $itemData = [
            'name' => 'Item name',
            'description' => 'Item description',
        ];
        $itemDataRepository = [
            'name' => 'Item name',
            'description' => 'Item description',
            'user_id' => $userId,
        ];

        $itemInterfaceRepositoryMock = Mockery::mock(ItemInterfaceRepository::class);
        $itemExistsVerifyMock = Mockery::mock(ItemExistsVerify::class);
        $itemUpdateAction = new ItemUpdateAction($itemInterfaceRepositoryMock, $itemExistsVerifyMock);

        $itemInterfaceRepositoryMock->shouldReceive('update')
            ->with($itemId, $itemDataRepository)
            ->once();

        $itemExistsVerifyMock->shouldReceive('__invoke')
            ->with($itemId)
            ->once();

        // Act
        $itemUpdateAction($itemId, $itemData);

        // Assert
        // No assertions needed since we are only testing the method calls and no return value
    }
}
