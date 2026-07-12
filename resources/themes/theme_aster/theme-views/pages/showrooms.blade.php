@extends('theme-views.layouts.app')

@section('title', 'معارض ذرة أكسجين | ' . $web_config['company_name'])

@section('content')
<main class="main-content">

    <div class="showrooms-hero">
        <div class="showrooms-hero-content">
            <span class="hero-pin-icon"><i class="fa fa-map-marker"></i></span>
            <h1>معارض ذرة أكسجين</h1>
            <p>كل لوازم بيتك في مكان واحد لتسوق بلا حدود</p>
        </div>
    </div>

    <div class="showrooms-section">
        <div class="container">

            <div class="section-title-bar">
                <h2>المعارض</h2>
                <span class="bar"></span>
            </div>

            <div class="row g-4">
                @foreach($showrooms as $showroom)
                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6">
                        <div class="showroom-card h-100">
                            <div class="showroom-city">
                                <i class="fa fa-building ms-1 text-muted" style="font-size:0.9rem;"></i> 
                                {{ $showroom->city_name }}
                            </div>
                            <div class="showroom-info-row">
                                <i class="fa fa-map-marker"></i>
                                <span>{{ $showroom->address }}</span>
                            </div>
                            <div class="showroom-info-row">
                                <i class="fa fa-map"></i>
                                <a href="{{ $showroom->maps_url }}" target="_blank">عرض على الخريطة</a>
                            </div>
                            <div class="showroom-info-row">
                                <i class="fa fa-phone"></i>
                                <div class="showroom-phones">{{ $showroom->phone }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</main>
@endsection
