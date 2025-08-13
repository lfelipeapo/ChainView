<?php

namespace App\Http\Controllers;

use App\Models\Tool;
use Illuminate\Http\Request;

class ToolController extends Controller
{
    public function index()
    {
        return Tool::all();
    }

    public function store(Request $request)
    {
        return Tool::create($request->all());
    }

    public function show($id)
    {
        return Tool::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $tool = Tool::findOrFail($id);
        $tool->update($request->all());
        return $tool;
    }

    public function destroy($id)
    {
        Tool::destroy($id);
        return response()->noContent();
    }
}
