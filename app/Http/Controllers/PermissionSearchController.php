<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionSearchController extends Controller
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
        $permissions = Permission::query();

        if ($request->input('keyword')) {
            $permissions->where('name', 'LIKE', '%'.$request->input('keyword').'%');
        }

        if ($request->input('only')) {
            $permissions->whereIn('id', explode(',', $request->input('only')));
        }

        if ($request->input('except')) {
            $permissions->whereNotIn('id', explode(',', $request->input('except')));
        }

        $permissions = $permissions->orderBy('name')
            ->limit(5)
            ->offset(0)
            ->get();

        if ($permissions->isNotEmpty()) {
            return response()->json($permissions);
        } else {
            return response()->json('Permissions not found.', 404);
        }
    }
}
