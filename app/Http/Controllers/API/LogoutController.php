<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Http\JsonResponse;
   
class LogoutController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
  
   
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request){
        $user = $request->user;
        $user->tokens()->delete();
        return response()->noContent();
    }
}