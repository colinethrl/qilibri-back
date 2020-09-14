<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{


    public function signUp(Request $request) {
        if ($request->email) {
            $otherUser = User::where('email',$request->email)->first();
            if ($otherUser instanceof User) {
                return response("username_already", 400);
            }
            return User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]);
        } else {
            return response('Missing email');
        }
    }

    public function login(Request $request) {
        $user = User::where('email', $request->email)->first();
        if ($user instanceof User) {
            if ($user->password === $request->password) {
                return $user;
            } else {
                return response('Wrong password', 403);
            }
        } else {
            return response('This user does not exist', 403);
        }
    }

    public function getUsers() {
        $users = User::get();

        return response()->json($users);
    }
}
