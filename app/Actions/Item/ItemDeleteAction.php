<?php

namespace App\Actions\Item;

use App\Repositories\Item\ItemInterfaceRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ItemDeleteAction
{
    public function __construct(
        private readonly ItemInterfaceRepository $itemInterfaceRepository,
        private readonly ItemExistsVerify $itemExistsVerify,
    ) {
    }

    public function __invoke(int $itemId): void
    {
        $userId = Auth::id();
        ($this->itemExistsVerify)($itemId);
        $this->itemInterfaceRepository->delete($itemId);

        Cache::delete("item-{$itemId}");
        Cache::delete("item-{$itemId}-user-{$userId}-exist");
    }
}
