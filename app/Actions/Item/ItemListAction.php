<?php

namespace App\Actions\Item;

use App\Repositories\Item\ItemInterfaceRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class ItemListAction
{
    public function __construct(
        private readonly ItemInterfaceRepository $itemInterfaceRepository
    ) {
    }

    public function __invoke(): LengthAwarePaginator
    {
        $userId = Auth::id();

        return $this->itemInterfaceRepository->get($userId);
    }
}
