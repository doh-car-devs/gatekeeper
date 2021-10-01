<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateRoleFormRequest;
use App\Http\Requests\UpdateRoleFormRequest;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
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
            ->with('permissions')
            ->withCount('users');

        if ($request->input('keyword')) {
            $roles->whereRaw('MATCH (name, description) AGAINST (? IN BOOLEAN MODE)', $request->input('keyword').'*');
        }

        $sort_by = [
            'name_up' => 'name ASC',
            'name_down' => 'name DESC',
        ];

        $roles->orderByRaw($sort_by[$request->input('sort', 'name_up')]);

        $roles = $roles->paginate($request->input('per_page'))
            ->appends($request->input());

        if ($roles->isNotEmpty()) {
            return response()->json($roles);
        } else {
            return response()->json('Roles not found.', 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateRoleFormRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateRoleFormRequest $request)
    {
        try {
            DB::beginTransaction();

            $role = Role::query()
                ->create($request->validated());

            if ($request->validated()['permissions']) {
                $role->permissions()->sync(explode(',', $request->validated()['permissions']));
            }

            $role = Role::query()
                ->with('permissions')
                ->withCount('users')
                ->where('id', $role->id)
                ->first();

            DB::commit();

            return response()->json($role);
        } catch (\Exception $e) {
            DB::rollBack();
            logger($e);
            return response()->json('Unable to create role. Please try again later.', 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRoleFormRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRoleFormRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $role = Role::find($id);

            if ($role === null) {
                return response()->json('Role not found.', 404);
            }

            $role->fill($request->validated());
            $role->save();

            if ($request->validated()['permissions']) {
                $role->permissions()->sync(explode(',', $request->validated()['permissions']));
            } else {
                $role->permissions()->detach();
            }

            $role = Role::query()
                ->where('id', $role->id)
                ->with('permissions')
                ->withCount('users')
                ->first();

            DB::commit();

            return response()->json($role);
        } catch (\Exception $e) {
            DB::rollBack();
            logger($e);
            return response()->json('Unable to update role. Please try again later.', 500);
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
            $role = Role::find($id);

            if ($role === null) {
                return response()->json('Role not found.', 404);
            }

            $role->users()->detach();
            $role->permissions()->detach();
            $role->delete();

            return response()->json('Role has been deleted.');
        } catch (\Exception $e) {
            logger($e);
            return response()->json('Unable to delete role. Please try again later.', 500);
        }
    }
}
