<?php

namespace Tests\Unit\Item;

use App\Actions\Item\ItemExistsVerify;
use App\Repositories\Item\ItemInterfaceRepository;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ItemExistsVerifyTest extends TestCase
{
    private $itemInterfaceRepositoryMock;

    public function setUp(): void
    {
        parent::setUp();
        $this->itemInterfaceRepositoryMock = Mockery::mock(ItemInterfaceRepository::class);
    }

    public function test_item_exists()
    {
        $itemId = 1;
        $userId = 1;
        Auth::shouldReceive('id')
            ->andReturn($userId);

        $this->itemInterfaceRepositoryMock->shouldReceive('existById')
            ->with($itemId, $userId)
            ->andReturn(true);

        $itemExistsVerify = new ItemExistsVerify($this->itemInterfaceRepositoryMock);

        $itemExistsVerify($itemId);

        $this->assertTrue(true);
    }

    public function test_item_not_found_exception()
    {
        $itemId = 1;
        $userId = 1;

        Auth::shouldReceive('id')
            ->andReturn($userId);

        $this->itemInterfaceRepositoryMock->shouldReceive('existById')
            ->with($itemId, $userId)
            ->andReturn(false);

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Item not found');

        $itemExistsVerify = new ItemExistsVerify($this->itemInterfaceRepositoryMock);

        $itemExistsVerify($itemId);
    }
}
