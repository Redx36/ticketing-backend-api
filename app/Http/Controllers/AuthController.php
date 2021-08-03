<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class AuthController extends Controller
{
    //
    private $basic;
    private $client;


    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'reset']]);
    }



    /**
     * Get a JWT via given credentials.
     *
     * @return JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }


    /**
     * Reset a password when user forgot.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function reset(Request $request)
    {

        $passwordReset = Str::random(8);
        $password = Hash::make($passwordReset);
        $updateValue = array(
            'password' => $password
        );
        return response()->json([
            'success' => isset($user),
            'user_data' => isset($user) ? $user : false
        ], Response::HTTP_OK);
    }


    /**
     * Register a new user.
     *
     * @param Request $request
     * @return string
     */
    public function register(Request $request)
    {
        // all registering field
        $validator = Validator::make($request->all(),
            [
                'name' => '',
                'email' => 'email',
                'password' => 'required',
                'firstName' => '',
                'lastName' => '',
                'userAddress' => '',
                'role_id' => 'required',
            ]);

        if ($validator->fails()) {

            return response()->json(['error'=>$validator->errors()], 401);

        }
        $query =  DB::table('users');

        if ($query->where('users.email', '=', $request->email)->first()) {
            return response()->json(['error' => 'Found email, please use another one'], 302);
        }

        $user = new User();
        $user->name = $request->name;
        if($request->email) {
            $user->email = $request->email;
        }
        $user->password = bcrypt($request->password);
        if($request->firstName) {
            $user->firstName = $request->firstName;
        }
        if($request->lastName) {
            $user->lastName = $request->lastName;
        }

        $user->address = $request->userAddress;
        $user->role_id = number_format($request->role_id);
        $user->save();


        return response()->json([
            'success' => true,
            'user_data' => $user,
        ], Response::HTTP_OK);
    }

    /**
     * Get the authenticated User.
     *
     * @return JsonResponse
     */
    public function me()
    {
        return response()->json(auth('api')->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'id' => auth('api')->user()->id,
//            'name' => auth('api')->user()->name,
//            'firstName' => auth('api')->user()->firstName,
//            'lastName' => auth('api')->user()->lastName,
//            'email' => auth('api')->user()->email,
//            'address' => auth('api')->user()->address,
//            'createdAt' => auth('api')->user()->createdAt,
//            'updatedAt' => auth('api')->user()->updatedAt,
//            'role' => auth('api')->user()->role->name,
            // 'user' => auth('api')->user(),
        ]);
    }
}
