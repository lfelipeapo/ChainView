<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index()
    {
        return Document::all();
    }

    public function store(Request $request)
    {
        return Document::create($request->all());
    }

    public function show($id)
    {
        return Document::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $document = Document::findOrFail($id);
        $document->update($request->all());
        return $document;
    }

    public function destroy($id)
    {
        Document::destroy($id);
        return response()->noContent();
    }
}
