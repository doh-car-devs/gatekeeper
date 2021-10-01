<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->only('show', 'destroy');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $user = User::query()
            ->with('roles.permissions')
            ->where('username', $request->input('username'))
            ->where('status', 'active')
            ->first();

        if ($user === null) {
            return response()->json('User not found.', 404);
        }

        if (Hash::check($request->input('password'), $user->password) === false) {
            return response()->json('Incorrect credentials.', 401);
        }

        $permissions = [];

        foreach($user->roles AS $role) {
            foreach ($role->permissions AS $permission) {
                $permissions[] = $permission->name;
            }
        }

        $user->tokens()->delete();

        return response()->json([
            'token' => $user->createToken($user->username, $permissions)->plainTextToken,
            'token_scope' => $permissions,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show()
    {
        $user = User::query()
            ->where('id', Auth::id())
            ->with('roles')
            ->first();

        $permissions = [];

        foreach($user->roles AS $role) {
            foreach ($role->permissions AS $permission) {
                $permissions[] = $permission->name;
            }
        }

        return response()->json([
            'user' => $user,
            'token_scope' => $permissions,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy()
    {
        Auth::user()->tokens()->delete();

        return response()->json('Logged out.');
    }
}
