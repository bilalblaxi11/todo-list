<?php

namespace App\Http\Controllers;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    /**
     * AuthController constructor.
     */
    public function __construct() {
        $this->middleware(['auth:api','verified'], ['except' => ['login', 'register','verify']]);
    }

    /**
     * Login User.
     *
     * @return JsonResponse
     */
    public function login(LoginRequest $request) {

        if (! $token = auth()->attempt($request->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if(!auth()->user()->hasVerifiedEmail()){
            auth()->logout();
            return response()->json(['error' => 'Email is not verified'], 401);
        }

        return User::createToken($token);
    }

    /**
     * Register User.
     *
     * @return JsonResponse
     */
    public function register(RegisterRequest $request) {

        $user = User::create(array_merge(
            $request->validated(),
            ['password' => bcrypt($request->password)]
        ));

        event(new Registered($user));
        return response()->json([
            'message' => 'User successfully registered'
        ]);
    }

    /**
     * Verify Email.
     *
     * @return JsonResponse
     */
    public function verify(Request $request) {

        $user = User::find($request->route('id'));

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Your email is already verified.']);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return response()->json(['message' => 'Your email has been verified.']);
    }

    /**
     * Logout User.
     *
     * @return JsonResponse
     */
    public function logout() {
        auth()->logout();

        return response()->json(['message' => 'User successfully signed out']);
    }

    /**
     * Refresh Jwt token.
     *
     * @return JsonResponse
     */
    public function refresh() {
        return User::createToken(auth()->refresh());
    }

    /**
     * Get User.
     *
     * @return JsonResponse
     */
    public function getUser() {
        return response()->json(auth()->user());
    }
}
