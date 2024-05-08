<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\JobListing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JobListingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jobs = jobListing::all();
        return $jobs;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), JobListing::$rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $jobListing = JobListing::create($request->all());

        return response()->json($jobListing, 201);
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

        // Update the job listing with the validated data
        $job->fill($request->all());
        $job->save();

        // Return the updated job listing
        return response()->json($job, 200);
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
