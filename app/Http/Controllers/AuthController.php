<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function register(Request $request) {
        try {
            DB::beginTransaction();

            $newUser = new User();
            $newUser->username = $request->username;
            $newUser->email = $request->email;
            $newUser->password = $request->password;
            $newUser->save();

            DB::commit();

            $response = [
                'code' => 1,
                'message' => 'Anda berhasil mendaftar',
                'data' => []
            ];

            return response()->json($response, 200);

        } catch(QueryException $qe) {
            Log::error($qe);
            DB::rollback();

            $response = [
                'code' => 2,
                'message' => 'Username / Email Tidak Tersedia',
                'data' => []
            ];

            return response()->json($response, 200);

        } catch(Exception $e) {
            Log::error($e);
            DB::rollback();

            $response = [
                'code' => 500,
                'message' => 'Internal Server Error',
                'data' => []
            ];

            return response()->json($response, 500);
        }
    }

    public function login(Request $request) {
        // get request data
        $email = $request->email;
        $password = $request->password;

        // find user
        $user = User::where('email', $email)->where('password', $password)->first();
        
        // if user not found
        if (!$user) {
            $response = [
                'code' => 404,
                'message' => 'User Not Found',
                'data' => []
            ];
            return response()->json($response, 404);
        }

        // if user found

        // create login token
        try {
            DB::beginTransaction();

            $token = sha1($email.$password);
            $user->token = $token;
            $user->save();

            DB::commit();
        } catch(QueryException $qe) {
            Log::error($qe);
            DB::rollback();

            $response = [
                'code' => 500,
                'message' => 'Internal Server Error',
                'data' => []
            ];

            return response()->json($response, 500);

        } catch(Exception $e) {
            Log::error($e);
            DB::rollback();

            $response = [
                'code' => 500,
                'message' => 'Internal Server Error',
                'data' => []
            ];

            return response()->json($response, 500);
        }

        // response user with token
        $response = [
            'code' => 200,
            'message' => 'Login Success',
            'data' => $user
        ];

        return response()->json($response, 200);
    }

    public function logout(Request $request) {
        // get request data
        $id_user = $request->id_user;
        $token = $request->token;
        
        // find user with token 
        $user = User::where('id', $id_user)->where('token', $token)->first();

        // if user not found
        if (!$user) {
            $response = [
                'code' => 404,
                'message' => 'User Not Found',
                'data' => []
            ];
            return response()->json($response, 404);
        }

        // if user found

        // clear login token, set as null value
        try {
            DB::beginTransaction();

            $user->token = null;
            $user->save();

            DB::commit();
        } catch(QueryException $qe) {
            Log::error($qe);
            DB::rollback();

            $response = [
                'code' => 500,
                'message' => 'Internal Server Error',
                'data' => []
            ];

            return response()->json($response, 500);

        } catch(Exception $e) {
            Log::error($e);
            DB::rollback();

            $response = [
                'code' => 500,
                'message' => 'Internal Server Error',
                'data' => []
            ];

            return response()->json($response, 500);
        }

        // response user with token
        $response = [
            'code' => 2,
            'message' => 'Logout Success',
            'data' => $user
        ];

        return response()->json($response, 200);
    }
}
