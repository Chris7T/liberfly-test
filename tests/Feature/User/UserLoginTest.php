<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Mockery;
use Tests\TestCase;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserLoginTest extends TestCase
{
    private const ROUTE = 'user.login';

    public function test_expected_true_when_route_exists()
    {
        $this->assertTrue(Route::has(self::ROUTE));
    }

    public function test_expected_unprocessable_entity_exception_when_email_is_null()
    {
        $response = $this->postJson(route(self::ROUTE, ['email' => null]));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['email'])
            ->assertJson([
                'errors' => [
                    'email' => ['The email field is required.'],
                ],
            ]);
    }

    public function test_expected_unprocessable_entity_exception_when_email_does_not_have_email_format()
    {
        $response = $this->postJson(route(self::ROUTE, ['email' => 'string']));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['email'])
            ->assertJson([
                'errors' => [
                    'email' => ['The email field must be a valid email address.'],
                ],
            ]);
    }

    public function test_expected_unprocessable_entity_exception_when_password_is_null()
    {
        $response = $this->postJson(route(self::ROUTE, ['password' => null]));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['password'])
            ->assertJson([
                'errors' => [
                    'password' => ['The password field is required.'],
                ],
            ]);
    }

    public function test_expected_unprocessable_entity_when_email_is_not_found()
    {
        $email = $this->faker->unique()->email();
        $password =  $this->faker->password();
        $request = [
            'email' => $email,
            'password' =>  $password,
        ];

        $jwtAuthMock = Mockery::mock('overload:Tymon\JWTAuth\JWTAuth');
        $jwtAuthMock->shouldReceive('lockSubject')->andReturnSelf();
        $jwtAuthMock->shouldReceive('fromUser')
            ->andThrow(new JWTException('Unable to generate token'));

        $response = $this->postJson(route(self::ROUTE, $request));

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'message' => 'Invalid credentials',
            ]);
    }

    public function test_expected_unprocessable_entity_when_password_is_wrong()
    {
        $email = User::factory()->create()->email;
        $password =  $this->faker->password();
        $request = [
            'email' => $email,
            'password' =>  $password,
        ];

        $jwtAuthMock = Mockery::mock('overload:Tymon\JWTAuth\JWTAuth');
        $jwtAuthMock->shouldReceive('lockSubject')->andReturnSelf();
        $jwtAuthMock->shouldReceive('fromUser')
            ->andThrow(new JWTException('Unable to generate token'));

        $response = $this->postJson(route(self::ROUTE, $request));

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'message' => 'Invalid credentials',
            ]);
    }

    public function test_expected_server_error_when_jwt_auth_throw_exception()
    {
        $password = 'password';
        $email = 'test@gmail.com';
        User::factory()->create(
            [
                'email' => $email,
                'password' =>  Hash::make($password),
            ]
        );
        $request = [
            'email' => $email,
            'password' =>  $password,
        ];

        $jwtAuthMock = Mockery::mock('overload:Tymon\JWTAuth\JWTAuth');
        $jwtAuthMock->shouldReceive('lockSubject')->andReturnSelf();
        $jwtAuthMock->shouldReceive('fromUser')
            ->andThrow(new JWTException('Unable to generate token'));

        $response = $this->postJson(route(self::ROUTE, $request));

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson([
                'message' => config('messages.error.server'),
            ]);
    }

    public function test_expected_user_token()
    {
        $token = $this->faker->regexify('[A-Za-z0-9]{10}');
        $password = 'password';
        $email = 'test@gmail.com';
        User::factory()->create(
            [
                'email' => $email,
                'password' =>  Hash::make($password),
            ]
        );
        $request = [
            'email' => $email,
            'password' =>  $password,
        ];

        $jwtAuthMock = Mockery::mock('overload:Tymon\JWTAuth\JWTAuth');
        $jwtAuthMock->shouldReceive('lockSubject')->andReturnSelf();

        $jwtAuthMock->shouldReceive('fromUser')->andReturn($token);

        $response = $this->postJson(route(self::ROUTE, $request));

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'token' => $token,
            ]);
    }
}
