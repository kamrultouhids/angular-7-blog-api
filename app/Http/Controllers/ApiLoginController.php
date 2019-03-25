<?php

namespace App\Http\Controllers;

use JWTAuth;

use JWTAuthException;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;


class ApiLoginController extends Controller
{

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'     => 'required',
            'password'  => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code'      => 422,
                'massage'   => apiValidateError($validator->errors()),
                'data'      => null
            ]);
        }

        $credentials = $request->only('email', 'password');
        $token = null;
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'code'      => 400,
                    'massage'    => 'invalid email/phone or password.',
                    'data'      => null,
                ]);
            }
        } catch (JWTAuthException $e) {
            return response()->json([
                'code'      => 400,
                'massage'   => 'failed to create token.',
                'data'      => null,
            ]);
        }

        $user = JWTAuth::toUser($token);
        $user['token'] = $token;
        return response()->json([
            'code'      => 200,
            'massage'    => 'Login success.',
            'data'      => $user,
        ]);
    }
}
