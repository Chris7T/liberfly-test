<?php

namespace App\Repositories\Item;

use App\Models\Item;
use Illuminate\Pagination\LengthAwarePaginator;

class ItemEloquentRepository implements ItemInterfaceRepository
{
    public function __construct(
        private readonly Item $model,
    ) {
    }

    public function create(array $itemData): array
    {
        return $this->model->create($itemData)->toArray();
    }

    public function update(int $id, array $itemData): void
    {
        $this->model->find($id)->update($itemData);
    }

    public function existById(int $id, int $userId): bool
    {
        return $this->model->where('id', $id)->where('user_id', $userId)->exists();
    }

    public function getById(int $id): ?array
    {
        return $this->model->find($id)?->toArray();
    }

    public function get(int $userId): LengthAwarePaginator
    {
        return $this->model->where('user_id', $userId)->paginate();
    }

    public function delete(int $id): void
    {
        $this->model->find($id)->delete();
    }
}
