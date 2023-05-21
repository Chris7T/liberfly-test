<?php

namespace App\Actions\User;

use App\Repositories\User\UserInterfaceRepository;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserRegisterAction
{
    public function __construct(
        private readonly UserInterfaceRepository $userInterfaceRepository,
        private readonly UserEmailUniqueVerifyAction $userEmailUniqueVerifyAction
    ) {
    }

    public function __invoke(array $userData): string
    {
        ($this->userEmailUniqueVerifyAction)($userData['email']);
        $userData['password'] = Hash::make($userData['password']);
        $user = $this->userInterfaceRepository->create($userData);

        return JWTAuth::fromUser($user);
    }
}
