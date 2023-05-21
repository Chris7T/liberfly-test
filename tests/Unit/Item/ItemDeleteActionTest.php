<?php

namespace Tests\Unit\Item;

use App\Actions\Item\ItemDeleteAction;
use App\Actions\Item\ItemExistsVerify;
use App\Repositories\Item\ItemInterfaceRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Tests\TestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ItemDeleteActionTest extends TestCase
{
    private $itemInterfaceRepositoryMock;
    private $itemExistsVerifyMock;

    public function setUp(): void
    {
        parent::setUp();
        $this->itemInterfaceRepositoryMock = Mockery::mock(ItemInterfaceRepository::class);
        $this->itemExistsVerifyMock = Mockery::mock(ItemExistsVerify::class);
    }

    public function test_item_deletion()
    {
        $itemId = 1;
        $userId = 1;

        Auth::shouldReceive('id')
            ->andReturn($userId);

        $this->itemExistsVerifyMock->shouldReceive('__invoke')
            ->with($itemId);
        $this->itemInterfaceRepositoryMock->shouldReceive('existById')
            ->with($itemId, $userId)
            ->andReturn(true);

        $this->itemInterfaceRepositoryMock->shouldReceive('delete')
            ->with($itemId)
            ->once();

        Cache::shouldReceive('delete')
            ->with("item-{$itemId}")
            ->once();

        Cache::shouldReceive('delete')
            ->with("item-{$itemId}-user-{$userId}-exist")
            ->once();
        $action = new ItemDeleteAction($this->itemInterfaceRepositoryMock, $this->itemExistsVerifyMock);

        $action($itemId);
    }

    public function test_item_not_found_exception()
    {
        $itemId = 1;
        $userId = 1;

        $this->itemExistsVerifyMock->shouldReceive('__invoke')
            ->with($itemId)
            ->andThrow(new NotFoundHttpException('Item not found'));

        $this->itemInterfaceRepositoryMock->shouldReceive('existById')
            ->with($itemId, $userId)
            ->andReturn(false);

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Item not found');

        $action = new ItemDeleteAction($this->itemInterfaceRepositoryMock, $this->itemExistsVerifyMock);
        $action($itemId);
    }
}
