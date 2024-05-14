<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;
    protected $fillable = ['email','phoneNumber','resume','status','user_id','job_listings_id'];
    public function users(){
        return $this->belongsTo(User::class);
    }
    public function job_listings(){
        return $this->belongsTo(JobListing::class,'job_listings_id');
    }
}
