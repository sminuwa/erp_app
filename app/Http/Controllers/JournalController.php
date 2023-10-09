<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use Illuminate\Http\Request;

class JournalController extends Controller
{
    //
    public function index(){
        $journals = Journal::orderBy('id', 'desc')->get();
        return view('pages.journals.index', compact('journals'));
    }

    public function store(Request $request){

    }

    public function create(Request $request){

        return view('pages.journals.create');
    }




}
