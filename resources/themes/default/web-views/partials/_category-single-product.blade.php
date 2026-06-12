@if(isset($product))
    @php($overallRating = getOverallRating($product?->reviews))
    @php($wishlist_status = Auth::guard('customer')->check() ? \App\Models\Wishlist::where('customer_id', Auth::guard('customer')->id())->where('product_id', $product->id)->count() : (session()->has('wish_list') && in_array($product->id, session('wish_list')) ? 1 : 0))

    <div class="custom-product-card shadow-none">
        <div class="product-card-top">
            @if(getProductPriceByType(product: $product, type: 'discount', result: 'value') > 0)
                <span class="product-badges">
                    {{ translate('discount') }} {{ getProductPriceByType(product: $product, type: 'discount', result: 'string') }}
                </span>
            @endif

            <div class="card-actions">
                <button type="button" data-product-id="{{ $product['id'] }}"
                        class="action-btn product-action-add-wishlist"
                        title="{{ translate('Add_to_wishlist') }}">
                    <i class="fa {{($wishlist_status == 1?'fa-heart text-danger':'fa-heart-o')}} wishlist_icon_{{$product['id']}}"></i>
                </button>
                <a class="action-btn stopPropagation action-product-quick-view"
                   href="javascript:"
                   data-product-id="{{ $product->id }}"
                   title="{{ translate('Quick_View') }}">
                    <i class="czi-eye align-middle"></i>
                </a>
            </div>
        </div>

        <div class="product-image-container">
            <a href="{{route('product',$product->slug)}}" class="d-block">
                <img src="{{ getStorageImages(path: $product->thumbnail_full_url, type: 'product') }}" alt="{{ $product['name'] }}">
            </a>
            @if($product->product_type == 'physical' && $product->current_stock <= 0)
                <span class="out_fo_stock">{{translate('out_of_stock')}}</span>
            @endif
        </div>

        <hr>

        <div class="product-details {{Session::get('direction') === "rtl" ? 'rtl' : ''}}">
            @if($product->category)
                <span class="category-badge">{{ $product->category->name }}</span>
            @endif

            <a href="{{route('product',$product->slug)}}" class="product-title-text" title="{{ $product['name'] }}">
                {{ $product['name'] }}
            </a>

            <div class="product-prices">
                @if(getProductPriceByType(product: $product, type: 'discount', result: 'value') > 0)
                    <del class="price-old">
                        {{ webCurrencyConverter(amount: $product->unit_price) }}
                    </del>
                @endif
                <span class="price-new font-bold">
                    {{ getProductPriceByType(product: $product, type: 'discounted_unit_price', result: 'string') }}
                </span>
            </div>

            <form class="addToCartDynamicForm d-none" id="add-to-cart-form-{{ $product->id }}">
                @csrf
                <input type="hidden" name="id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" value="{{ $product->minimum_order_qty ?? 1 }}">
            </form>

            <button class="add-to-cart-btn product-add-to-cart-button"
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


