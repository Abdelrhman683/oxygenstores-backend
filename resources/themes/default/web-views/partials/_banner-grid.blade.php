@if(count($bannerTypeFeaturedSection) > 0)
<section class="featured-banners-section custom_pd">
    <div class="container ">
        <h2 class="section-title">تسوق عبر الأقسام المميزة</h2>

        <div class="featured-banners-grid">
            {{-- Banner 1: Vertical --}}
            @php($banner1 = $bannerTypeFeaturedSection[0] ?? null)
            @if($banner1)
                <div class="banner-item banner-vertical">
                    <a href="{{ $banner1->url }}">
                        <img src="{{ getStorageImages(path: $banner1->photo_full_url, type: 'banner') }}" alt="{{ $banner1->title }}">
                        <div class="banner-content">
                            <span class="banner-title">{{ $banner1->title }}</span>
                            <span class="banner-desc">{{ $banner1->sub_title }}</span>
                        </div>
                    </a>
                </div>
            @endif
     

            {{-- Banner 2: Small --}}
            @php($banner2 = $bannerTypeFeaturedSection[1] ?? null)
            @if($banner2)
                <div class="banner-item banner-sm">
                    <a href="{{ $banner2->url }}">
                        <img src="{{ getStorageImages(path: $banner2->photo_full_url, type: 'banner') }}" alt="{{ $banner2->title }}">
                        <div class="banner-content">
                            <span class="banner-title">{{ $banner2->title }}</span>
                            <span class="banner-desc">{{ $banner2->sub_title }}</span>
                        </div>
                    </a>
                </div>
            @endif

            {{-- Banner 3: Medium --}}
            @php($banner3 = $bannerTypeFeaturedSection[2] ?? null)
            @if($banner3)
                <div class="banner-item banner-md">
                    <a href="{{ $banner3->url }}">
                        <img src="{{ getStorageImages(path: $banner3->photo_full_url, type: 'banner') }}" alt="{{ $banner3->title }}">
                        <div class="banner-content">
                            <span class="banner-title">{{ $banner3->title }}</span>
                            <span class="banner-desc">{{ $banner3->sub_title }}</span>
                        </div>
                    </a>
                </div>
            @endif
       

            {{-- Banner 4: Wide --}}
            @php($banner4 = $bannerTypeFeaturedSection[3] ?? null)
            @if($banner4)
                <div class="banner-item banner-wide">
                    <a href="{{ $banner4->url }}">
                        <img src="{{ getStorageImages(path: $banner4->photo_full_url, type: 'banner') }}" alt="{{ $banner4->title }}">
                        <div class="banner-content">
                            <span class="banner-title">{{ $banner4->title }}</span>
                            <span class="banner-desc">{{ $banner4->sub_title }}</span>
                        </div>
                    </a>
                </div>
            @endif
        </div>

        <div class="featured-banners-row">
            {{-- Banner 5: Short --}}
            @php($banner5 = $bannerTypeFeaturedSection[4] ?? null)
            @if($banner5)
                <div class="banner-item banner-short">
                    <a href="{{ $banner5->url }}">
                        <img src="{{ getStorageImages(path: $banner5->photo_full_url, type: 'banner') }}" alt="{{ $banner5->title }}">
                        <div class="banner-content">
                            <span class="banner-title">{{ $banner5->title }}</span>
                            <span class="banner-desc">{{ $banner5->sub_title }}</span>
                        </div>
                    </a>
                </div>
            @endif

            {{-- Banner 6: Short --}}
            @php($banner6 = $bannerTypeFeaturedSection[5] ?? null)
            @if($banner6)
                <div class="banner-item banner-short">
                    <a href="{{ $banner6->url }}">
                        <img src="{{ getStorageImages(path: $banner6->photo_full_url, type: 'banner') }}" alt="{{ $banner6->title }}">
                        <div class="banner-content">
                            <span class="banner-title">{{ $banner6->title }}</span>
                            <span class="banner-desc">{{ $banner6->sub_title }}</span>
                        </div>
                    </a>
                </div>
            @endif
        </div>
    </div>
</section>
@endif
