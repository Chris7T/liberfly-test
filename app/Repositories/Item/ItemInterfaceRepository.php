<?php

namespace App\Repositories\Item;

use Illuminate\Pagination\LengthAwarePaginator;

interface ItemInterfaceRepository
{
    public function create(array $itemData): array;

    public function update(int $id, array $itemData): void;

    public function getById(int $id): ?array;

    public function existById(int $id, int $userId): bool;

    public function get(int $userId): LengthAwarePaginator;

    public function delete(int $id): void;
}
