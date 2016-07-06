<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use SmartCafe\Tests\UseDatabase;

class RegisterTest extends TestCase
{
    use UseDatabase;

    /**
     * Test options method for api document.
     */
    public function testOptions()
    {
        $description = 'Register a new user.';
        $allow = ['POST'];
        $methods = [
            'POST' => [
                'email' => [
                    'description' => "User's email.",
                    'required' => true,
                    'type' => 'string',
                ],
                'name' => [
                    'description' => "User's name.",
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
                'description' => 'Is this request successfully?',
                'type' => 'boolean',
            ],
            'message' => [
                'description' => 'Request message.',
                'type' => 'array/string',
            ],
        ];

        $this->makeRequest('options', '/auth/register')
            ->seeJson([
                'description' => $description,
                'allow' => $allow,
                'methods' => $methods,
                'returns' => $returns,
            ]);
    }

    /**
     * Test register feature.
     */
    public function testNormalStore()
    {
        $expected['email'] = str_random(10).'@gmail.com';
        $expected['name'] = str_random(20);

        $this->post('/auth/register', [
            'email' => $expected['email'],
            'name' => $expected['name'],
            'password' => str_random(20),
        ])
            ->seeJson([
                'status' => true,
            ]);

        $this->seeInDatabase('users', $expected);
    }

    public function testRepeatEmailStore()
    {
        $expected['email'] = str_random(10).'@gmail.com';
        $expected['name'] = str_random(20);

        $this->post('/auth/register', [
            'email' => $expected['email'],
            'name' => $expected['name'],
            'password' => str_random(20),
        ]);

        $this->post('/auth/register', [
            'email' => $expected['email'],
            'name' => str_random(20),
            'password' => str_random(20),
        ])
            ->seeJson([
                'status' => false,
                'message' => [
                    'email' => ['The email has already been taken.'],
                ],
            ]);
    }

    public function testTooManyRequests()
    {
        for ($i = 0; $i < 10; $i++) {
            $this->post('/auth/register', [
                'email' => str_random().'@gmail.com',
                'name' => str_random(),
                'password' => str_random(),
            ]);
        }

        $this->post('/auth/register', [
            'email' => str_random().'@gmail.com',
            'name' => str_random(),
            'password' => str_random(),
        ])
            ->seeJson([
                'status' => false,
                'message' => 'Too many requests.',
            ])
            ->seeStatusCode(429);
    }
}
