<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserFormRequest;
use App\Http\Requests\UpdateUserFormRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
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
        $users = User::query()
            ->with('office')
            ->with('roles');

        if ($request->input('keyword')) {
            $users->whereRaw('MATCH (given_name, middle_name, last_name, name_suffix, username)
                    AGAINST (? IN BOOLEAN MODE)', $request->input('keyword').'*'
                )
                ->orWhere('status', 'LIKE', '%'.$request->input('keyword').'%')
                ->orWhereHas('roles', function ($query) use ($request) {
                    $query->where('name', 'LIKE', '%'.$request->input('keyword').'%');
                })
                ->orWhereHas('office', function ($query) use ($request) {
                    $query->where('name', 'LIKE', '%'.$request->input('keyword').'%');
                });
        }

        $sort_by = [
            'name_up' => 'last_name ASC, given_name ASC, middle_name ASC, name_suffix ASC',
            'name_down' => 'last_name DESC, given_name DESC, middle_name DESC, name_suffix DESC',
            'username_up' => 'username ASC',
            'username_down' => 'username DESC',
        ];

        $users = $users->orderByRaw($sort_by[$request->input('sort', 'name_up')])
            ->paginate($request->input('per_page', 25))
            ->appends($request->input());

        if ($users->isNotEmpty()) {
            return response()->json($users);
        } else {
            return response()->json('No users found.', 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateUserFormRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateUserFormRequest $request)
    {
        try {
            DB::beginTransaction();

            $user = User::query()
                ->create($request->validated());

            if ($request->validated()['roles']) {
                $user->roles()->sync(explode(',', $request->validated()['roles']));
            }

            DB::commit();

            return response()->json($user);
        } catch (\Exception $e) {
            DB::rollBack();
            logger($e);
            return response()->json('Unable to create user. Please try again later.', 500);
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
        $user = User::query()
            ->where('id', $id)
            ->with('employee')
            ->with('roles')
            ->first();

        if ($user === null) {
            return response()->json('User not found.', 404);
        } else {
            return response()->json($user);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateUserFormRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateUserFormRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $user = User::find($id);

            if ($user === null) {
                return response()->json('User not found.', 404);
            }

            $user->fill(array_filter($request->validated()))->save();

            if ($request->input('roles')) {
                $user->roles()->sync(explode(',', $request->input('roles')));
            } else {
                $user->roles()->detach();
            }

            DB::commit();

            return response()->json('User has been updated.');
        } catch (\Exception $e) {
            DB::rollBack();
            logger($e);
            return response()->json('Unable to update user. Please try again later.', 500);
        }
    }
}
