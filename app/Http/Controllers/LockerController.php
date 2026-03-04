<?php

namespace App\Http\Controllers;

use App\Models\Locker;
use Illuminate\Http\Request;

class LockerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return "Index";
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return "Store";
    }

    /**
     * Display the specified resource.
     */
    public function show(Locker $locker)
    {
        return "Show";
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Locker $locker)
    {
        return "Update";
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Locker $locker)
    {
        return "Destroy";
    }
}
