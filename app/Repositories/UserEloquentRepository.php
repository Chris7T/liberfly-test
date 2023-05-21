<?php

namespace App\Repositories;

use App\Models\User;

class UserEloquentRepository implements UserInterfaceRepository
{
    public function __construct(
        private readonly User $model,
    ) {
    }

    public function create(array $data): User
    {
        return $this->model->create($data);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->model->firstWhere('email', $email);
    }
}
