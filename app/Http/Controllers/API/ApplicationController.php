<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Exception;
use Carbon\Carbon;

class ApplicationController extends Controller
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
    public function index()
    {
        $applications = Application::all();

        return $applications;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $message = $this->specifyRole($request, 'candidate');
        if ($message !== 'Matched') {
            return new JsonResponse($message, 401);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'phoneNumber' => 'required',
            'resume' => 'required|file|mimes:pdf,doc,docx,odt',
            'job_listings_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $user = $request->user();
        try {
            if ($request->hasFile('resume')) {
                $uploadedFile = cloudinary()->upload($request->file('resume')->getRealPath());
                $application = new Application();
                $application->email = $request->input('email');
                $application->phoneNumber = $request->input('phoneNumber');
                $application->job_listings_id = $request->input('job_listings_id');
                $application->resume = $uploadedFile->getSecurePath();
                $application->user_id = $user->id;
                $application->save();
                return $application;
            } else {
                return response()->json(['message' => 'Resume file is required'], 415);
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'Error while storing the application', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Application $application)
    {
        return $application;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Application $application)
    {
        $hasRequiredField = $request->hasAny(['email', 'phoneNumber','status']) || $request->hasFile('resume');

        if (!$hasRequiredField) {
            return response()->json(['errors' => 'At least one of email, phoneNumber, or resume must be provided.'], 422);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'nullable|email',
            'phoneNumber' => 'nullable|string',
            'resume' => 'file|mimes:pdf,doc,docx,odt',
            'status' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            if ($request->has('email')) {
                $application->email = $request->input('email');
            }
            if ($request->has('phoneNumber')) {
                $application->phoneNumber = $request->input('phoneNumber');
            }
            if ($request->hasFile('resume')) {
                $uploadedFile = cloudinary()->upload($request->file('resume')->getRealPath());
                $application->resume = $uploadedFile->getSecurePath();
            }
            if ($request->has('status')) {
                $application->status = $request->input('status');
            }
            $application->save();

            return response()->json(['message' => 'The application has been updated', 'data' => $application], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update the application', 'error' => $e->getMessage()], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Application $application)
    {
        $application->delete();
        return response()->json(['message' => "the application deleted", 'status_code' => 204], 204);
    }

    public function getJobApplications($jobListingsId){
        try {
            $applications = Application::where('job_listings_id', $jobListingsId)->get();
    
            return response()->json(['applications' => $applications], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve applications', 'error' => $e->getMessage()], 500);
        }
    }

    public function getUserApplications($userId){
        try {
            $applications = Application::where('user_id', $userId)->get();
    
            return response()->json(['applications' => $applications], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve applications', 'error' => $e->getMessage()], 500);
        }
    }

}
