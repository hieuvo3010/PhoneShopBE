<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User,App\Admin;
class VerificationController extends Controller
{
    
    //
    public function verify_user($id, Request $request) {
        if (!$request->hasValidSignature()) {
            return response()->json(["msg" => "Invalid/Expired url provided."], 401);
        }
    
        $user = User::findOrFail($id);
    
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }
        return response()->json($user);
    }

    // public function verify_admin($id, Request $request) {
    //     if (!$request->hasValidSignature()) {
    //         return response()->json(["msg" => "Invalid/Expired url provided."], 401);
    //     }
    
    //     $admin = Admin::findOrFail($id);
    
    //     if (!$admin->hasVerifiedEmail()) {
    //         $admin->markEmailAsVerified();
    //     }
    //     return response()->json($admin);
    // }

    
    public function resend() {
        if (auth()->user()->hasVerifiedEmail()) {
            return response()->json(["msg" => "Email already verified."], 400);
        }
    
        auth()->user()->sendEmailVerificationNotification();
    
        return response()->json(["msg" => "Email verification link sent on your email id"]);
    }
}
