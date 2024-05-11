<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobListing extends Model
{
    use HasFactory;
    protected $fillable = [
        'title', 'description', 'responsibilities', 'skills', 'qualifications', 'salary_range', 'benefits', 'location', 'work_type', 'application_deadline', 'logo', 'status'
    ];
    public static $rules = [
        'title' => 'required|max:255',
        'description' => 'required',
        'responsibilities' => 'required',
        'skills' => 'required',
        'qualifications' => 'required',
        'salary_range' => 'required|numeric',
        'benefits' => 'required',
        'location' => 'required|max:100',
        'work_type' => 'required|in:on-site,remote,hybrid',
        'application_deadline' => 'required|date|after:tomorrow',
        'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ];
}
