@if(count($bannerTypeMainBanner) > 0)
<section class="hero-slider-section">
    <div class="owl-theme owl-carousel hero-slider" data-loop="{{ count($bannerTypeMainBanner) > 1 ? 1 : 0 }}">
        @foreach($bannerTypeMainBanner as $key=>$banner)
            <a href="{{$banner['url']}}" class="d-block" target="_blank">
                <img class="w-100 hero-slider-img" alt=""
                    src="{{ getStorageImages(path: $banner->photo_full_url, type: 'banner') }}">
            </a>
        @endforeach
    </div>
</section>
@endif
