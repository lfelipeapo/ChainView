<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function index()
    {
        return response()->json(['data' => []]);
    }

    public function store(Request $request)
    {
        return response()->json(['message' => 'Area created']);
    }

    public function show($id)
    {
        return response()->json(['data' => ['id' => $id]]);
    }

    public function update(Request $request, $id)
    {
        return response()->json(['message' => "Area {$id} updated"]);
    }

    public function destroy($id)
    {
        return response()->json(['message' => "Area {$id} deleted"]);
    }

    public function tree()
    {
        return response()->json(['data' => []]);
    }
}

