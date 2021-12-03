<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Validator;

class ForgotPasswordController extends Controller
{
    //
    public function forgot(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        Password::sendResetLink($validator->validated());
        return response()->json(["msg" => 'Reset password link sent on your email id.']);
    }

}
