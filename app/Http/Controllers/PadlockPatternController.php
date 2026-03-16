<?php

namespace App\Http\Controllers;

use App\Models\PadlockPattern;
use Illuminate\Http\Request;
use App\Http\Resources\PadlockPatternResource;

class PadlockPatternController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $patthers = PadlockPattern::all();
        return PadlockPatternResource::collection($patthers);
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
    public function show(PadlockPattern $padlockPattern)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PadlockPattern $padlockPattern)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PadlockPattern $padlockPattern)
    {
        //
    }
}
