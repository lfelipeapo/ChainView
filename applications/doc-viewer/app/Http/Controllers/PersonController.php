<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PersonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return DB::table('people')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $id = DB::table('people')->insertGetId($request->all());
        return DB::table('people')->find($id);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return DB::table('people')->find($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        DB::table('people')->where('id', $id)->update($request->all());
        return DB::table('people')->find($id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::table('people')->where('id', $id)->delete();
        return response()->noContent();
    }
}
