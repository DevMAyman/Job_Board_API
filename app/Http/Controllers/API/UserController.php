<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends BaseController
{
    //_________________________________get_all_users____________________________________

// public function index(Request $request)
// {
//     $role = $request->query('role');

//     // Retrieve users along with their applications based on the role
//     $usersQuery = $role ? User::where('role', $role) : User::query();

//     // Eager load applications with conditions for each status
//     $users = $usersQuery->with(['application' => function ($query) {
//             $query->select('user_id', 'status');
//         }, 'jobListings'])
//         ->paginate(10);

//     // Group applications by status within each user object
//     $users->each(function ($user) {
//         $user->applications = $user->application->groupBy('status');
//     });

//     return $users;
// }

public function index(Request $request)
{
    $role = $request->query('role');

    // Retrieve users along with their applications based on the role
    $usersQuery = $role ? User::where('role', $role) : User::query();

    // Eager load applications with conditions for each status and jobListings
    $users = $usersQuery->with(['application' => function ($query) {
            $query->select('user_id', 'status');
        }, 'jobListings' => function ($query) {
            // Include the count of applications for each job listing
            $query->withCount('application');
        }])
        ->paginate(10);

    // Group applications by status within each user object
    $users->each(function ($user) {
        $user->applications = $user->application->groupBy('status');
    });

    return $users;
}




// public function index(Request $request)
// {
//     $role = $request->query('role');

//     // Retrieve users along with their applications and jobs
//     $users = User::with(['application', 'jobListings'])->get();

//     return $users;
// }



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

public function show(Request $request)
{
    $user = $request->user();

    if (!$user) {
        return response()->json(['error' => 'User not authenticated'], 401);
    }

    return response()->json([
        'name' => $user->name,
        'email' => $user->email,
        'role' => $user->role,
    ], 200);
}


    //_________________________________update_specific_user____________________________________

    public function update(Request $request, User $user): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required',
            'email' => 'sometimes|required|email',
            'password' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error.',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $input = $request->only(['name', 'email']);

        $user->update($input);

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
            $user->save();
        }

        $success['message'] = 'Your Profile is updated successfully.';
        $success['user'] = $user;

        return $this->sendResponse($success, 'Your Profile is updated successfully.');
    }

    //_________________________________delete_specific_user____________________________________

    public function destroy(User $user)
    {
        //
    }
    //_________________________________delete_all_users____________________________________

    public function deleteAllUsers(Request $request)
    {
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
                'name' => $user->name,
                'role' => $user->role,
            ]);
        }

        return response()->json(['error' => 'Email or password is not correct !'], 401);
    }

    //_________________________________logout_delete_token____________________________________

}
