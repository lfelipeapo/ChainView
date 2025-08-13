<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DcCategoryController extends Controller
{
    public function index()
    {
        $categories = DB::table('dccategory')->get();
        return view('dashboard', compact('categories'));
    }

    public function show($id)
    {
        $category = DB::table('dccategory')->find($id);
        $attributes = DB::select("
                            SELECT
                                cat.nmcategory,
                                cat.idcategory,
                                cat.fglogo,
                                doc_path.path,
                                doc_attrib.numero_doc,
                                doc_attrib.nmuserupd,
                                doc_attrib.cdrevision,
                                attrib.nmattribute,
                                attrib.nmlabel
                                FROM
                                dccategory cat
                                INNER JOIN dccatdocattrib cat_doc_attrib
                                ON cat.cdcategory = cat_doc_attrib.cdcategory
                                INNER JOIN dcdocumentattrib doc_attrib
                                ON cat_doc_attrib.cdcategory = doc_attrib.cdcategory
                                AND cat_doc_attrib.cdattribute::integer = doc_attrib.cdattribute
                                INNER JOIN adattribute attrib
                                ON attrib.cdattribute = doc_attrib.cdattribute
                                INNER JOIN dcdocumentpath doc_path
                                ON doc_path.doc_id = doc_attrib.id
                                WHERE
                                cat.cdcategory = :id
                                ORDER BY
                                cat.cdcategory,
                                attrib.nmlabel,
                                doc_attrib.numero_doc;", [$id]);
        return view('categories.show', compact('category', 'attributes'));
    }
}
