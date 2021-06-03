<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'registration']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }


    public static function response($data = null, $message = null)
    {
        return [
            'data'    => $data,
            'message' => $message,
        ];
    }


    public function getResetToken(Request $request)
    {
        $this->validate($request, ['email' => 'required|email']);
        if ($request->wantsJson()) {
            $user = User::where('email', $request->input('email'))->first();
            if (!$user) {
                return response()->json(response(null, trans('passwords.user')), 400);
            }
            $token = $this->broker()->createToken($user);
            return response()->json(response(['token' => $token]));
        }
    }

    /**
     * User registration
     */


//    public function forget_password(Request $request){
//        $user = User::where('email', '=', $request->only(['email']));
//        $user = $user->first();
//        $token = Str::random(20);
//        $user->update(['remember_token' => $token]);
//        $data = [
//            'email' => $user->email(),
//            'token' => $token
//        ];
//        Mail::send('mail', $data, function ($m) use ($user) {
//            $m->to($user->email);
//            $m->subject('Password recovery!');
//        });
//        return 1;
//
//    }

    public function registration()
    {
        $name = request('name');
        $email = request('email');
        $password = request('password');


        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->password = Hash::make($password);
        $user->save();

        return response()->json(['message' => 'Successfully registration!']);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
