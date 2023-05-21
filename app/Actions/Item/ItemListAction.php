<?php

namespace App\Actions\Item;

use App\Repositories\Item\ItemInterfaceRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ItemListAction
{
    public function __construct(
        private readonly ItemInterfaceRepository $itemInterfaceRepository
    ) {
    }

    public function __invoke(int $page): LengthAwarePaginator
    {
        $userId = Auth::id();

        return Cache::remember(
            "items-list-user-{$userId}-page-{$page}",
            config('cache.time.one_month'),
            function () use ($userId) {
                return $this->itemInterfaceRepository->get($userId);
            }
        );
    }
}
