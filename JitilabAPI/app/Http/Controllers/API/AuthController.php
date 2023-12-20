<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    /**
     * Create user
     * @param request $request
     * @return User
     */

    public function createUser(Request $request)
    {

        try {

            $validateUser = validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'email' => 'required|email|unique:users,email',
                    'password' => 'required'
                ]
            );
            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            return response()->json([
                'status' => true,
                'message' => 'User Created Succesfully',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            /** @var \App\Models\MyUserModel $user **/
            $user = User::where('email', $request->email)->first();

            return response()->json([
                'status' => true,
                'message' => 'user Logged in successfully',
                'data' => [
                    'Token' => $user->createToken('API TOKEN')->plainTextToken,
                    'Email' => $user->email,
                    'Nama' => $user->name
                ]
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Email & password does not match'
            ], 401);
        }
    }
}
