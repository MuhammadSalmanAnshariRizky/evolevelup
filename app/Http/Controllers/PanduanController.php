<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
class PanduanController extends Controller
{
    public function panduanguru(): View
    {
        return view('panduan.panduan-guru');
    }

    public function panduansiswa(): View
    {
        return view('panduan.panduan-siswa');
    }
}
