<!-- <div class="container rtl">
    <div class="row g-4 pt-0 pb-0 mt-0 __deal-of align-items-start">
        @if(isset($dealOfTheDay->product) || isset($recommendedProduct->discount_type))
            <div class="col-xl-3 col-md-4 pt-0">
                <div class="deal_of_the_day h-100 bg--light">
                    @if(isset($dealOfTheDay->product))
                        <div class="d-flex justify-content-center align-items-center py-4">
                            <h2 class="font-bold fs-16 m-0 align-items-center text-uppercase text-center px-2 web-text-primary h4">
                                {{ translate('deal_of_the_day') }}
                            </h2>
                        </div>
                        <div class="recommended-product-card mt-0 min-height-auto px-2 pb-3">
                            @php($product = $dealOfTheDay->product)
                            @php($wishlist_status = Auth::guard('customer')->check() ? \App\Models\Wishlist::where('customer_id', Auth::guard('customer')->id())->where('product_id', $product->id)->count() : (session()->has('wish_list') && in_array($product->id, session('wish_list')) ? 1 : 0))
                            <div class="premium-card">
                                <div class="premium-product-media">
                                    @if(getProductPriceByType(product: $product, type: 'discount', result: 'value') > 0)
                                        <span class="premium-promo-badge">
                                            {{ translate('discount') }} {{ getProductPriceByType(product: $product, type: 'discount', result: 'string') }}
                                        </span>
                                    @endif

                                    <div class="premium-card-actions">
                                        <button type="button" data-product-id="{{ $product['id'] }}"
                                                class="premium-action-btn product-action-add-wishlist"
                                                title="{{ translate('Add_to_wishlist') }}">
                                            <i class="fa {{($wishlist_status == 1?'fa-heart text-danger':'fa-heart-o')}} wishlist_icon_{{$product['id']}}"></i>
                                        </button>
                                        <a class="premium-action-btn stopPropagation action-product-quick-view"
                                           href="javascript:"
                                           data-product-id="{{ $product->id }}"
                                           title="{{ translate('Quick_View') }}">
                                            <i class="czi-eye align-middle"></i>
                                        </a>
                                    </div>

                                    <div class="premium-card-image">
                                        <a href="{{route('product',$product->slug)}}" class="d-block">
                                            <img loading="lazy" src="{{ getStorageImages(path: $product->thumbnail_full_url, type: 'product') }}" alt="{{ $product['name'] }}">
                                        </a>
                                        @if($product->product_type == 'physical' && $product->current_stock <= 0)
                                            <span class="out_fo_stock">{{translate('out_of_stock')}}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="premium-card-details">
                                    <div class="premium-card-top-info">
                                        @if($product->category)
                                            <span class="premium-category-tag">{{ $product->category->name }}</span>
                                        @endif

                                        <a href="{{route('product',$product->slug)}}" class="premium-product-title" title="{{ $product['name'] }}">
                                            {{ $product['name'] }}
                                        </a>

                                        @php($overallRating = getOverallRating($product['reviews']))
                                        @if($overallRating[0] != 0 )
                                            <div class="rating-show mb-1">
                                                <span class="d-inline-block font-size-sm text-body">
                                                    @for($inc=1;$inc<=5;$inc++)
                                                        @if ($inc <= (int)$overallRating[0])
                                                            <i class="tio-star text-warning"></i>
                                                        @elseif ($overallRating[0] != 0 && $inc <= (int)$overallRating[0] + 1.1 && $overallRating[0] > ((int)$overallRating[0]))
                                                            <i class="tio-star-half text-warning"></i>
                                                        @else
                                                            <i class="tio-star-outlined text-warning"></i>
                                                        @endif
                                                    @endfor
                                                    <label class="badge-style">( {{ count($product['reviews']) }} )</label>
                                                </span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="premium-card-bottom-info">
                                        <div class="premium-product-prices">
                                            @if(getProductPriceByType(product: $product, type: 'discount', result: 'value') > 0)
                                                <del class="premium-price-old">
                                                    {{ webCurrencyConverter(amount: $product->unit_price) }}
                                                </del>
                                            @endif
                                            <span class="premium-price-new">
                                                {{ getProductPriceByType(product: $product, type: 'discounted_unit_price', result: 'string') }}
                                            </span>
                                        </div>

                                        <form class="addToCartDynamicForm d-none" id="add-to-cart-form-deal-{{ $product->id }}">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $product->id }}">
                                            <input type="hidden" name="quantity" value="{{ $product->minimum_order_qty ?? 1 }}">
                                        </form>

                                        <button class="premium-add-to-cart product-add-to-cart-button w-100"
                                                type="button"
                                                data-form="#add-to-cart-form-deal-{{ $product->id }}"
                                                data-update="{{ translate('update_cart') }}"
                                                data-add="{{ translate('add_to_cart') }}">
                                            <i class="fa fa-shopping-cart"></i>
                                            <span class="ms-1">{{ translate('Grab_This_Deal') }}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif (isset($recommendedProduct->discount_type))
                        <div class="d-flex justify-content-center align-items-center py-4">
                            <h2 class="font-bold fs-16 m-0 align-items-center text-uppercase text-center px-2 web-text-primary h4">
                                {{ translate('recommended_product') }}
                            </h2>
                        </div>
                        <div class="recommended-product-card mt-0 px-2 pb-3">
                            @php($product = $recommendedProduct)
                            @php($wishlist_status = Auth::guard('customer')->check() ? \App\Models\Wishlist::where('customer_id', Auth::guard('customer')->id())->where('product_id', $product->id)->count() : (session()->has('wish_list') && in_array($product->id, session('wish_list')) ? 1 : 0))
                            <div class="premium-card">
                                <div class="premium-product-media">
                                    @if(getProductPriceByType(product: $product, type: 'discount', result: 'value') > 0)
                                        <span class="premium-promo-badge">
                                            {{ translate('discount') }} {{ getProductPriceByType(product: $product, type: 'discount', result: 'string') }}
                                        </span>
                                    @endif

                                    <div class="premium-card-actions">
                                        <button type="button" data-product-id="{{ $product['id'] }}"
                                                class="premium-action-btn product-action-add-wishlist"
                                                title="{{ translate('Add_to_wishlist') }}">
                                            <i class="fa {{($wishlist_status == 1?'fa-heart text-danger':'fa-heart-o')}} wishlist_icon_{{$product['id']}}"></i>
                                        </button>
                                        <a class="premium-action-btn stopPropagation action-product-quick-view"
                                           href="javascript:"
                                           data-product-id="{{ $product->id }}"
                                           title="{{ translate('Quick_View') }}">
                                            <i class="czi-eye align-middle"></i>
                                        </a>
                                    </div>

                                    <div class="premium-card-image">
                                        <a href="{{route('product',$product->slug)}}" class="d-block">
                                            <img loading="lazy" src="{{ getStorageImages(path: $product->thumbnail_full_url, type: 'product') }}" alt="{{ $product['name'] }}">
                                        </a>
                                        @if($product->product_type == 'physical' && $product->current_stock <= 0)
                                            <span class="out_fo_stock">{{translate('out_of_stock')}}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="premium-card-details">
                                    <div class="premium-card-top-info">
                                        @if($product->category)
                                            <span class="premium-category-tag">{{ $product->category->name }}</span>
                                        @endif

                                        <a href="{{route('product',$product->slug)}}" class="premium-product-title" title="{{ $product['name'] }}">
                                            {{ $product['name'] }}
                                        </a>

                                        @php($overallRating = getOverallRating($product['reviews']))
                                        @if($overallRating[0] != 0 )
                                            <div class="rating-show mb-1">
                                                <span class="d-inline-block font-size-sm text-body">
                                                    @for($inc=1;$inc<=5;$inc++)
                                                        @if ($inc <= (int)$overallRating[0])
                                                            <i class="tio-star text-warning"></i>
                                                        @elseif ($overallRating[0] != 0 && $inc <= (int)$overallRating[0] + 1.1 && $overallRating[0] > ((int)$overallRating[0]))
                                                            <i class="tio-star-half text-warning"></i>
                                                        @else
                                                            <i class="tio-star-outlined text-warning"></i>
                                                        @endif
                                                    @endfor
                                                    <label class="badge-style">( {{ count($product['reviews']) }} )</label>
                                                </span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="premium-card-bottom-info">
                                        <div class="premium-product-prices">
                                            @if(getProductPriceByType(product: $product, type: 'discount', result: 'value') > 0)
                                                <del class="premium-price-old">
                                                    {{ webCurrencyConverter(amount: $product->unit_price) }}
                                                </del>
                                            @endif
                                            <span class="premium-price-new">
                                                {{ getProductPriceByType(product: $product, type: 'discounted_unit_price', result: 'string') }}
                                            </span>
                                        </div>

                                        <form class="addToCartDynamicForm d-none" id="add-to-cart-form-rec-{{ $product->id }}">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $product->id }}">
                                            <input type="hidden" name="quantity" value="{{ $product->minimum_order_qty ?? 1 }}">
                                        </form>

                                        <button class="premium-add-to-cart product-add-to-cart-button w-100"
                                                type="button"
                                                data-form="#add-to-cart-form-rec-{{ $product->id }}"
                                                data-update="{{ translate('update_cart') }}"
                                                data-add="{{ translate('add_to_cart') }}">
                                            <i class="fa fa-shopping-cart"></i>
                                            <span class="ms-1">{{ translate('Grab_This_Deal') }}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <div
            class="{{ (isset($dealOfTheDay->product) || isset($recommendedProduct->discount_type)) ? 'col-xl-9 col-md-8' : 'col-12' }}">
            <div class="latest-product-margin">
                <div class="d-flex justify-content-between align-items-baseline mb-14px">
                    <h2 class="text-center mb-0">
                    <span class="for-feature-title __text-22px font-bold text-center">
                        {{ translate('latest_products')}}
                    </span>
                    </h2>
                    <div class="mr-1">
                        <a class="view-all-btn-yellow"
                           href="{{ route('latest-products') }}">
                            {{ translate('view_all')}}
                        </a>
                    </div>
                </div>

                <div class="row mt-0 g-2">
                    @php($latestProductsListIndex=0)
                    @foreach($latestProductsList as $product)
                        @if($latestProductsListIndex < 8)
                            @php($latestProductsListIndex++)
                            <div class="col-xl-3 col-sm-4 col-md-6 col-lg-4 col-6 product-with-bg">
                                <div class="h-100">
                                    @php($wishlist_status = Auth::guard('customer')->check() ? \App\Models\Wishlist::where('customer_id', Auth::guard('customer')->id())->where('product_id', $product->id)->count() : (session()->has('wish_list') && in_array($product->id, session('wish_list')) ? 1 : 0))
                                    <div class="premium-card">
                                        <div class="premium-product-media">
                                            @if(getProductPriceByType(product: $product, type: 'discount', result: 'value') > 0)
                                                <span class="premium-promo-badge">
                                                    {{ translate('discount') }} {{ getProductPriceByType(product: $product, type: 'discount', result: 'string') }}
                                                </span>
                                            @endif

                                            <div class="premium-card-actions">
                                                <button type="button" data-product-id="{{ $product['id'] }}"
                                                        class="premium-action-btn product-action-add-wishlist"
                                                        title="{{ translate('Add_to_wishlist') }}">
                                                    <i class="fa {{($wishlist_status == 1?'fa-heart text-danger':'fa-heart-o')}} wishlist_icon_{{$product['id']}}"></i>
                                                </button>
                                                <a class="premium-action-btn stopPropagation action-product-quick-view"
                                                   href="javascript:"
                                                   data-product-id="{{ $product->id }}"
                                                   title="{{ translate('Quick_View') }}">
                                                    <i class="czi-eye align-middle"></i>
                                                </a>
                                            </div>

                                            <div class="premium-card-image">
                                                <a href="{{route('product',$product->slug)}}" class="d-block">
                                                    <img loading="lazy" src="{{ getStorageImages(path: $product->thumbnail_full_url, type: 'product') }}" alt="{{ $product['name'] }}">
                                                </a>
                                                @if($product->product_type == 'physical' && $product->current_stock <= 0)
                                                    <span class="out_fo_stock">{{translate('out_of_stock')}}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="premium-card-details">
                                            <div class="premium-card-top-info">
                                                @if($product->category)
                                                    <span class="premium-category-tag">{{ $product->category->name }}</span>
                                                @endif

                                                <a href="{{route('product',$product->slug)}}" class="premium-product-title" title="{{ $product['name'] }}">
                                                    {{ $product['name'] }}
                                                </a>

                                                @php($overallRating = getOverallRating($product['reviews']))
                                                @if($overallRating[0] != 0 )
                                                    <div class="rating-show mb-1">
                                                        <span class="d-inline-block font-size-sm text-body">
                                                            @for($inc=1;$inc<=5;$inc++)
                                                                @if ($inc <= (int)$overallRating[0])
                                                                    <i class="tio-star text-warning"></i>
                                                                @elseif ($overallRating[0] != 0 && $inc <= (int)$overallRating[0] + 1.1 && $overallRating[0] > ((int)$overallRating[0]))
                                                                    <i class="tio-star-half text-warning"></i>
                                                                @else
                                                                    <i class="tio-star-outlined text-warning"></i>
                                                                @endif
                                                            @endfor
                                                            <label class="badge-style">( {{ count($product['reviews']) }} )</label>
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="premium-card-bottom-info">
                                                <div class="premium-product-prices">
                                                    @if(getProductPriceByType(product: $product, type: 'discount', result: 'value') > 0)
                                                        <del class="premium-price-old">
                                                            {{ webCurrencyConverter(amount: $product->unit_price) }}
                                                        </del>
                                                    @endif
                                                    <span class="premium-price-new">
                                                        {{ getProductPriceByType(product: $product, type: 'discounted_unit_price', result: 'string') }}
                                                    </span>
                                                </div>

                                                <form class="addToCartDynamicForm d-none" id="add-to-cart-form-deal-{{ $product->id }}">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $product->id }}">
                                                    <input type="hidden" name="quantity" value="{{ $product->minimum_order_qty ?? 1 }}">
                                                </form>

                                                <button class="premium-add-to-cart product-add-to-cart-button"
                                                        type="button"
                                                        data-form="#add-to-cart-form-deal-{{ $product->id }}"
                                                        data-update="{{ translate('update_cart') }}"
                                                        data-add="{{ translate('add_to_cart') }}">
                                                    <i class="fa fa-shopping-cart"></i>
                                                    <span class="ms-1">{{ translate('add_to_cart') }}</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div> -->
