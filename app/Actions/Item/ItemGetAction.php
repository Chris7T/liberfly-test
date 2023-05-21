<?php

namespace App\Actions\Item;

use App\Repositories\Item\ItemInterfaceRepository;
use Illuminate\Support\Facades\Cache;

class ItemGetAction
{
    public function __construct(
        private readonly ItemInterfaceRepository $itemInterfaceRepository,
        private readonly ItemExistsVerify $itemExistsVerify
    ) {
    }

    public function __invoke(int $itemId): ?array
    {
        ($this->itemExistsVerify)($itemId);
        return Cache::remember(
            "item-{$itemId}",
            config('cache.time.one_month'),
            function () use ($itemId) {
                return $this->itemInterfaceRepository->getById($itemId);
            }
        );
    }
}
