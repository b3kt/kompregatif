<?php

namespace App\Http\Controllers\Voyager;

use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Http\Controllers\VoyagerController as BaseVoyagerController;
use TCG\Voyager\Facades\Voyager;

class VoyagerController extends BaseVoyagerController
{
    public function index()
    {
        return Voyager::view('voyager::index');
    }

    public function logout(){
        Auth::logout();
    }
}
