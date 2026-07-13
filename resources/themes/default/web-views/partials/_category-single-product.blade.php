@if(isset($product))
    @php
        $overallRating = getOverallRating($product?->reviews);
        $wishlist_status = Auth::guard('customer')->check() ? \App\Models\Wishlist::where('customer_id', Auth::guard('customer')->id())->where('product_id', $product->id)->count() : (session()->has('wish_list') && in_array($product->id, session('wish_list')) ? 1 : 0);
    @endphp

    <div class="premium-card h-100">
        <div class="premium-product-media">
            @if($product->getActiveCouponCode())
                <span class="premium-promo-badge">
                    {{ translate('استخدم كود') }} {{ $product->getActiveCouponCode() }}
                </span>
            @elseif(getProductPriceByType(product: $product, type: 'discount', result: 'value') > 0)
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
                    <img src="{{ getStorageImages(path: $product->thumbnail_full_url, type: 'product') }}" alt="{{ $product['name'] }}">
                </a>
                @if($product->product_type == 'physical' && $product->current_stock <= 0)
                    <span class="out_fo_stock">{{translate('out_of_stock')}}</span>
                @endif
            </div>
            @if($product->isAirConditioner())
                <span class="product-tax-badge ac-tax-badge">السعر شامل الضريبة والتركيب و 4 متر نحاس + ربل او كرسي + تيب</span>
            @else
                <span class="product-tax-badge">السعر شامل الضريبة</span>
            @endif
        </div>

        <div class="premium-card-details">
            @if($product->category)
                <span class="premium-category-tag">{{ $product->category->name }}</span>
            @endif

            <a href="{{route('product',$product->slug)}}" class="premium-product-title" title="{{ $product['name'] }}">
                {{ $product['name'] }}
            </a>

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

            <form class="addToCartDynamicForm d-none" id="add-to-cart-form-{{ $product->id }}">
                @csrf
                <input type="hidden" name="id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" value="{{ $product->minimum_order_qty ?? 1 }}">
            </form>

            <button class="premium-add-to-cart product-add-to-cart-button"
                    type="button"
                    data-form="#add-to-cart-form-{{ $product->id }}"
                    data-update="{{ translate('update_cart') }}"
                    data-add="{{ translate('add_to_cart') }}">
                <i class="fa fa-shopping-cart"></i>
                <span class="ms-1">أضف للعربة</span>
            </button>
        </div>
    </div>
@endif


