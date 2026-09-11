@extends('layouts.main')

@section('panduanSiswa', request()->is('panduan-siswa') ? 'active' : '')

@section('content')
<div class="container py-4">
    <h1 class="h3 font-weight-bold mb-4 text-primary">Panduan Siswa</h1>

    <div class="card border-0 shadow-sm rounded-lg overflow-hidden">
        <div class="card-body p-0">
            <div style="position:relative; padding-top:max(60%,324px); width:100%; height:0;">
                <iframe 
                    style="position:absolute; border:none; width:100%; height:100%; left:0; top:0;" 
                    src="https://online.fliphtml5.com/yugxd/Panduan-Peserta-didik/" 
                    title="Panduan Siswa" 
                    seamless="seamless" 
                    scrolling="no" 
                    frameborder="0" 
                    allowtransparency="true" 
                    allowfullscreen="true">
                </iframe>
            </div>
        </div>
    </div>
</div>
@endsection