@extends('theme-views.layouts.app')

@section('title', (translate('our_stores') ?? 'معارضنا') . ' | ' . $web_config['company_name'] . ' ' . translate('ecommerce'))

@section('content')
    <main class="main-content d-flex flex-column gap-3 pt-3 mb-sm-5">
        <div class="container rtl text-align-direction">
            <div class="card">
                <div class="card-body p-5">
                    <div class="text-center mb-5">
                        <h2 class="h2 mb-3 text-primary">{{ translate('our_stores') ?? 'معارضنا' }}</h2>
                        <hr class="mx-auto" style="width: 80px; height: 3px; background-color: var(--bs-primary); border: 0;">
                    </div>
                    
                    <div class="text-center py-5">
                        <i class="bi bi-geo-alt-fill text-muted mb-3" style="font-size: 3rem;"></i>
                        <h4 class="text-muted">{{ translate('showrooms_will_be_listed_here') ?? 'سيتم إدراج معارضنا هنا قريباً' }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
