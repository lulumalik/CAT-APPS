@extends('pdf.registration-forms.layout')

@section('content')
@php
    $f = $fields;
@endphp

@foreach ($pages as $page)
    <div class="page">
        @include($page['view'], compact('fields', 'line', 'institution', 'course', 'printed_at'))
    </div>
@endforeach
@endsection
