<?php

namespace App\Http\Controllers;

use App\Models\Office;
use Illuminate\Http\Request;

class OfficeController extends Controller
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
        $offices = Office::query();

        if ($request->input('keyword')) {
            $offices->whereRaw('MATCH (name, short_name) AGAINST (? IN BOOLEAN MODE)', $request->input('keyword').'*');
        }

        if ($request->input('sort')) {
            $sort_by = [
                'name_up' => 'name ASC',
                'name_down' => 'name DESC',
            ];

            $offices->orderByRaw($sort_by[$request->input('sort')]);
        }

        $offices = $offices->paginate($request->input('per_page', 25))
            ->appends($request->input());

        if ($offices->isNotEmpty()) {
            return response()->json($offices);
        } else {
            return response()->json('No offices found.', 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
