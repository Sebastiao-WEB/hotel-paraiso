<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RoomsController extends Controller
{
    public function index()
    {
        return view('public.rooms');
    }

    public function show(Request $request, $id)
    {
        return view('public.room-detail', ['id' => $id]);
    }
}

