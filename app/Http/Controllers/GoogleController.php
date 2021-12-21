<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\User;
use JWTAuth;
use Carbon\Carbon;
use App\Http\Resources\UserResource;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function getGoogleSignInUrl()
    {
        try {
            $url = Socialite::driver('google')->stateless()
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
            $googleUser = Socialite::driver('google')->stateless()->user();
            
            $user = User::where('email', $googleUser->email)->first();
            if ($user) {
                $token = JWTAuth::fromUser($user);
            }else{
                $user = new User();
                $user->email = $googleUser->email;
                $user->name = $googleUser->name;
                $user->image = $googleUser->avatar;
                $user->google_id = $googleUser->id;
                $user->email_verified_at = now()->timestamp;
                $user->password = bcrypt('123456');
                $user->save();
                // $user = User::create(
                //     [
                //         'email' => $googleUser->email,
                //         'name' => $googleUser->name,
                //         'image' => $googleUser->avatar,
                //         'google_id'=> $googleUser->id,
                //         'email_verified_at' => now()->timestamp,
                //         'password'=>  bcrypt('123456'),
                //     ]
                // );
                $token = JWTAuth::fromUser($user);
            }
         
            
            return response()->json([
                'status' => __('google sign in successful'),
                'token' => $token
            ], Response::HTTP_CREATED);

        } catch (\Exception $exception) {
            return response()->json([
                'status' => __('google sign in failed'),
                'error' => $exception,
                'message' => $exception->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    private function issueToken(User $user) {

        $userToken = $user->token() ?? $user->createToken('socialLogin');
    
        return [
            "token_type" => "Bearer",
            "access_token" => $userToken->accessToken
        ];
    }
}
