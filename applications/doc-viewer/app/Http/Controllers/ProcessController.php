<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProcessController extends Controller
{
    public function index()
    {
        return response()->json(['data' => []]);
    }

    public function store(Request $request)
    {
        return response()->json(['message' => 'Process created']);
    }

    public function show($id)
    {
        return response()->json(['data' => ['id' => $id]]);
    }

    public function update(Request $request, $id)
    {
        return response()->json(['message' => "Process {$id} updated"]);
    }

    public function destroy($id)
    {
        return response()->json(['message' => "Process {$id} deleted"]);
    }
}

