@extends('layouts.front-end.app')

@section('title', $web_config['meta_title'])

@push('css_or_js')
    <link rel="stylesheet" href="{{theme_asset(path: 'public/assets/front-end/css/home.css')}}"/>
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/owl.theme.default.min.css') }}">
@endpush

@section('content')

<?php
    $orderSuccessIds = session('order_success_ids');
    $isNewCustomerInSession = session('isNewCustomerInSession');
    session()->forget('order_success_ids');
    session()->forget('isNewCustomerInSession');
?>
@include("web-views.partials._order-success-modal",['orderSuccessIds' => $orderSuccessIds,'isNewCustomerInSession' => $isNewCustomerInSession])

<div class="__inline-61">
        @php($decimalPointSettings = !empty(getWebConfig(name: 'decimal_point_settings')) ? getWebConfig(name: 'decimal_point_settings') : 0)

        @include('web-views.partials._home-top-slider',['bannerTypeMainBanner'=>$bannerTypeMainBanner])
        @if ($flashDeal['flashDeal'] && $flashDeal['flashDealProducts'] && count($flashDeal['flashDealProducts']) > 0)
            @include('web-views.partials._flash-deal', ['decimal_point_settings'=>$decimalPointSettings])
        @endif

        @if ($featuredProductsList->count() > 0 )
            <div class="container">
                <div class="__inline-62 section-card-margin">
                    <div class="d-flex justify-content-between align-items-baseline px-3 pt-3">
                        <h2 class="feature-product-title font-bold m-0 text-capitalize h5 letter-spacing-0">
                            {{ translate('featured_products') }}
                        </h2>
                        <div class="text-end d-none d-md-block">
                            <a class="view-all-btn-yellow" href="{{ route('featured-products') }}">
                                {{ translate('view_all')}}
                                <!-- <i class="czi-arrow-{{Session::get('direction') === 'rtl' ? 'left mr-1 ml-n1 mt-1' : 'right ml-1'}}"></i> -->
                            </a>
                        </div>
                    </div>
                    <div class="feature-product">
                        <div class="carousel-wrap p-1">
                            <div class="owl-carousel featured_products_listSlide owl-theme" data-slide-items="{{ count($featuredProductsList) }}">
                                @foreach($featuredProductsList as $product)
                                    <div>
                                        @include('web-views.partials._feature-product',['product'=>$product, 'decimal_point_settings'=>$decimalPointSettings])
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="text-center pt-2 d-md-none">
                            <a class="view-all-btn-yellow" href="{{ route('featured-products') }}">
                                {{ translate('view_all') }}
                                <!-- <i class="czi-arrow-{{Session::get('direction') === "rtl" ? 'left mr-1 ml-n1 mt-1' : 'right ml-1'}}"></i> -->
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @include('web-views.partials._category-section-home')
    @include('web-views.partials._recommended-products')


        @if(getFeaturedDealsProductList() && (count(getFeaturedDealsProductList()) > 0))
            <section class="featured_deal pb-3">
                <div class="container">
                    <div class="__featured-deal-wrap bg--light px-0-mobile">
                        <div class="d-flex flex-wrap justify-content-between align-items-sm-start gap-8 mb-xxl-4 mb-3">
                            <div class="w-0 flex-grow-1">
                                <span class="featured_deal_title font-bold text-dark">{{ translate('featured_deal')}}</span>
                                <br>
                                <span class="text-left">{{ translate('see_the_latest_deals_and_exciting_new_offers')}}!</span>
                            </div>
                            <div>
                                <a class="view-all-btn-yellow" href="{{ route('featured-deal-products') }}">
                                    {{ translate('view_all')}}
                                    <i class="czi-arrow-{{Session::get('direction') === 'rtl' ? 'left mr-1 ml-n1 mt-1' : 'right ml-1'}}"></i>
                                </a>
                            </div>
                        </div>
                        <div class="owl-carousel owl-theme new-arrivals-product" data-slide-items="{{ count(getFeaturedDealsProductList()) }}">
                           @foreach(getFeaturedDealsProductList() as $key=>$product)
                                @include('web-views.partials._product-card-1',['product'=>$product, 'decimal_point_settings'=>$decimalPointSettings])
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>
         @endif

        @include('web-views.partials._clearance-sale-products', ['clearanceSaleProducts' => $clearanceSaleProducts])

        @if (isset($bannerTypeMainSectionBanner))
            <div class="container rtl ">
                <a href="{{$bannerTypeMainSectionBanner->url}}" target="_blank"
                    class="cursor-pointer d-block">
                    <img loading="lazy" class="d-block footer_banner_img __inline-63" alt=""
                         src="{{ getStorageImages(path:$bannerTypeMainSectionBanner->photo_full_url, type: 'wide-banner') }}">
                </a>
            </div>
        @endif

        @php($businessMode = getWebConfig(name: 'business_mode'))
        @if ($businessMode == 'multi' && count($topVendorsList) > 0)
            @include('web-views.partials._top-sellers')
        @endif

        @include('web-views.partials._deal-of-the-day', ['decimal_point_settings' => $decimalPointSettings])

        <section class="new-arrival-section">
