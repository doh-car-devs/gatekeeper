<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePermissionFormRequest;
use App\Http\Requests\UpdatePermissionFormRequest;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
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
        $permissions = Permission::query()
            ->withCount('roles');

        if ($request->input('keyword')) {
            $permissions->whereRaw('MATCH (name, description) AGAINST (? IN BOOLEAN MODE)',
                $request->input('keyword').'*'
            );
        }

        $sort_by = [
            'name_up' => 'name ASC',
            'name_down' => 'name DESC',
        ];

        $permissions = $permissions->orderByRaw($sort_by[$request->input('sort', 'name_up')])
            ->paginate($request->input('per_page', 25))
            ->appends($request->input());

        if ($permissions->isNotEmpty()) {
            return response()->json($permissions);
        } else {
            return response()->json('Permissions not found.', 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreatePermissionFormRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreatePermissionFormRequest $request)
    {
        try {
            $permission = Permission::create($request->validated());

            return response()->json($permission);
        } catch (\Exception $e) {
            logger($e);
            return response()->json('Unable to create permission. Please try again later.', 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $permission = Permission::query()
            ->where('id', $id)
            ->withCount('roles')
            ->first();

        if ($permission === null) {
            return response()->json('Permissions not found.', 404);
        } else {
            return response()->json($permission);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdatePermissionFormRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdatePermissionFormRequest $request, $id)
    {
        try {
            $permission = Permission::find($id);
            $permission->fill($request->validated())->save();
            return response()->json('Permission has been updated.');
        } catch (\Exception $e) {
            return response()->json('Unable to update permission. Please try again later.', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $permission = Permission::find($id);

            if ($permission === null) {
                return response()->json('Permission not found.', 404);
            }

            $permission->roles()->detach();
            $permission->delete();

            return response()->json('Permission has been deleted.');
        } catch (\Exception $e) {
            logger($e);
            return response()->json('Unable to delete permission. Please try again later.', 500);
        }
    }
}
