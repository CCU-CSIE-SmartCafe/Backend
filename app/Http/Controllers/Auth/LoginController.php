<?php

namespace SmartCafe\Http\Controllers\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use SmartCafe\Http\Requests;
use SmartCafe\Http\Controllers\Controller;

class LoginController extends Controller
{
    public function __construct()
    {
    }

    public function options(): JsonResponse
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
            ]
        ];

        return response()
                ->json([
                    'description' => $description,
                    'allow' => $allow,
                    'methods' => $methods,
                    'returns' => $returns,
                ], 200, [
                    'Allow' => 'OPTIONS, POST',
                ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }
}
