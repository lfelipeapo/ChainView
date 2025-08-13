<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;

class PersonController extends Controller
{
    public function index()
    {
        return Person::all();
    }

    public function store(Request $request)
    {
        return Person::create($request->all());
    }

    public function show($id)
    {
        return Person::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $person = Person::findOrFail($id);
        $person->update($request->all());
        return $person;
    }

    public function destroy($id)
    {
        Person::destroy($id);
        return response()->noContent();
    }
}
