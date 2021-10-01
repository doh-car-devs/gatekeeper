<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserAccessController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function store($id)
    {
        $user = User::find($id);

        if ($user === null) {
            return response()->json('User not found.', 404);
        }

        $user->status = 'active';
        $user->save();

        return response()->json('Use has been activated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if ($user === null) {
            return response()->json('User not found.', 404);
        }

        $user->status = 'inactive';
        $user->save();

        return response()->json('Use has been deactivated');
    }
}
