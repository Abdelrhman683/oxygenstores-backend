@extends('layouts.front-end.app')

@section('title', translate('our_stores') ?? 'معارضنا')

@section('content')
    <div class="container py-5 rtl text-align-direction">
        <div class="row">
            <div class="col-md-12 text-center mb-5">
                <h1 class="h2 mb-3 headerTitle" style="color: var(--web-primary);">{{ translate('our_stores') ?? 'معارضنا' }}</h1>
                <hr class="mx-auto" style="width: 80px; height: 3px; background-color: var(--web-primary); border: 0;">
            </div>
            
            <div class="col-md-12 text-center">
                <div class="p-5 border rounded bg-white shadow-sm">
                    <i class="fa fa-map-marker text-muted mb-3" style="font-size: 3rem;"></i>
                </div>
            </div>
        </div>
    </div>
@endsection
