<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\JobListing;
use App\Utilities\QueryParamHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;


class JobListingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = JobListing::query();

        $searchFields = ['title', 'description', 'location'];

        $result = QueryParamHandler::handle($query, $request->all(), $searchFields);

        return response()->json($result);
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

        try {
            $input = $request->all();
            $input['user_id'] = auth()->user()->id;
            // dd($input);
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
