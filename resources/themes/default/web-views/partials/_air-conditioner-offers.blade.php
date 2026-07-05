<section class="air-conditioner-offers-section rtl custom_pd">
    <div class="container">
        <h2 class="rp-section-title mb-3">عروض المكيفات</h2>
        @if(isset($airConditionerProducts) && $airConditionerProducts->count() > 0)
            <div class="owl-carousel owl-theme premium-product-carousel" id="ac-offers-carousel">
                @foreach($airConditionerProducts as $product)
                    <div class="premium-card-item h-100">
                        <div class="premium-card">
                            <div class="premium-product-media">
                                @php
                                    $discountPercent = 0;
                                    if($product->discount > 0) {
                                        if($product->discount_type == 'percent') {
                                            $discountPercent = $product->discount;
                                        } else {
                                            $discountPercent = round(($product->discount / $product->unit_price) * 100);
                                        }
                                    }
                                @endphp
                                @if($discountPercent > 0)
                                    <span class="premium-promo-badge">خصم {{ $discountPercent }}%</span>
                                @endif
                                <div class="premium-card-actions">
                                    @php
                                        $wishlist_status = Auth::guard('customer')->check() ? \App\Models\Wishlist::where('customer_id', Auth::guard('customer')->id())->where('product_id', $product->id)->count() : (session()->has('wish_list') && in_array($product->id, session('wish_list')) ? 1 : 0);
                                    @endphp
                                    <button type="button" data-product-id="{{ $product['id'] }}"
                                            class="premium-action-btn product-action-add-wishlist"
                                            title="{{ translate('Add_to_wishlist') }}">
                                        <i class="fa {{($wishlist_status == 1?'fa-heart text-danger':'fa-heart-o')}} wishlist_icon_{{$product['id']}}"></i>
                                    </button>
                                    <button type="button" class="premium-action-btn stopPropagation action-product-quick-view"
                                            data-product-id="{{ $product->id }}"
                                            title="{{ translate('Quick_View') }}">
                                        <i class="czi-eye align-middle"></i>
                                    </button>
                                </div>
                                <div class="premium-card-image">
                                    <a href="{{ route('product', $product->slug) }}" class="d-block">
                                        <img loading="lazy" src="{{ getStorageImages(path: $product->thumbnail_full_url ?? $product->thumbnail, type: 'product') }}" alt="{{ $product->name }}">
                                    </a>
                                </div>
                            </div>
                            <div class="premium-card-details">
                                <a href="{{ route('product', $product->slug) }}" class="premium-product-title">{{ Str::limit($product->name, 50) }}</a>
                                <div class="premium-product-prices">
                                    @php
                                        $finalPrice = $product->unit_price;
                                        if($product->discount > 0) {
                                            if($product->discount_type == 'percent') {
                                                $finalPrice = $product->unit_price - ($product->unit_price * $product->discount / 100);
                                            } else {
                                                $finalPrice = $product->unit_price - $product->discount;
                                            }
                                        }
                                    @endphp
                                    @if($product->discount > 0)
                                        <del class="premium-price-old">{{ webCurrencyConverter(amount: $product->unit_price) }}</del>
                                    @endif
                                    <span class="premium-price-new">{{ webCurrencyConverter(amount: $finalPrice) }}</span>
                                </div>
                                @php
                                    $hasVariations = false;
                                    if (isset($product->choice_options)) {
                                        $choices = json_decode($product->choice_options, true);
                                        $specTitles = ['الصناعة', 'الضمان', 'الضمان الشامل', 'ضمان الكمبروسر', 'سعة التبريد', 'حار و بارد / بارد', 'حار وبارد / بارد', 'التردد', 'ارتفاع الاستاند', 'الجهد الكهربائي', 'الفريون'];
                                        if (is_array($choices)) {
                                            foreach ($choices as $choice) {
                                                $title = trim($choice['title'] ?? '');
                                                $optionsCount = count($choice['options'] ?? []);
                                                if (!in_array($title, $specTitles) && $optionsCount > 1) {
                                                    $hasVariations = true;
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                    if (isset($product->colors)) {
                                        $colors = json_decode($product->colors, true);
                                        if (is_array($colors) && count($colors) > 0) {
                                            $hasVariations = true;
                                        }
                                    }
                                @endphp
                                @if($hasVariations)
                                    <button class="premium-add-to-cart action-product-quick-view" type="button" data-product-id="{{ $product->id }}">
                                        <i class="fa fa-shopping-cart"></i>
                                        <span class="ms-1">أضف للعربة</span>
                                    </button>
                                @else
                                    <form class="addToCartDynamicForm d-none" id="add-to-cart-form-ac-{{ $product->id }}">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $product->id }}">
                                        <input type="hidden" name="quantity" value="{{ $product->minimum_order_qty ?? 1 }}">
                                    </form>
                                    <button class="premium-add-to-cart product-add-to-cart-button" type="button" data-form="#add-to-cart-form-ac-{{ $product->id }}">
                                        <i class="fa fa-shopping-cart"></i>
                                        <span class="ms-1">أضف للعربة</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center">لا توجد منتجات في قسم المكيفات حالياً.</p>
        @endif
    </div>
</section>
