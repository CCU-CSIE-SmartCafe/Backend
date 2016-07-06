<?php

namespace SmartCafe\Http\Controllers\Auth;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use SmartCafe\Exceptions\Auth\Login\Credential;
use SmartCafe\Exceptions\ValidateFail;
use SmartCafe\Http\Requests;
use SmartCafe\Http\Controllers\Controller;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;

class LoginController extends Controller
{
    public function __construct()
    {
    }

    /**
     * Options for api document.
     *
     * @return JsonResponse
     */
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
            $token = $this->auth($request->only(['email', 'password']));
            $expire = Carbon::now()->addHour()->timestamp;
            $expireRefresh = Carbon::now()->addWeeks(2)->timestamp;

            return response()
                    ->json([
                        'status' => true,
                        'message' => 'Login successfully',
                        'token' => $token,
                        'expire' => $expire,
                        'expire_refresh' => $expireRefresh,
                    ]);
        } catch (ValidateFail $e) {
            return response()
                    ->json([
                        'status' => false,
                        'message' => $e->getErrors(),
                    ]);
        } catch (JWTException $e) {
            return response()
                    ->json([
                        'status' => false,
                        'message' => 'Server is too busy to create token, please try in minutes again.',
                    ], 500);
        } catch (Credential $e) {
            return response()
                    ->json([
                        'status' => false,
                        'message' => $e->getMessage(),
                    ]);
        }
    }

    protected function validator(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails())
            throw new ValidateFail($validator->errors());
    }

    protected function auth(array $credentials): string
    {
        if (!$token = JWTAuth::attempt($credentials)) {
            throw new Credential('Invalid Credentials');
        }

        return $token;
    }
}