<!-- 
            @if ($newArrivalProducts->count() >0 )
                <div class="container rtl">
                    <div class="section-header">
                        <h2 class="arrival-title d-block mb-1">
                            <div class="text-capitalize">
                                {{ translate('new_arrivals')}}
                            </div>
                        </h2>
                    </div>
                </div>
                <div class="container rtl mb-3 overflow-hidden">
                    <div class="py-2">
                        <div class="new_arrival_product">
                            <div class="carousel-wrap">
                                <div class="owl-carousel owl-theme new-arrivals-product" data-slide-items="{{ count($newArrivalProducts) }}">
                                    @foreach($newArrivalProducts as $key=> $product)
                                        @include('web-views.partials._product-card-2',['product'=>$product,'decimal_point_settings'=>$decimalPointSettings])
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif -->

            <div class="container rtl custom_pd ">
                <div class="row g-3 mx-max-md-0">

                    @if ($bestSellProduct->count() >0)
                        @include('web-views.partials._best-selling')
                    @endif

                    @if ($topRatedProducts->count() >0)
                        @include('web-views.partials._top-rated')
                    @endif
                </div>
            </div>
    </section>

@include('web-views.partials._air-conditioner-offers', ['airConditionerProducts' => $airConditionerProducts])


        @if (count($bannerTypeFooterBanner) > 1)
            <div class="container rtl ">
                <div class="promotional-banner-slider owl-carousel owl-theme">
                    @foreach($bannerTypeFooterBanner as $banner)
                        <a href="{{ $banner['url'] }}" class="d-block" target="_blank">
                            <img loading="lazy" class="footer_banner_img __inline-63"  alt="" src="{{ getStorageImages(path:$banner->photo_full_url, type: 'banner') }}">
                        </a>
                    @endforeach
                </div>
            </div>
        @elseif (count($bannerTypeFooterBanner) > 0 && count($bannerTypeFooterBanner) == 1)
            <div class="container rtl">
                <div class="row">
                    @foreach($bannerTypeFooterBanner as $banner)
                        <div class="col-md-6">
                            <a href="{{ $banner['url'] }}" class="d-block" target="_blank">
                                <img loading="lazy" class="footer_banner_img __inline-63"  alt="" src="{{ getStorageImages(path:$banner->photo_full_url, type: 'banner') }}">
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        @include('web-views.partials._banner-grid')
@include('web-views.partials._dynamic-category-section', [
    'sectionTitle'    => 'الأكثر مبيعا',
    'sectionProducts' => $bestSellerAllTimeProducts,
])


@include('web-views.partials._dynamic-category-section', [
    'sectionTitle'    => 'الأكثر مبيعا هذا الشهر',
    'sectionProducts' => $bestSellerThisMonthProducts,
])

<section class="custom-banner-section custom_pd">
    <div class="promo-wide-banner">
        <a href="{{ route('products') }}" class="d-block">
            <img
                loading="lazy"
                src="{{ theme_asset('public/assets/front-end/img/promo_ban.webp') }}"
                alt="Promotional Banner"
                class="promo-wide-banner__img"
            >
        </a>
    </div>
</section>

@include('web-views.partials._dynamic-category-section', [
    'sectionTitle'    => 'ثلاجة بابين',
    'sectionProducts' => $twoDoorFridgeProducts,
])

