<?php

namespace App\Repositories;

use App\Models\User;

interface UserInterfaceRepository
{
    public function create(array $data): User;

    public function findByEmail(string $email): ?User;
}
