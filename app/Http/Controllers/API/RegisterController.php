<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Http\JsonResponse;
   
class RegisterController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    
    public function specifyRole($request, $role) {
        $currentRequestPersonalAccessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($request->bearerToken());
        if ($currentRequestPersonalAccessToken) {
            $userRole = $currentRequestPersonalAccessToken->tokenable->role;
            var_dump($role,$userRole);
            if ($role !== $userRole) {
                return "You are not $userRole to access that! 😏";
            }
        } else {
            return "You must send token";
        }
        return 'Matched';
    }
    public function register(Request $request): JsonResponse
{
    try {
        $currentRequestPersonalAccessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($request->bearerToken());
        if($currentRequestPersonalAccessToken){
            $role = $currentRequestPersonalAccessToken->tokenable->role;
        if ($role != 'admin' && $request->role == 'admin') {
            return new JsonResponse('You are not an admin to add admin !😏');
        }
        }
        

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = null;
        try {
            $user = User::create($input);
        } catch (\Exception $e) {
    if (strpos($e->getMessage(), '1062 Duplicate entry') !== false) {
        return $this->sendError('Email is used before');
    } else {
        return $this->sendError('User creation failed.');
    }
}
        $success['token'] = $user->createToken('MyApp')->plainTextToken;
        $success['name'] = $user->name;

        return $this->sendResponse($success, 'User registered successfully.');
    } catch (\Exception $e) {
        return $this->sendError('An error occurred.', ['error' => $e->getMessage()]);
    }
}

   
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request): JsonResponse
    {
        // $message = $this->specifyRole($request,'employer');
        // if( $message!=='Matched'){ return new JsonResponse($message,401);}
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('MyApp')->plainTextToken; 
            $success['name'] =  $user->name;
   
            return $this->sendResponse($success, 'User login successfully.');
        } 
        else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        } 
    }


}