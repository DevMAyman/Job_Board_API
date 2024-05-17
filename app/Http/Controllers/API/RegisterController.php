<?php


namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Validator;

class RegisterController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */

    public function specifyRole($request, $role)
    {
        $currentRequestPersonalAccessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($request->bearerToken());
        if ($currentRequestPersonalAccessToken) {
            $userRole = $currentRequestPersonalAccessToken->tokenable->role;
            var_dump($role, $userRole);
            var_dump($role, $userRole);
            if ($role !== $userRole) {
                return "You are not $userRole to access that! 😏";
            }
        } else {
            return 'You must send token';
        }

        return 'Matched';
    }

    public function register(Request $request): JsonResponse
    {
        try {
            if ($request->role == 'admin') {
                $currentRequestPersonalAccessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($request->bearerToken());
                if ($currentRequestPersonalAccessToken) {
                    $role = $currentRequestPersonalAccessToken->tokenable->role;
                    if ($role != 'admin' && $request->role == 'admin') {
                        return new JsonResponse(['message' => 'You are not an admin to add admin !😏', 'role' => $role], 401);
                    }
                }
                else{
                    return new JsonResponse(['message' => 'You are not an admin to add admin !😏'], 401);
                }
            }

            if ($request->hasFile('image')) {
                $myfile = cloudinary()->upload($request->file('image')->getRealPath());
                $uploadedFile = $myfile->getSecurePath();
            } else {
                $uploadedFile = 'https://static.vecteezy.com/system/resources/previews/008/442/086/non_2x/illustration-of-human-icon-user-symbol-icon-modern-design-on-blank-background-free-vector.jpg';
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email',
                'password' => 'required',
                'c_password' => 'required|same:password',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }

            $input = $request->all();
            $input['password'] = bcrypt($input['password']);
            $input['image'] = $uploadedFile;
            $user = null;        
            try {
                $user = User::create($input);
            } catch (\Exception $e) {
                if (strpos($e->getMessage(), '1062 Duplicate entry') !== false) {
                    return $this->sendError('Email is used before');
                } else {
                    return $this->sendError($e);
                }
            }
            $success['token'] = $user->createToken('MyApp')->plainTextToken;
            $success['name'] = $user->name;
            // $success['role'] = $user->role;
            // var_dump($user->role);

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
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('MyApp')->plainTextToken;
            $success['name'] = $user->name;
            $success['role'] = $user->role;

            return $this->sendResponse($success, 'User login successfully.');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
        }
        } 
    }
