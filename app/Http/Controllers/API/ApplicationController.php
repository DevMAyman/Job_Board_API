<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $application = new Application();
        $application->email = $request->input('email');
        $application->phoneNumber = $request->input('phoneNumber');
        $application->resume = $request->input('resume');
        $application->save();
        return $application;
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
    // Validate the request data
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'phoneNumber' => 'required',
        'resume' => 'required',
    ]);

    // Check if validation fails
    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422); 
    }

    try {
        // Update the application with the request data
        $application->update([
            'email' => $request->input('email'),
            'phoneNumber' => $request->input('phoneNumber'),
            'resume' => $request->input('resume'),
            // Update other fields as needed
        ]);

        return response()->json(['message' => 'The application has been updated', 'data' => $application], 200);
    } catch (\Exception $e) {
        // Handle any exceptions that occur during the update process
        return response()->json(['message' => 'Failed to update the application', 'error' => $e->getMessage()], 500); // Internal Server Error
    }
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Application $application)
    {
        $application->delete();
        return response()->json(['message'=>"the application deleted",'status_code'=>204],204);
    }
}