@include('web-views.partials._dynamic-category-section', [
    'sectionTitle'    => 'غسالات',
    'sectionProducts' => $washerProducts,
])


<section class="banner-grid-section custom_pd">
    <div class="container rtl">
        <div class="banner-grid-wrapper">

            <div class="banner-grid-row top-row">

                <div class="banner-grid-card "
                     style="background-image:url('assets/front-end/img/Group-2-1.webp'); background-size:cover; background-position:center;">
                    <div class="banner-card-overlay"></div>
                    <div class="banner-wave-pattern"></div>
                    <div class="banner-card-content">
                        <h3 class="banner-card-title">عروض وخصومات على المكيفات</h3>
                        <p class="banner-card-desc">تسوق الان</p>
                    </div>
                </div>

                <div class="banner-grid-card "
                     style="background-image:url('assets/front-end/img/Group-1-1.webp'); background-size:cover; background-position:center;">
                    <div class="banner-card-overlay"></div>
                    <div class="banner-wave-pattern"></div>
                    <div class="banner-card-content">
                        <h3 class="banner-card-title">عروض وخصومات على افران الغاز</h3>
                        <p class="banner-card-desc">تسوق الان</p>
                    </div>
                </div>

            </div>

            <div class="banner-grid-row bottom-row">

                <div class="banner-grid-wide "
                     style="background-image:url('assets/front-end/img/Group-3.webp'); background-size:cover; background-position:center;">
                    <div class="banner-card-overlay"></div>
                    <div class="banner-wave-pattern"></div>
                    <div class="banner-card-content">
                        <h3 class="banner-card-title">عروض وخصومات على الشاشات</h3>
                        <p class="banner-card-desc">تسوق الان</p>
                    </div>
                </div>

            </div>

        </div>
    </div>
</section>
        @if($web_config['brand_setting'] && $brands->count() > 0)
            <section class="container rtl custom_pd">

                <div class="section-header d-flex justify-content-between align-items-center mb-1">
                    <h2 class="header_section_title mb-0">
                        <span> {{translate('brands')}}</span>
                    </h2>
                    <div class="__mr-2px">
                        <a class="view-all-btn-yellow" href="{{route('brands')}}">
                            {{ translate('view_all')}}
                            <!-- <i class="czi-arrow-{{Session::get('direction') === 'rtl' ? 'left mr-1 ml-n1 mt-1 float-left' : 'right ml-1 mr-n1'}}"></i> -->
                        </a>
                    </div>
                </div>

                <div class="mt-sm-3 mb-0 brand-slider">
                    <div class="owl-carousel owl-theme p-2 brands-slider" data-slide-items="{{ count($brands) }}">
                        @php($brandCount=0)
                        @foreach($brands as $brand)
                            @if($brandCount < 15 && !empty($brand['slug']))
                                <div class="text-center">
                                    <a href="{{ route('brand-products', ['slug' => $brand['slug']]) }}"
                                       class="__brand-item">
                                        <img loading="lazy" alt="{{ $brand->image_alt_text }}"
                                             src="{{ getStorageImages(path: $brand->image_full_url, type: 'brand') }}">
                                    </a>
                                </div>
                            @endif
                            @php($brandCount++)
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <section class="cat-promo-section  rtl py-4">

        <div class="container">
     <div class="cat-promo-grid">

                <a href="{{ route('products') }}?category=large-appliances" class="cat-promo-card">
                    <img loading="lazy" src="{{ theme_asset('public/assets/front-end/img/cat_1.webp') }}"alt="" >
                </a>

                <a href="{{ route('products') }}?category=screens" class="cat-promo-card">
                    <img loading="lazy" src="{{ theme_asset('public/assets/front-end/img/cat_2.webp') }}"alt="" >
                </a>

                <a href="{{ route('products') }}?category=air-conditioners" class="cat-promo-card">
                    <img loading="lazy" src="{{ theme_asset('public/assets/front-end/img/cat_2.webp') }}"alt="" >
                </a>

            </div>
        </div>
       
        </section>

    </div>

    <span id="direction-from-session" data-value="{{ session()->get('direction') }}"></span>
@endsection

@push('script')

    <script src="{{theme_asset(path: 'public/assets/front-end/js/owl.carousel.min.js')}}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/home.js') }}"></script>
@endpush

