<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use SmartCafe\Tests\UseDatabase;
use SmartCafe\User;

class LoginTest extends TestCase
{

    use UseDatabase;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testOptions()
    {
        $description = 'Login service for users.';
        $allow = ['POST'];
        $methods = [
            'POST' => [
                'email' => [
                    'description' => "User's email.",
                    'required' => true,
                    'type' => 'string',
                ],
                'password' => [
                    'description' => "User's password.",
                    'required' => true,
                    'type' => 'string',
                ],
            ],
        ];

        $returns = [
            'status' => [
                'description' => 'Does login successfully?',
                'type' => 'boolean',
            ],
            'message' => [
                'description' => 'Response messages.',
                'type' => 'array/string',
            ],
            'token' => [
                'description' => 'Token to identify user.',
                'type' => 'string',
            ],
            'expire' => [
                'description' => 'Token expire timestamp',
                'type' => 'int',
            ],
            'expire_refresh' => [
                'description' => 'Token refresh expire timestamp',
                'type' => 'int',
            ]
        ];

        $this->makeRequest('options', '/auth/login')
            ->seeJson([
                'description' => $description,
                'allow' => $allow,
                'methods' => $methods,
                'returns' => $returns,
            ]);
    }
    
    public function testNormalStore()
    {
        $expect['email'] = str_random().'@gmail.com';
        $expect['password'] = str_random();
        
        factory(User::class)->create([
            'email' => $expect['email'],
            'password' => bcrypt($expect['password']),
        ]);

        
        $this->post('/auth/login', [
            'email' => $expect['email'],
            'password' => $expect['password'],
        ])
            ->seeJson([
                'status' => true,
                'message' => 'Login successfully.',
            ]);
    }
    
    public function testInvalidCredential()
    {
        $user = factory(User::class)->make();
        $this->post('/auth/login', [
            'email' => $user->email,
            'password' => $user->password,
        ])
            ->seeJson([
                'status' => false,
                'message' => 'Invalid credentials.',
            ]);
    }
}
