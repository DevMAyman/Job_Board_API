<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use Exception;
use Carbon\Carbon;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $applications = Application::all();
        return $applications;
    }

    public function pollForUpdates(Request $request)
    {
        // Retrieve the 'lastModified' parameter from the client
        $lastModified = $request->input('lastModified', 0);

        // Get the latest update time for the applications
        $latestUpdate = Application::orderBy('updated_at', 'desc')->first();
        $latestModifiedTime = $latestUpdate ? $latestUpdate->updated_at->timestamp : 0;
        // dd($latestModifiedTime);
        // Hold the connection until an update occurs or a timeout is reached
        $timeout = 60; // Set a timeout in seconds
        $startTime = time();

        while (time() - $startTime < $timeout) {
            // Check if the data has been updated since the last client check
            if ($latestModifiedTime > $lastModified) {
                // Retrieve the updated application(s)
                $updatedApplications = Application::where('updated_at', '>', Carbon::createFromTimestamp($lastModified, 'UTC'))->get();
                // dd($updatedApplications);
                // Prepare the response data
                $response = [
                    'status' => 'update',
                    'applications' => $updatedApplications,
                    'server_time' => $latestModifiedTime,
                ];

                return Response::json($response,200);
            }

            // Sleep briefly to avoid high CPU usage
            sleep(3);

            // Recheck the latest update time
            $latestUpdate = Application::orderBy('updated_at', 'desc')->first();
            $latestModifiedTime = $latestUpdate ? $latestUpdate->updated_at->timestamp : 0;
        }

        // If no update within the timeout, return a no-change response
        return Response::json([
            'status' => 'no_change',
            'server_time' => $latestModifiedTime,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'phoneNumber' => 'required',
            'resume' => 'required|file|mimes:pdf,doc,docx,odt'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        try {
            if ($request->hasFile('resume')) {
                $uploadedFile = cloudinary()->upload($request->file('resume')->getRealPath());
                $application = new Application();
                $application->email = $request->input('email');
                $application->phoneNumber = $request->input('phoneNumber');
                $application->resume = $uploadedFile->getSecurePath();
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
        $hasRequiredField = $request->hasAny(['email', 'phoneNumber']) || $request->hasFile('resume');

        if (!$hasRequiredField) {
            return response()->json(['errors' => 'At least one of email, phoneNumber, or resume must be provided.'], 422);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'nullable|email',
            'phoneNumber' => 'nullable|string',
            'resume' => 'file|mimes:pdf,doc,docx,odt',
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
}
