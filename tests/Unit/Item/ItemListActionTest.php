<?php

namespace Tests\Unit\Item;

use App\Actions\Item\ItemListAction;
use App\Repositories\Item\ItemInterfaceRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;

class ItemListActionTest extends TestCase
{
    public function test_item_list_successfully()
    {
        $page = 1;
        $userId = 1;
        $items = [
            ['id' => 1, 'name' => 'Item 1', 'description' => 'Description 1'],
            ['id' => 2, 'name' => 'Item 2', 'description' => 'Description 2'],
            ['id' => 3, 'name' => 'Item 3', 'description' => 'Description 3'],
        ];

        $resultExtected = new LengthAwarePaginator($items, 3, 5, 1);

        Auth::shouldReceive('id')
            ->andReturn($userId);

        $itemInterfaceRepositoryMock = Mockery::mock(ItemInterfaceRepository::class);
        $itemListAction = new ItemListAction($itemInterfaceRepositoryMock);

        $itemInterfaceRepositoryMock->shouldReceive('get')
            ->with($userId)
            ->once()
            ->andReturn($resultExtected);

        $result = $itemListAction($page);

        $this->assertEquals($resultExtected, $result);
    }
}
