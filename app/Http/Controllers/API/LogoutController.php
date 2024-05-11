<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;

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
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();

        return response()->noContent();
    }
}
