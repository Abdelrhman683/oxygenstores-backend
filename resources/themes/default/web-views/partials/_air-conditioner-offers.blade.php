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
                                        <del class="premium-price-old">{{ number_format($product->unit_price, 2) }}</del>
                                    @endif
                                    <span class="premium-price-new">{{ number_format($finalPrice, 2) }}</span>
                                </div>
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
