<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\User;
use Laravel\Socialite\Facades\Socialite;

class FacebookController extends Controller
{
    //
    public function getFacebookSignInUrl()
    {
        try {
            $url = Socialite::driver('facebook')->stateless()
                ->redirect()->getTargetUrl();
            return response()->json([
                'url' => $url,
            ])->setStatusCode(Response::HTTP_OK);
        } catch (\Exception $exception) {
            return $exception;
        }
    }

    public function loginCallback(Request $request)
    {
        try {
            $state = $request->input('state');

            parse_str($state, $result);
            $facebookUser = Socialite::driver('facebook')->stateless()->user();
           
            $user = User::where('facebook_id', $facebookUser->id)->first();
            if ($user) {
                throw new \Exception(__('facebook sign in existed'));
            }
            $user = User::create(
                [
                    'email' => $facebookUser->email,
                    'name' => $facebookUser->name,
                    'image' => $facebookUser->avatar,
                    'facebook_id'=> $facebookUser->id,
                    'password'=>  bcrypt('123456'),
                ]
            );
            return response()->json([
                'status' => __('facebook sign in successful'),
                'data' => $user,
            ], Response::HTTP_CREATED);

        } catch (\Exception $exception) {
            return response()->json([
                'status' => __('facebook sign in failed'),
                'error' => $exception,
                'message' => $exception->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
