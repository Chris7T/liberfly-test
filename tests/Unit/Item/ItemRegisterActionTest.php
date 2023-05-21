<?php

namespace Tests\Unit\Item;

use App\Actions\Item\ItemRegisterAction;
use App\Repositories\Item\ItemInterfaceRepository;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;

class ItemRegisterActionTest extends TestCase
{
    public function test_item_registration_successfully()
    {
        $userId = 1;
        Auth::shouldReceive('id')
            ->andReturn($userId);

        $itemData = [
            'name' => 'Item name',
            'description' => 'Item description',
        ];
        $itemDataRepository = [
            'name' => 'Item name',
            'description' => 'Item description',
            'user_id' => $userId,
        ];

        $createdItem = [
            'id' => 1,
            'name' => 'Item name',
            'description' => 'Item description',
            'user_id' => $userId,
        ];

        $itemInterfaceRepositoryMock = Mockery::mock(ItemInterfaceRepository::class);
        $itemRegisterAction = new ItemRegisterAction($itemInterfaceRepositoryMock);
        $itemInterfaceRepositoryMock->shouldReceive('create')
            ->with($itemDataRepository)
            ->once()
            ->andReturn($createdItem);

        // Act
        $result = $itemRegisterAction($itemData);

        // Assert
        $this->assertEquals($createdItem, $result);
    }
}
