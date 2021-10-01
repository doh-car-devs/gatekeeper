<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleSearchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $roles = Role::query()
            ->where('name', '!=', 'System Administrator');

        if ($request->input('keyword')) {
            $roles->whereRaw('MATCH (name, description) AGAINST (? IN BOOLEAN MODE)', $request->input('keyword').'*');
        }

        if ($request->input('only')) {
            $roles->whereIn('id', explode(',', $request->input('only')));
        }

        if ($request->input('except')) {
            $roles->whereNotIn('id', explode(',', $request->input('except')));
        }

        $roles = $roles->orderBy('name')
            ->limit(5)
            ->offset(0)
            ->get();

        if ($roles->isNotEmpty()) {
            return response()->json($roles);
        } else {
            return response()->json($request->input(), 404);
        }
    }
}
