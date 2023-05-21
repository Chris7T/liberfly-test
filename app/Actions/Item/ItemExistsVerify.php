<?php

namespace App\Actions\Item;

use App\Repositories\Item\ItemInterfaceRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ItemExistsVerify
{
    public function __construct(
        private readonly ItemInterfaceRepository $itemInterfaceRepository
    ) {
    }

    public function __invoke(int $itemId): void
    {
        $userId = Auth::id();
        $itemExist = Cache::remember(
            "item-{$itemId}-user-{$userId}-exist",
            config('cache.time.one_month'),
            function () use ($itemId, $userId) {
                return $this->itemInterfaceRepository->existById($itemId, $userId);
            }
        );
        if (!$itemExist) {
            throw new NotFoundHttpException('Item not found');
        }
    }
}
