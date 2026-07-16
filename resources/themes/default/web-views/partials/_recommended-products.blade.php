<section class="recommended-products-section rtl custom_pd">

    <div class="rp-header-bar">
        <div class="container">
            <h2 class="rp-section-title mb-0">منتجات ننصح بها</h2>
        </div>
    </div>

    @php
        $hasFeatured = isset($featuredProductsList) && count($featuredProductsList) > 0;
        $hasBestSell = isset($bestSellProduct) && count($bestSellProduct) > 0;
        $hasLatest = isset($latestProductsList) && count($latestProductsList) > 0;
        $hasRecommendedCategories = isset($recommendedCategories) && count($recommendedCategories) > 0;
        $activeTab = $hasRecommendedCategories ? 'rp-pane-' . $recommendedCategories[0]->id : ($hasFeatured ? 'rp-pane-featured' : ($hasBestSell ? 'rp-pane-best_selling' : 'rp-pane-latest'));
    @endphp

    @if($hasRecommendedCategories || $hasFeatured || $hasBestSell || $hasLatest)
        <div class="container rp-tabs-wrapper d-flex justify-content-center justify-content-lg-start">
            <ul class="rp-tabs-nav d-flex p-0 mb-0" id="recommendedTabsNav" role="tablist">
                @forelse($recommendedCategories as $category)
                    <li class="rp-tab-item" role="presentation">
                        <button class="rp-tab-btn {{ $loop->first ? 'active' : '' }}" data-target="rp-pane-{{ $category->id }}"
                            type="button">{{ $category->name }}</button>
                    </li>
                @empty
                @endforelse

                @if($hasFeatured && (!$hasRecommendedCategories || count($recommendedCategories) === 0))
                    <li class="rp-tab-item" role="presentation">
                        <button class="rp-tab-btn {{ $activeTab === 'rp-pane-featured' ? 'active' : '' }}"
                            data-target="rp-pane-featured" type="button">المنتجات المميزة</button>
                    </li>
                @endif
                @if($hasBestSell && (!$hasRecommendedCategories || count($recommendedCategories) === 0))
                    <li class="rp-tab-item" role="presentation">
                        <button class="rp-tab-btn {{ $activeTab === 'rp-pane-best_selling' ? 'active' : '' }}"
                            data-target="rp-pane-best_selling" type="button">الأكثر مبيعًا</button>
                    </li>
                @endif
                @if($hasLatest && (!$hasRecommendedCategories || count($recommendedCategories) === 0))
                    <li class="rp-tab-item" role="presentation">
                        <button class="rp-tab-btn {{ $activeTab === 'rp-pane-latest' ? 'active' : '' }}"
                            data-target="rp-pane-latest" type="button">أحدث المنتجات</button>
                    </li>
                @endif
            </ul>
        </div>

        <div class="rp-tabs-content mt-4 container" id="recommendedTabsContent">

            @forelse($recommendedCategories as $index => $category)
                <div class="rp-tab-pane {{ $index === 0 ? 'active' : '' }}" id="rp-pane-{{ $category->id }}" role="tabpanel">
                    <div class="owl-carousel owl-theme premium-product-carousel" id="rp-swiper-{{ $category->id }}">
                        @forelse(collect($category->product)->take(10) as $product)
                            <div class="premium-card-item h-100">
                                <div class="premium-card">
                                    <div class="premium-product-media">
                                        @php
                                            $discountPercent = 0;
                                            if ($product->discount > 0) {
                                                if ($product->discount_type == 'percent') {
                                                    $discountPercent = $product->discount;
                                                } else {
                                                    $discountPercent = round(($product->discount / $product->unit_price) * 100);
                                                }
                                            }
                                        @endphp
                                        @if($product->getActiveCouponCode())
                                            <span class="premium-promo-badge">{{ translate('استخدم كود') }}
                                                {{ $product->getActiveCouponCode() }}</span>
                                        @elseif($discountPercent > 0)
                                            <span class="premium-promo-badge">خصم {{ $discountPercent }}%</span>
                                        @endif
                                        <div class="premium-card-actions">
                                            @php
                                                $wishlist_status = Auth::guard('customer')->check() ? \App\Models\Wishlist::where('customer_id', Auth::guard('customer')->id())->where('product_id', $product->id)->count() : (session()->has('wish_list') && in_array($product->id, session('wish_list')) ? 1 : 0);
                                            @endphp
                                            <button type="button" data-product-id="{{ $product['id'] }}"
                                                class="premium-action-btn product-action-add-wishlist"
                                                title="{{ translate('Add_to_wishlist') }}">
                                                <i
                                                    class="fa {{($wishlist_status == 1 ? 'fa-heart text-danger' : 'fa-heart-o')}} wishlist_icon_{{$product['id']}}"></i>
                                            </button>
                                            <button type="button"
                                                class="premium-action-btn stopPropagation action-product-quick-view"
                                                data-product-id="{{ $product->id }}" title="{{ translate('Quick_View') }}">
                                                <i class="czi-eye align-middle"></i>
                                            </button>
                                        </div>
                                        <div class="premium-card-image">
                                            <a href="{{ route('product', $product->slug) }}" class="d-block">
                                                <img loading="lazy"
                                                    src="{{ getStorageImages(path: $product->thumbnail_full_url ?? $product->thumbnail, type: 'product') }}"
                                                    alt="{{ $product->name }}">
                                            </a>
                                            @if($product->product_type == 'physical' && $product->current_stock <= 0)
                                                <span class="out_fo_stock">{{translate('out_of_stock')}}</span>
                                            @endif
                                        </div>
                                        @if($product->isAirConditioner())
                                            <span class="product-tax-badge ac-tax-badge">شامل التركيب</span>
                                        @endif
                                    </div>
                                    <div class="premium-card-details">
                                        <span class="premium-category-tag">{{ $category->name }}</span>
                                        <a href="{{ route('product', $product->slug) }}"
                                            class="premium-product-title">{{ Str::limit($product->name, 50) }}</a>
                                        <div class="premium-product-prices">
                                            @php
                                                $finalPrice = $product->unit_price;
                                                if ($product->discount > 0) {
                                                    if ($product->discount_type == 'percent') {
                                                        $finalPrice = $product->unit_price - ($product->unit_price * $product->discount / 100);
                                                    } else {
                                                        $finalPrice = $product->unit_price - $product->discount;
                                                    }
                                                }
                                            @endphp
                                            @if($product->discount > 0)
                                                <del
                                                    class="premium-price-old">{{ webCurrencyConverter(amount: $product->unit_price) }}</del>
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
                                            <button class="premium-add-to-cart action-product-quick-view" type="button"
                                                data-product-id="{{ $product->id }}">
                                                <i class="fa fa-shopping-cart"></i>
                                                <span class="ms-1">أضف للعربة</span>
                                            </button>
                                        @else
                                            <form class="addToCartDynamicForm d-none" id="add-to-cart-form-rec-{{ $product->id }}">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $product->id }}">
                                                <input type="hidden" name="quantity" value="{{ $product->minimum_order_qty ?? 1 }}">
                                            </form>
                                            <button class="premium-add-to-cart product-add-to-cart-button" type="button"
                                                data-form="#add-to-cart-form-rec-{{ $product->id }}">
                                                <i class="fa fa-shopping-cart"></i>
                                                <span class="ms-1">أضف للعربة</span>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-center">{{ translate('no_product_found') }}</p>
                        @endforelse
                    </div>
                </div>
            @empty
                <p class="text-center py-4">{{ translate('no_category_found') }}</p>
            @endforelse

            <!-- Keep old static items as fallback for theme compatibility -->
            <div class="rp-tab-pane" id="rp-pane-washers" role="tabpanel" style="display: none;">
                <div class="owl-carousel owl-theme premium-product-carousel" id="rp-swiper-washers">
                    <div class="premium-card-item h-100">
                        <div class="premium-card">
                            <div class="premium-product-media">
                                <span class="premium-promo-badge">خصم 20%</span>
                                <div class="premium-card-actions">
                                    <button type="button" class="premium-action-btn" title="Add to wishlist">
                                        <i class="fa fa-heart-o"></i>
                                    </button>
                                    <button type="button" class="premium-action-btn" data-toggle="modal"
                                        data-target="#premium-static-quickview" title="Quick View">
                                        <i class="czi-eye align-middle"></i>
                                    </button>
                                </div>
                                <div class="premium-card-image">
                                    <a href="#" class="d-block">
                                        <img src="https://placehold.co/200x200?text=غسالة+1" alt="غسالة">
                                    </a>
                                </div>
                            </div>
                            <div class="premium-card-details">
                                <span class="premium-category-tag">غسالات أوتوماتيك</span>
                                <a href="#" class="premium-product-title">غسالة إل جي 10.5 كيلو أبيض...</a>
                                <div class="premium-product-prices">
                                    <span class="premium-price-new">2850 ريال</span>
                                    <del class="premium-price-old">3500 ريال</del>
                                </div>
                                <button class="premium-add-to-cart" type="button">
                                    <i class="fa fa-shopping-cart"></i>
                                    <span class="ms-1">أضف للعربة</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="premium-card-item h-100">
                        <div class="premium-card">
                            <div class="premium-product-media">
                                <span class="premium-promo-badge">خصم 20%</span>
                                <div class="premium-card-actions">
                                    <button type="button" class="premium-action-btn" title="Add to wishlist">
                                        <i class="fa fa-heart-o"></i>
                                    </button>
                                    <button type="button" class="premium-action-btn" data-toggle="modal"
                                        data-target="#premium-static-quickview" title="Quick View">
                                        <i class="czi-eye align-middle"></i>
                                    </button>
                                </div>
                                <div class="premium-card-image">
                                    <a href="#" class="d-block">
                                        <img src="https://placehold.co/200x200?text=غسالة+1" alt="غسالة">
                                    </a>
                                </div>
                            </div>
                            <div class="premium-card-details">
                                <span class="premium-category-tag">غسالات أوتوماتيك</span>
                                <a href="#" class="premium-product-title">غسالة إل جي 10.5 كيلو أبيض...</a>
                                <div class="premium-product-prices">
                                    <span class="premium-price-new">2850 ريال</span>
                                    <del class="premium-price-old">3500 ريال</del>
                                </div>
                                <button class="premium-add-to-cart" type="button">
                                    <i class="fa fa-shopping-cart"></i>
                                    <span class="ms-1">أضف للعربة</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="premium-card-item h-100">
                        <div class="premium-card">
                            <div class="premium-product-media">
                                <span class="premium-promo-badge">خصم 20%</span>
                                <div class="premium-card-actions">
                                    <button type="button" class="premium-action-btn" title="Add to wishlist">
                                        <i class="fa fa-heart-o"></i>
                                    </button>
                                    <button type="button" class="premium-action-btn" data-toggle="modal"
                                        data-target="#premium-static-quickview" title="Quick View">
                                        <i class="czi-eye align-middle"></i>
                                    </button>
                                </div>
                                <div class="premium-card-image">
                                    <a href="#" class="d-block">
                                        <img src="https://placehold.co/200x200?text=غسالة+1" alt="غسالة">
                                    </a>
                                </div>
                            </div>
                            <div class="premium-card-details">
                                <span class="premium-category-tag">غسالات أوتوماتيك</span>
                                <a href="#" class="premium-product-title">غسالة إل جي 10.5 كيلو أبيض...</a>
                                <div class="premium-product-prices">
                                    <span class="premium-price-new">2850 ريال</span>
                                    <del class="premium-price-old">3500 ريال</del>
                                </div>
                                <button class="premium-add-to-cart" type="button">
                                    <i class="fa fa-shopping-cart"></i>
                                    <span class="ms-1">أضف للعربة</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="premium-card-item h-100">
                        <div class="premium-card">
                            <div class="premium-product-media">
                                <span class="premium-promo-badge">خصم 20%</span>
                                <div class="premium-card-actions">
                                    <button type="button" class="premium-action-btn" title="Add to wishlist">
                                        <i class="fa fa-heart-o"></i>
                                    </button>
                                    <button type="button" class="premium-action-btn" data-toggle="modal"
                                        data-target="#premium-static-quickview" title="Quick View">
                                        <i class="czi-eye align-middle"></i>
                                    </button>
                                </div>
                                <div class="premium-card-image">
                                    <a href="#" class="d-block">
                                        <img src="https://placehold.co/200x200?text=غسالة+1" alt="غسالة">
                                    </a>
                                </div>
                            </div>
                            <div class="premium-card-details">
                                <span class="premium-category-tag">غسالات أوتوماتيك</span>
                                <a href="#" class="premium-product-title">غسالة إل جي 10.5 كيلو أبيض...</a>
                                <div class="premium-product-prices">
                                    <span class="premium-price-new">2850 ريال</span>
                                    <del class="premium-price-old">3500 ريال</del>
                                </div>
                                <button class="premium-add-to-cart" type="button">
                                    <i class="fa fa-shopping-cart"></i>
                                    <span class="ms-1">أضف للعربة</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="premium-card-item h-100">
                        <div class="premium-card">
                            <div class="premium-product-media">
                                <span class="premium-promo-badge">خصم 20%</span>
                                <div class="premium-card-actions">
                                    <button type="button" class="premium-action-btn" title="Add to wishlist">
                                        <i class="fa fa-heart-o"></i>
                                    </button>
                                    <button type="button" class="premium-action-btn" data-toggle="modal"
                                        data-target="#premium-static-quickview" title="Quick View">
                                        <i class="czi-eye align-middle"></i>
                                    </button>
                                </div>
                                <div class="premium-card-image">
                                    <a href="#" class="d-block">
                                        <img src="https://placehold.co/200x200?text=غسالة+1" alt="غسالة">
                                    </a>
                                </div>
                            </div>
                            <div class="premium-card-details">
                                <span class="premium-category-tag">غسالات أوتوماتيك</span>
                                <a href="#" class="premium-product-title">غسالة إل جي 10.5 كيلو أبيض...</a>
                                <div class="premium-product-prices">
                                    <span class="premium-price-new">2850 ريال</span>
                                    <del class="premium-price-old">3500 ريال</del>
                                </div>
                                <button class="premium-add-to-cart" type="button">
                                    <i class="fa fa-shopping-cart"></i>
                                    <span class="ms-1">أضف للعربة</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="premium-card-item h-100">
                        <div class="premium-card">
                            <div class="premium-product-media">
                                <span class="premium-promo-badge">خصم 20%</span>
                                <div class="premium-card-actions">
                                    <button type="button" class="premium-action-btn" title="Add to wishlist">
                                        <i class="fa fa-heart-o"></i>
                                    </button>
                                    <button type="button" class="premium-action-btn" data-toggle="modal"
                                        data-target="#premium-static-quickview" title="Quick View">
                                        <i class="czi-eye align-middle"></i>
                                    </button>
                                </div>
                                <div class="premium-card-image">
                                    <a href="#" class="d-block">
                                        <img src="https://placehold.co/200x200?text=غسالة+1" alt="غسالة">
                                    </a>
                                </div>
                            </div>
                            <div class="premium-card-details">
                                <span class="premium-category-tag">غسالات أوتوماتيك</span>
                                <a href="#" class="premium-product-title">غسالة إل جي 10.5 كيلو أبيض...</a>
                                <div class="premium-product-prices">
                                    <span class="premium-price-new">2850 ريال</span>
                                    <del class="premium-price-old">3500 ريال</del>
                                </div>
                                <button class="premium-add-to-cart" type="button">
                                    <i class="fa fa-shopping-cart"></i>
                                    <span class="ms-1">أضف للعربة</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="premium-card-item h-100">
                        <div class="premium-card">
                            <div class="premium-product-media">
                                <span class="premium-promo-badge">خصم 20%</span>
                                <div class="premium-card-actions">
                                    <button type="button" class="premium-action-btn" title="Add to wishlist">
                                        <i class="fa fa-heart-o"></i>
                                    </button>
                                    <button type="button" class="premium-action-btn" data-toggle="modal"
                                        data-target="#premium-static-quickview" title="Quick View">
                                        <i class="czi-eye align-middle"></i>
                                    </button>
                                </div>
                                <div class="premium-card-image">
                                    <a href="#" class="d-block">
                                        <img src="https://placehold.co/200x200?text=غسالة+1" alt="غسالة">
                                    </a>
                                </div>
                            </div>
                            <div class="premium-card-details">
                                <span class="premium-category-tag">غسالات أوتوماتيك</span>
                                <a href="#" class="premium-product-title">غسالة إل جي 10.5 كيلو أبيض...</a>
                                <div class="premium-product-prices">
                                    <span class="premium-price-new">2850 ريال</span>
                                    <del class="premium-price-old">3500 ريال</del>
                                </div>
                                <button class="premium-add-to-cart" type="button">
                                    <i class="fa fa-shopping-cart"></i>
                                    <span class="ms-1">أضف للعربة</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="premium-card-item h-100">
                        <div class="premium-card">
                            <div class="premium-product-media">
                                <span class="premium-promo-badge">خصم 20%</span>
                                <div class="premium-card-actions">
                                    <button type="button" class="premium-action-btn" title="Add to wishlist">
                                        <i class="fa fa-heart-o"></i>
                                    </button>
                                    <button type="button" class="premium-action-btn" data-toggle="modal"
                                        data-target="#premium-static-quickview" title="Quick View">
                                        <i class="czi-eye align-middle"></i>
                                    </button>
                                </div>
                                <div class="premium-card-image">
                                    <a href="#" class="d-block">
                                        <img src="https://placehold.co/200x200?text=غسالة+1" alt="غسالة">
                                    </a>
                                </div>
                            </div>
                            <div class="premium-card-details">
                                <span class="premium-category-tag">غسالات أوتوماتيك</span>
                                <a href="#" class="premium-product-title">غسالة إل جي 10.5 كيلو أبيض...</a>
                                <div class="premium-product-prices">
                                    <span class="premium-price-new">2850 ريال</span>
                                    <del class="premium-price-old">3500 ريال</del>
                                </div>
                                <button class="premium-add-to-cart" type="button">
                                    <i class="fa fa-shopping-cart"></i>
                                    <span class="ms-1">أضف للعربة</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="premium-card-item h-100">
                        <div class="premium-card">
                            <div class="premium-product-media">
                                <span class="premium-promo-badge">خصم 20%</span>
                                <div class="premium-card-actions">
                                    <button type="button" class="premium-action-btn" title="Add to wishlist">
                                        <i class="fa fa-heart-o"></i>
                                    </button>
                                    <button type="button" class="premium-action-btn" data-toggle="modal"
                                        data-target="#premium-static-quickview" title="Quick View">
                                        <i class="czi-eye align-middle"></i>
                                    </button>
                                </div>
                                <div class="premium-card-image">
                                    <a href="#" class="d-block">
                                        <img src="https://placehold.co/200x200?text=غسالة+1" alt="غسالة">
                                    </a>
                                </div>
                            </div>
                            <div class="premium-card-details">
                                <span class="premium-category-tag">غسالات أوتوماتيك</span>
                                <a href="#" class="premium-product-title">غسالة إل جي 10.5 كيلو أبيض...</a>
                                <div class="premium-product-prices">
                                    <span class="premium-price-new">2850 ريال</span>
                                    <del class="premium-price-old">3500 ريال</del>
                                </div>
                                <button class="premium-add-to-cart" type="button">
                                    <i class="fa fa-shopping-cart"></i>
                                    <span class="ms-1">أضف للعربة</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="premium-card-item h-100">
                        <div class="premium-card">
                            <div class="premium-product-media">
                                <span class="premium-promo-badge">خصم 20%</span>
                                <div class="premium-card-actions">
                                    <button type="button" class="premium-action-btn" title="Add to wishlist">
                                        <i class="fa fa-heart-o"></i>
                                    </button>
                                    <button type="button" class="premium-action-btn" data-toggle="modal"
                                        data-target="#premium-static-quickview" title="Quick View">
                                        <i class="czi-eye align-middle"></i>
                                    </button>
                                </div>
                                <div class="premium-card-image">
                                    <a href="#" class="d-block">
                                        <img src="https://placehold.co/200x200?text=غسالة+1" alt="غسالة">
                                    </a>
                                </div>
                            </div>
                            <div class="premium-card-details">
                                <span class="premium-category-tag">غسالات أوتوماتيك</span>
                                <a href="#" class="premium-product-title">غسالة إل جي 10.5 كيلو أبيض...</a>
                                <div class="premium-product-prices">
                                    <span class="premium-price-new">2850 ريال</span>
                                    <del class="premium-price-old">3500 ريال</del>
                                </div>
                                <button class="premium-add-to-cart" type="button">
                                    <i class="fa fa-shopping-cart"></i>
                                    <span class="ms-1">أضف للعربة</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="premium-card-item h-100">
                        <div class="premium-card">
                            <div class="premium-product-media">
                                <span class="premium-promo-badge">خصم 20%</span>
                                <div class="premium-card-actions">
                                    <button type="button" class="premium-action-btn" title="Add to wishlist">
                                        <i class="fa fa-heart-o"></i>
                                    </button>
                                    <button type="button" class="premium-action-btn" data-toggle="modal"
                                        data-target="#premium-static-quickview" title="Quick View">
                                        <i class="czi-eye align-middle"></i>
                                    </button>
                                </div>
                                <div class="premium-card-image">
                                    <a href="#" class="d-block">
                                        <img src="https://placehold.co/200x200?text=غسالة+1" alt="غسالة">
                                    </a>
                                </div>
                            </div>
                            <div class="premium-card-details">
                                <span class="premium-category-tag">غسالات أوتوماتيك</span>
                                <a href="#" class="premium-product-title">غسالة إل جي 10.5 كيلو أبيض...</a>
                                <div class="premium-product-prices">
                                    <span class="premium-price-new">2850 ريال</span>
                                    <del class="premium-price-old">3500 ريال</del>
                                </div>
                                <button class="premium-add-to-cart" type="button">
                                    <i class="fa fa-shopping-cart"></i>
                                    <span class="ms-1">أضف للعربة</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="rp-tab-pane" id="rp-pane-refrigerators" role="tabpanel">
                <div class="owl-carousel owl-theme premium-product-carousel" id="rp-swiper-refrigerators">
                    <div class="premium-card-item h-100">
                        <div class="premium-card">
                            <div class="premium-product-media">
                                <span class="premium-promo-badge">خصم 10%</span>
                                <div class="premium-card-actions">
                                    <button type="button" class="premium-action-btn" title="Add to wishlist">
                                        <i class="fa fa-heart-o"></i>
                                    </button>
                                    <button type="button" class="premium-action-btn" data-toggle="modal"
                                        data-target="#premium-static-quickview" title="Quick View">
                                        <i class="czi-eye align-middle"></i>
                                    </button>
                                </div>
                                <div class="premium-card-image">
                                    <a href="#" class="d-block">
                                        <img src="https://placehold.co/200x200?text=ثلاجة+1" alt="ثلاجة">
                                    </a>
                                </div>
                            </div>
                            <div class="premium-card-details">
                                <span class="premium-category-tag">ثلاجات دولابي</span>
                                <a href="#" class="premium-product-title">ثلاجة دولابي جنرال سوبريم 18 قدم...</a>
                                <div class="premium-product-prices">
                                    <span class="premium-price-new">3150 ريال</span>
                                    <del class="premium-price-old">3500 ريال</del>
                                </div>
                                <button class="premium-add-to-cart" type="button">
                                    <i class="fa fa-shopping-cart"></i>
                                    <span class="ms-1">أضف للعربة</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rp-tab-pane" id="rp-pane-conditioners" role="tabpanel">
                <div class="owl-carousel owl-theme premium-product-carousel" id="rp-swiper-conditioners">
                    <div class="premium-card-item h-100">
                        <div class="premium-card">
                            <div class="premium-product-media">
                                <span class="premium-promo-badge">خصم 15%</span>
                                <div class="premium-card-actions">
                                    <button type="button" class="premium-action-btn" title="Add to wishlist">
                                        <i class="fa fa-heart-o"></i>
                                    </button>
                                    <button type="button" class="premium-action-btn" data-toggle="modal"
                                        data-target="#premium-static-quickview" title="Quick View">
                                        <i class="czi-eye align-middle"></i>
                                    </button>
                                </div>
                                <div class="premium-card-image">
                                    <a href="#" class="d-block">
                                        <img src="https://placehold.co/200x200?text=مكيف+1" alt="مكيف">
                                    </a>
                                </div>
                            </div>
                            <div class="premium-card-details">
                                <span class="premium-category-tag">مكيفات سبليت</span>
                                <a href="#" class="premium-product-title">مكيف سبليت جنرال سوبريم 18000 وحدة...</a>
                                <div class="premium-product-prices">
                                    <span class="premium-price-new">1870 ريال</span>
                                    <del class="premium-price-old">2200 ريال</del>
                                </div>
                                <button class="premium-add-to-cart" type="button">
                                    <i class="fa fa-shopping-cart"></i>
                                    <span class="ms-1">أضف للعربة</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    @endif

</section>