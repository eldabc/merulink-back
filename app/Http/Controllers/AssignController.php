<?php

namespace App\Http\Controllers;

use App\Models\Assign;
use Illuminate\Http\Request;
use App\Http\Resources\AssignResource;

class AssignController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assigns = Assign::all();
        return AssignResource::collection($assigns);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Assign $assign)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Assign $assign)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Assign $assign)
    {
        //
    }
}
