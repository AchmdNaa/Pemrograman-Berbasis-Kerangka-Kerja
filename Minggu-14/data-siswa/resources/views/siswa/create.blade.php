@extends('layouts.app')
@section('title', 'Tambah Siswa')
@section('content')
<h1 class="h4 fw-bold mb-3">Tambah Siswa</h1>
@if ($errors->any())<div class="alert alert-danger"><i class="bi bi-x-octagon"></i> Ada {{ $errors->count() }} isian yang perlu diperbaiki.</div>@endif
<form action="{{ route('siswa.store') }}" method="POST" enctype="multipart/form-data" novalidate>
    @include('siswa._form')
</form>
@endsection
