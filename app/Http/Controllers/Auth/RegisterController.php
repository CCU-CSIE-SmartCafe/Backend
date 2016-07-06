<?php

namespace SmartCafe\Http\Controllers\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use SmartCafe\Exceptions\ValidateFail;
use SmartCafe\Http\Requests;
use SmartCafe\Http\Controllers\Controller;
use SmartCafe\User;
use Validator;

class RegisterController extends Controller
{

    public function __construct()
    {
    }

    /**
     * Options for api document.
     */
    public function options(): JsonResponse
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
            ]
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
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $this->validator($request);
            $this->createUser($request);
            
            return response()
                    ->json([
                        'status' => true,
                        'message' => 'Register successfully.',
                    ]);
        } catch (ValidateFail $e) {
            return response()
                    ->json([
                        'status' => false,
                        'message' => $e->getErrors(),
                    ]);
        }
    }

    /**
     * Validate the request data is valid.
     * 
     * @param Request $request
     * @throws ValidateFail
     */
    protected function validator(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255|unique:users',
            'name' => 'required|max:255',
            'password' => 'required|min:6',
        ]);
        
        if ($validator->fails())
            throw new ValidateFail($validator->errors());
    }

    /**
     * Crate user data in database. 
     * 
     * @param Request $request
     */
    protected function createUser(Request $request)
    {
        User::create([
            'email' => $request->email,
            'name' => $request->name,
            'password' => bcrypt($request->password),
        ]);
    }

}
