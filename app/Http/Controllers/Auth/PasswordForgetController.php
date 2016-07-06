<?php

namespace SmartCafe\Http\Controllers\Auth;

use ClassPreloader\Exceptions\Auth\Password\InvalidUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use SmartCafe\Exceptions\ValidateFail;
use SmartCafe\Http\Requests;
use SmartCafe\Http\Controllers\Controller;
use Password;
use Validator;

class PasswordForgetController extends Controller
{

    public function __construct()
    {
        $this->middleware('throttle:30,5', ['only' => ['store']]);
    }

    /**
     * Options for api document.
     *
     * @return JsonResponse
     */
    public function options():JsonResponse
    {
        $description = 'Send password reset mail.';
        $allow = ['POST'];
        $methods = [
            'POST' => [
                'email' => [
                    'description' => "User's email.",
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
            $this->sendMail($request);

            return response()
                ->json([
                    'status' => true,
                    'message' => 'Password reset mail sent.',
                ]);
            
        } catch (ValidateFail $e) {
            return response()
                ->json([
                    'status' => false,
                    'message' => $e->getErrors(),
                ]);
        } catch (InvalidUser $e) {
            return response()
                    ->json([
                        'status' => false,
                        'message' => 'This email does not exist.',
                    ]);
        }
    }

    protected function validator(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
        ]);

        if ($validator->fails()) {
            throw new ValidateFail($validator->errors());
        }
    }

    protected function sendMail(Request $request)
    {
        $response = Password::sendResetLink($request->only('email'), function (Message $m){
            $m->subject('Password Reset');
        });

        if ($response === Password::INVALID_USER) {
            throw new InvalidUser();
        }
    }
}
