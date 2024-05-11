<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    //  public function createToken($name, $user_id,array $abilities = ['*'])
    // {
    //     var_dump("Mo");
    //     $token = $this->tokens()->create([
    //         'name' => $name,
    //         'token' => hash('sha256', Str::random(40)),
    //         'abilities' => $abilities,
    //         'user_id' => $user_id, // Assigning the user ID to the token
    //     ]);

    //     return new NewAccessToken($token, $token->id.'|'.$token->token);
    // }
    public function jobListings()
    {
        return $this->hasMany(JobListing::class);
    }
    public function Application(){
        return $this->hasMany(Application::class);
    }
}
