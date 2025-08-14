<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return DB::table('areas')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $id = DB::table('areas')->insertGetId($request->all());
        return DB::table('areas')->find($id);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return DB::table('areas')->find($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        DB::table('areas')->where('id', $id)->update($request->all());
        return DB::table('areas')->find($id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::table('areas')->where('id', $id)->delete();
        return response()->noContent();
    }

    /**
     * Return the process tree for the given area.
     */
    public function processesTree($id)
    {
        $area = DB::table('areas')->find($id);
        if (! $area) {
            return null;
        }

        $area->processes = $this->areaProcesses($id);
        return $area;
    }

    protected function areaProcesses($areaId, $parentId = null)
    {
        $query = DB::table('processes')->where('area_id', $areaId);
        if ($parentId === null) {
            $query->whereNull('parent_id');
        } else {
            $query->where('parent_id', $parentId);
        }

        $processes = $query->get();
        foreach ($processes as $process) {
            $process->children = $this->areaProcesses($areaId, $process->id);
        }

        return $processes;
    }
}
