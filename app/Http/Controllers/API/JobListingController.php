<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\JobListing;
use App\Utilities\QueryParamHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\API\RegisterController;
use Exception;
use Illuminate\Support\Facades\Log;

class JobListingController extends Controller
{
    public function specifyRole($request, $role)
    {
        $currentRequestPersonalAccessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($request->bearerToken());
        if ($currentRequestPersonalAccessToken) {
            $userRole = $currentRequestPersonalAccessToken->tokenable->role;
            var_dump($role, $userRole);
            if ($role !== $userRole) {
                return "You are not $userRole to access that! 😏";
            }
        } else {
            return "You must send token";
        }
        return 'Matched';
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = JobListing::query();

        $result = QueryParamHandler::handle($query, $request->all());

        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $message = $this->specifyRole($request, 'employer');
        if ($message !== 'Matched') {
            return new JsonResponse($message, 401);
        }

        $validator = Validator::make($request->all(), JobListing::$rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $input = $request->all();
            $user = $request->user();

            // if (!$user) {
            //     return response()->json(['error' => 'User not authenticated'], 401);
            // }
            $input['user_id'] = $user->id;
            if ($request->hasFile('logo')) {
                $uploadedFile = cloudinary()->upload($request->file('logo')->getRealPath());
                $input['logo'] = $uploadedFile->getSecurePath(); // Assigning the logo path to the 'logo' field in $input array
            }
            $jobListing = JobListing::create($input);

            return response()->json($jobListing, 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error while storing the application', 'error' => $e->getMessage()], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(jobListing $job)
    {
        return $job;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, jobListing $job)
    {
        $validator = Validator::make($request->all(), JobListing::$rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        try {
            $input = $request->all();
            if ($request->hasFile('logo')) {
                $uploadedFile = cloudinary()->upload($request->file('logo')->getRealPath());
                $input['logo'] = $uploadedFile->getSecurePath(); // Assigning the logo path to the 'logo' field in $input array
            }
            $job->fill($input);
            $job->save();
            return response()->json($job, 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error while storing the application', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(jobListing $job)
    {
        $job->delete();

        return response()->json(['message' => 'Job listing deleted successfully'], 200);
    }
}
