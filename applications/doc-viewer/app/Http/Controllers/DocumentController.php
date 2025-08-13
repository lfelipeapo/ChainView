<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index()
    {
        return response()->json(['data' => []]);
    }

    public function store(Request $request)
    {
        return response()->json(['message' => 'Document created']);
    }

    public function show($id)
    {
        return response()->json(['data' => ['id' => $id]]);
    }

    public function update(Request $request, $id)
    {
        return response()->json(['message' => "Document {$id} updated"]);
    }

    public function destroy($id)
    {
        return response()->json(['message' => "Document {$id} deleted"]);
    }
}

