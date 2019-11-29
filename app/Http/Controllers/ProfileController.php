<?php

namespace App\Http\Controllers;

use App\User;
use App\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProfileController extends Controller
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

    public function profile($id_user) {
        // find user  
        $user = User::find($id_user);

        // find user profile
        $profile = UserProfile::where('user_id', $id_user)->first();

        // response
        $response = [
            'code' => 200,
            'message' => 'User Profile',
            'data' => [
                'user' => $user,
                'profile' => $profile,
            ]
        ];
        return response()->json($response, 200);
    }

    public function updateProfile(Request $request, $id_user) {
        // find user
        $user = User::find($id_user);

        // find user profile
        $profile = UserProfile::where('user_id', $id_user)->first();

        // if profile not found, create new profile
        if (!$profile) {
            $profile = new UserProfile();
            $profile->user_id = $id_user;
            $profile->save();
        }

        // Update Profile : save avatar image
        $avatar = null;

        if ($request->file('avatar')) {
            // delete image, when not null
            if ($profile->avatar) {
                $current_avatar_path = storage_path('avatar').'/'. $profile->avatar;
                if (file_exists($current_avatar_path)) {
                    unlink($current_avatar_path);
                }
            }

            // upload image
            $avatar = $user->username.'.jpg';
            $request->file('avatar')->move(storage_path('avatar'), $avatar);
        }

        // Update Profile : update data
        try {
            DB::beginTransaction();

            $profile->avatar = $avatar;
            $profile->first_name = $request->first_name;
            $profile->last_name = $request->last_name;
            $profile->gender = $request->gender;
            $profile->save();

            DB::commit();
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

        // response user profile
        $response = [
            'code' => 200,
            'message' => 'Update Profile Success',
            'data' => $profile
        ];

        return response()->json($response, 200);
    }

}
