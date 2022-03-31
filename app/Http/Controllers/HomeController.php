<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Group;

class HomeController extends Controller
{
    public function index()
    {
        return Inertia::render('About');
    }

    public function showgroup($id) 
    {
        return Group::where('id', $id)->get();
    }
}
