<?php

namespace Tests\Unit\Item;

use App\Actions\Item\ItemExistsVerify;
use App\Actions\Item\ItemGetAction;
use App\Repositories\Item\ItemInterfaceRepository;
use Mockery;
use Tests\TestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ItemGetActionTest extends TestCase
{
    public function test_item_get_successfully()
    {
        // Arrange
        $itemId = 1;
        $itemData = ['id' => 1, 'name' => 'Item 1', 'description' => 'Description'];

        $itemInterfaceRepositoryMock = Mockery::mock(ItemInterfaceRepository::class);
        $itemExistsVerifyMock = Mockery::mock(ItemExistsVerify::class);
        $itemGetAction = new ItemGetAction($itemInterfaceRepositoryMock, $itemExistsVerifyMock);

        $itemExistsVerifyMock->shouldReceive('__invoke')
            ->with($itemId)
            ->once();

        $itemInterfaceRepositoryMock->shouldReceive('getById')
            ->with($itemId)
            ->once()
            ->andReturn($itemData);

        // Act
        $result = $itemGetAction($itemId);

        // Assert
        $this->assertEquals($itemData, $result);
    }

    public function test_item_not_found_exception()
    {
        // Arrange
        $itemId = 1;

        $itemInterfaceRepositoryMock = Mockery::mock(ItemInterfaceRepository::class);
        $itemExistsVerifyMock = Mockery::mock(ItemExistsVerify::class);
        $itemGetAction = new ItemGetAction($itemInterfaceRepositoryMock, $itemExistsVerifyMock);

        $itemExistsVerifyMock->shouldReceive('__invoke')
            ->with($itemId)
            ->once()
            ->andThrow(new NotFoundHttpException('Item not found'));

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Item not found');

        // Act
        $itemGetAction($itemId);

        // Assert (Exception is thrown)
    }
}
