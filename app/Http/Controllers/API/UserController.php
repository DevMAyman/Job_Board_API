<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
//_________________________________get_all_users____________________________________

    public function index()
    {
        $users=User::all();
        return $users;
    }

//_________________________________register_user____________________________________

    public function store(Request $request)
    {
        $user = new User();
    
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'));
        $user->role = $request->input('role');
        $user->save();
        return response()->json(['message' => 'User created successfully'], 201);
    }
    
//_________________________________display_specific_user____________________________________

    public function show(User $user)
    {
        //
    }

//_________________________________update_specific_user____________________________________

    public function update(Request $request, User $user)
    {
        //
    }

//_________________________________delete_specific_user____________________________________

    public function destroy(User $user)
    {
        //
    }
//_________________________________delete_all_users____________________________________

    public function deleteAllUsers(Request $request){
        User::truncate();
        return 'all users deleted seuccessfylly !! ';
    } 

//_________________________________login_return_token____________________________________

    public function login(Request $request)
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            $token = $user->createToken('api_token')->plainTextToken;
            return response()->json([
                'message' => 'Authenticated Succeed , Now You Are Logged In',
                'token' => $token,
                'name' => $user->name 
            ]);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

//_________________________________logout_delete_token____________________________________

}
