<?php

namespace App\Actions\Item;

use App\Repositories\Item\ItemInterfaceRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ItemUpdateAction
{
    public function __construct(
        private readonly ItemInterfaceRepository $itemInterfaceRepository,
        private readonly ItemExistsVerify $itemExistsVerify
    ) {
    }

    public function __invoke(int $itemId, array $itemData): void
    {
        $itemData['user_id'] = Auth::id();
        ($this->itemExistsVerify)($itemId);
        $this->itemInterfaceRepository->update($itemId, $itemData);

        Cache::put("item-{$itemId}", $itemData, config('cache.time.one_month'));
    }
}
