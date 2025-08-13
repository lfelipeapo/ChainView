<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProcessController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return DB::table('processes')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $id = DB::table('processes')->insertGetId($request->all());
        return DB::table('processes')->find($id);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return DB::table('processes')->find($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        DB::table('processes')->where('id', $id)->update($request->all());
        return DB::table('processes')->find($id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::table('processes')->where('id', $id)->delete();
        return response()->noContent();
    }

    /**
     * Return the full process tree starting at the given id.
     */
    public function tree($id)
    {
        return $this->buildTree($id);
    }

    protected function buildTree($id)
    {
        $process = DB::table('processes')->find($id);
        if (! $process) {
            return null;
        }

        $children = DB::table('processes')->where('parent_id', $id)->get();
        foreach ($children as $child) {
            $child->children = $this->buildTree($child->id);
        }
        $process->children = $children;

        return $process;
    }
}
