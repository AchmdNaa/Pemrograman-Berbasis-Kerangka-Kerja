@extends('layouts.app')
@section('title', 'Edit Siswa')
@section('content')
<h1 class="h4 fw-bold mb-3">Edit Siswa <span class="text-muted fw-normal">· {{ $siswa->nama }}</span></h1>
@if ($errors->any())<div class="alert alert-danger"><i class="bi bi-x-octagon"></i> Ada {{ $errors->count() }} isian yang perlu diperbaiki.</div>@endif
<form action="{{ route('siswa.update', $siswa) }}" method="POST" enctype="multipart/form-data" novalidate>
    @method('PUT')
    @include('siswa._form')
</form>
@endsection
