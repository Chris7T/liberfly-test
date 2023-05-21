<?php

namespace App\Actions\Item;

use App\Repositories\Item\ItemInterfaceRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ItemRegisterAction
{
    public function __construct(
        private readonly ItemInterfaceRepository $itemInterfaceRepository,
    ) {
    }

    public function __invoke(array $itemData): array
    {
        $itemData['user_id'] = Auth::id();
        $item = $this->itemInterfaceRepository->create($itemData);

        Cache::put("item-{$item['id']}", $item, config('cache.time.one_month'));

        return $item;
    }
}
