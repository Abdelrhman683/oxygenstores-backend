@if ($categories->count() > 0 )
    <section class="home-categories-section container rtl px-0 px-md-3">
        <div class="section-header">
            <h2 class="categories-title ">{{ translate('categories')}}</h2>
            <a class="view-all-btn-yellow" href="{{route('categories')}}">
                {{ translate('view_all')}}
            </a>
        </div>

        <div class="categories-carousel-wrapper">
            <div class="owl-carousel owl-theme categories--slider">
                @foreach($categories as $category)
                    <div class="category-card-item">
                        <a href="{{ route('category-products', ['slug' => $category['slug']]) }}">
                            <div class="category-img-wrapper">
                                <img loading="lazy" alt="{{ $category->name }}"
                                     src="{{ getStorageImages(path: $category->icon_full_url, type: 'category') }}">
                            </div>
                            <span class="category-title">{{ $category->name }}</span>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
