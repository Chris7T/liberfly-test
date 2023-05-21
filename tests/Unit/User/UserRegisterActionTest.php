<?php

namespace Tests\Unit;

use App\Actions\User\UserEmailUniqueVerifyAction;
use App\Actions\User\UserRegisterAction;
use App\Models\User;
use App\Repositories\UserInterfaceRepository;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserRegisterActionTest extends TestCase
{
    private $repositoryMock;
    private $emailVerifyActionMock;

    public function setUp(): void
    {
        parent::setUp();
        $this->repositoryMock = $this->createMock(UserInterfaceRepository::class);
        $this->emailVerifyActionMock = $this->createMock(UserEmailUniqueVerifyAction::class);
    }

    public function test_expected_conflict_http_exception_when_email_verify_throw_exception(): void
    {
        $this->expectException(ConflictHttpException::class);
        $this->expectExceptionMessage('Email is already being used');

        $userData = [
            'email' => 'test@example.com',
            'password' => 'password123',
            'name' => 'John Doe'
        ];

        $this->emailVerifyActionMock->expects($this->once())
            ->method('__invoke')
            ->with($userData['email'])
            ->willThrowException(new ConflictHttpException('Email is already being used'));

        $action = new UserRegisterAction($this->repositoryMock, $this->emailVerifyActionMock);

        ($action)($userData);
    }

    public function test_expected_jwt_token_when_user_registration_is_successful(): void
    {
        $passwordHashed = Hash::make('password123');
        $userData = [
            'email' => 'test@example.com',
            'password' => $passwordHashed,
            'name' => 'John Doe'
        ];

        Hash::shouldReceive('make')
            ->andReturn($passwordHashed);

        $this->emailVerifyActionMock->expects($this->once())
            ->method('__invoke')
            ->with($userData['email']);

        $user = new User();
        $user->email = $userData['email'];
        $user->password = $passwordHashed;
        $user->name = $userData['name'];

        $this->repositoryMock->expects($this->once())
            ->method('create')
            ->with($userData)
            ->willReturn($user);

        JWTAuth::shouldReceive('fromUser')
            ->once()
            ->with($user)
            ->andReturn('jwt_token');

        $action = new UserRegisterAction($this->repositoryMock, $this->emailVerifyActionMock);

        $jwtToken = ($action)($userData);

        $this->assertSame('jwt_token', $jwtToken);
    }
}
