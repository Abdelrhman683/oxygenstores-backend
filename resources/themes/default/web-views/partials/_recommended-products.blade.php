{{--
    =========================================================
    سكشن: منتجات ننصح بها (Recommended Products - Category Tabs)
    =========================================================
    الوصف:
        يعرض منتجات مقسمة بالتابات بتصميم مطابق تماماً للصورة المرسلة.
        يحتوي على عنوان بشريط أصفر كامل العرض، وتابات باللون الأزرق مع مثلث سفلي نشط.

    الاستايل:
        مكتوب بالكامل في custom.css
    =========================================================
--}}

<<<<<<< Updated upstream
<section class="recommended-products-section container rtl py-4 px-0">
=======
<section class="recommended-products-section rtl custom_pd">
>>>>>>> Stashed changes

    {{-- رأس السكشن: شريط أصفر كامل العرض --}}
    <div class="rp-header-bar">
        <h2 class="rp-section-title mb-0">منتجات ننصح بها</h2>
    </div>

<<<<<<< Updated upstream
    {{-- شريط التابات --}}
    <div class="rp-tabs-wrapper px-3 d-flex justify-content-start">
        <ul class="rp-tabs-nav d-flex p-0 mb-0" id="recommendedTabsNav" role="tablist">
            <li class="rp-tab-item" role="presentation">
                <button class="rp-tab-btn" data-target="rp-pane-conditioners" type="button">مكيفات</button>
            </li>
            <li class="rp-tab-item" role="presentation">
                <button class="rp-tab-btn" data-target="rp-pane-refrigerators" type="button">ثلاجات</button>
            </li>
            <li class="rp-tab-item" role="presentation">
                <button class="rp-tab-btn active" data-target="rp-pane-washers" type="button">الغسالات</button>
            </li>
        </ul>
    </div>

    {{-- محتوى التابات --}}
    <div class="rp-tabs-content mt-4 px-3" id="recommendedTabsContent">

        {{-- تاب 1: الغسالات --}}
        <div class="rp-tab-pane active" id="rp-pane-washers" role="tabpanel">
            <div class="swiper rp-swiper" id="rp-swiper-washers">
                <div class="swiper-wrapper">

                {{-- المنتج 1 --}}
                    <div class="swiper-slide">
                    <div class="rp-card">
                        <div class="rp-card-header">
                            <span class="rp-discount-badge">خصم 11%</span>
                            <div class="rp-card-actions">
                                <button class="rp-action-btn"><i class="fa fa-heart-o"></i></button>
                                <button class="rp-action-btn rp-quickview-btn"
                                        data-title="غسالة جنرال سوبريم تعبئة أمامية 13 كجم غسيل / 8 كجم تجفيف"
                                        data-category="غسالات ملابس تعبأة من الامام"
                                        data-image="https://placehold.co/400x400?text=غسالة+1"
                                        data-price-current="2499 ر.س"
                                        data-price-old="2799 ر.س"
                                        data-discount="11%"
                                        data-desc="غسالة جنرال سوبريم تعبئة أمامية متطورة بسعة غسيل مذهلة تبلغ 13 كجم وتجفيف 8 كجم. تتميز ببرامج غسيل ذكية تناسب كافة أنواع الأقمشة والمحافظة عليها مع توفير كبير للمياه والكهرباء."
                                        data-spec-brand="جنرال سوبريم"
                                        data-spec-capacity="13 غسيل / 8 تجفيف"
                                        data-spec-type="تعبئة أمامية"
                                        data-spec-guarantee="سنتين شامل"
                                        data-spec-motor-guarantee="10 سنوات على المحرك"
                                        data-spec-origin="صيني">
                                    <i class="fa fa-eye"></i>
=======
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
                        <button class="rp-tab-btn {{ $loop->first ? 'active' : '' }}" data-target="rp-pane-{{ $category->id }}" type="button">{{ $category->name }}</button>
                    </li>
                @empty
                @endforelse
                
                @if($hasFeatured && (!$hasRecommendedCategories || count($recommendedCategories) === 0))
                    <li class="rp-tab-item" role="presentation">
                        <button class="rp-tab-btn {{ $activeTab === 'rp-pane-featured' ? 'active' : '' }}" data-target="rp-pane-featured" type="button">المنتجات المميزة</button>
                    </li>
                @endif
                @if($hasBestSell && (!$hasRecommendedCategories || count($recommendedCategories) === 0))
                    <li class="rp-tab-item" role="presentation">
                        <button class="rp-tab-btn {{ $activeTab === 'rp-pane-best_selling' ? 'active' : '' }}" data-target="rp-pane-best_selling" type="button">الأكثر مبيعًا</button>
                    </li>
                @endif
                @if($hasLatest && (!$hasRecommendedCategories || count($recommendedCategories) === 0))
                    <li class="rp-tab-item" role="presentation">
                        <button class="rp-tab-btn {{ $activeTab === 'rp-pane-latest' ? 'active' : '' }}" data-target="rp-pane-latest" type="button">أحدث المنتجات</button>
                    </li>
                @endif
            </ul>
        </div>

        <div class="rp-tabs-content mt-4 container" id="recommendedTabsContent">

        @forelse($recommendedCategories as $index => $category)
            <div class="rp-tab-pane {{ $index === 0 ? 'active' : '' }}" id="rp-pane-{{ $category->id }}" role="tabpanel">
                <div class="owl-carousel owl-theme premium-product-carousel" id="rp-swiper-{{ $category->id }}">
                    @forelse($category->product as $product)
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
                                        <button type="button" class="premium-action-btn" title="Add to wishlist">
                                            <i class="fa fa-heart-o"></i>
                                        </button>
                                        <button type="button" class="premium-action-btn" data-toggle="modal" data-target="#premium-quickview-{{ $product->id }}" title="Quick View">
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
                                    <span class="premium-category-tag">{{ $category->name }}</span>
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
                                    <button class="premium-add-to-cart" type="button" onclick="addToCart({{ $product->id }})">
                                        <i class="fa fa-shopping-cart"></i>
                                        <span class="ms-1">أضف للعربة</span>
                                    </button>
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
>>>>>>> Stashed changes
                                </button>
                            </div>
                        </div>
                        <div class="rp-card-image">
                            <img src="https://placehold.co/200x200?text=غسالة+1" alt="غسالة">
                        </div>
                        <div class="rp-card-body">
                            <div class="rp-card-category">غسالات ملابس تعبأة من الامام</div>
                            <h3 class="rp-card-title">غسالة جنرال سوبريم تعبئة أمامية 13 كجم غسيل / 8...</h3>
                            <div class="rp-card-price">
                                <del class="rp-old-price">2799 ر.س</del>
                                <span class="rp-current-price">2499 ر.س</span>
                            </div>
                        </div>
                        <div class="rp-card-footer">
                            <button class="rp-add-to-cart">
                                <i class="fa fa-shopping-cart"></i>
                                أضف للعربة
                            </button>
                        </div>
                    </div>
                </div>

                {{-- المنتج 2 --}}
                    <div class="swiper-slide">
                    <div class="rp-card">
                        <div class="rp-card-header">
                            <span class="rp-discount-badge">خصم 27%</span>
                            <div class="rp-card-actions">
                                <button class="rp-action-btn"><i class="fa fa-heart-o"></i></button>
                                <button class="rp-action-btn rp-quickview-btn"
                                        data-title="غسالة جنرال سوبريم حوضين 12 كيلو غسيل"
                                        data-category="غسالات حوضين"
                                        data-image="https://placehold.co/400x400?text=غسالة+2"
                                        data-price-current="950 ر.س"
                                        data-price-old="1301 ر.س"
                                        data-discount="27%"
                                        data-desc="غسالة جنرال سوبريم حوضين بسعة 12 كيلو لتلبية كافة احتياجات الغسيل للمنزل. هيكل بلاستيكي مقاوم للصدأ، تشغيل ميكانيكي قوي وأداء متميز في التنشيف والغسيل."
                                        data-spec-brand="جنرال سوبريم"
                                        data-spec-capacity="12 كيلو"
                                        data-spec-type="حوضين"
                                        data-spec-guarantee="سنتين شامل"
                                        data-spec-motor-guarantee="خمس سنوات على المحرك"
                                        data-spec-origin="صيني">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="rp-card-image">
                            <img src="https://placehold.co/200x200?text=غسالة+2" alt="غسالة">
                        </div>
                        <div class="rp-card-body">
                            <div class="rp-card-category">غسالات حوضين</div>
                            <h3 class="rp-card-title">غسالة جنرال سوبريم حوضين 12 كيلو...</h3>
                            <div class="rp-card-price">
                                <del class="rp-old-price">1301 ر.س</del>
                                <span class="rp-current-price">950 ر.س</span>
                            </div>
                        </div>
                        <div class="rp-card-footer">
                            <button class="rp-add-to-cart">
                                <i class="fa fa-shopping-cart"></i>
                                أضف للعربة
                            </button>
                        </div>
                    </div>
                </div>

                {{-- المنتج 3 --}}
                    <div class="swiper-slide">
                    <div class="rp-card">
                        <div class="rp-card-header">
                            <span class="rp-discount-badge">خصم 30%</span>
                            <div class="rp-card-actions">
                                <button class="rp-action-btn"><i class="fa fa-heart-o"></i></button>
                                <button class="rp-action-btn rp-quickview-btn"
                                        data-title="غسالة جنرال سوبريم حوضين 10 كيلو غسيل"
                                        data-category="غسالات حوضين"
                                        data-image="https://placehold.co/400x400?text=غسالة+3"
                                        data-price-current="839 ر.س"
                                        data-price-old="1199 ر.س"
                                        data-discount="30%"
                                        data-desc="غسالة جنرال سوبريم حوضين بسعة غسيل 10 كيلو مناسبة للعائلات المتوسطة. لوحة تحكم علوية سهلة الاستخدام ومحرك قوي للغاية مع كفاءة طاقة ممتازة."
                                        data-spec-brand="جنرال سوبريم"
                                        data-spec-capacity="10 كيلو"
                                        data-spec-type="حوضين"
                                        data-spec-guarantee="سنتين شامل"
                                        data-spec-motor-guarantee="خمس سنوات على المحرك"
                                        data-spec-origin="صيني">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="rp-card-image">
                            <img src="https://placehold.co/200x200?text=غسالة+3" alt="غسالة">
                        </div>
                        <div class="rp-card-body">
                            <div class="rp-card-category">غسالات حوضين</div>
                            <h3 class="rp-card-title">غسالة جنرال سوبريم حوضين 10 كيلو...</h3>
                            <div class="rp-card-price">
                                <del class="rp-old-price">1199 ر.س</del>
                                <span class="rp-current-price">839 ر.س</span>
                            </div>
                        </div>
                        <div class="rp-card-footer">
                            <button class="rp-add-to-cart">
                                <i class="fa fa-shopping-cart"></i>
                                أضف للعربة
                            </button>
                        </div>
                    </div>
                </div>

                {{-- المنتج 4 --}}
                    <div class="swiper-slide">
                    <div class="rp-card">
                        <div class="rp-card-header">
                            <span class="rp-discount-badge">خصم 38%</span>
                            <div class="rp-card-actions">
                                <button class="rp-action-btn"><i class="fa fa-heart-o"></i></button>
                                <button class="rp-action-btn rp-quickview-btn"
                                        data-title="غسالة جنرال سوبريم حوضين 8 كجم أبيض/أحمر"
                                        data-category="غسالات حوضين"
                                        data-image="https://placehold.co/400x400?text=غسالة+4"
                                        data-price-current="650 ر.س"
                                        data-price-old="1048 ر.س"
                                        data-discount="38%"
                                        data-desc="غسالة جنرال سوبريم حوضين بسعة 8 كجم وبتصميم جذاب بلونين أبيض وأحمر. حوض غسيل وحوض تجفيف منفصلين، تشغيل مرن وموفرة للكهرباء."
                                        data-spec-brand="جنرال سوبريم"
                                        data-spec-capacity="8 كجم"
                                        data-spec-type="حوضين"
                                        data-spec-guarantee="سنتين شامل"
                                        data-spec-motor-guarantee="خمس سنوات على المحرك"
                                        data-spec-origin="صيني">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="rp-card-image">
                            <img src="https://placehold.co/200x200?text=غسالة+4" alt="غسالة">
                        </div>
                        <div class="rp-card-body">
                            <div class="rp-card-category">غسالات حوضين</div>
                            <h3 class="rp-card-title">غسالة جنرال سوبريم حوضين 8 كجم، ابيض/اح...</h3>
                            <div class="rp-card-price">
                                <del class="rp-old-price">1048 ر.س</del>
                                <span class="rp-current-price">650 ر.س</span>
                            </div>
                        </div>
                        <div class="rp-card-footer">
                            <button class="rp-add-to-cart">
                                <i class="fa fa-shopping-cart"></i>
                                أضف للعربة
                            </button>
                        </div>
                    </div>
                </div>

                {{-- المنتج 5 --}}
                    <div class="swiper-slide">
                    <div class="rp-card">
                        <div class="rp-card-header">
                            <span class="rp-discount-badge">خصم 30%</span>
                            <div class="rp-card-actions">
                                <button class="rp-action-btn"><i class="fa fa-heart-o"></i></button>
                                <button class="rp-action-btn rp-quickview-btn"
                                        data-title="غسالة جنرال سوبريم حوضين 6 كيلو"
                                        data-category="غسالات حوضين"
                                        data-image="https://placehold.co/400x400?text=غسالة+5"
                                        data-price-current="629 ر.س"
                                        data-price-old="899 ر.س"
                                        data-discount="30%"
                                        data-desc="غسالة جنرال سوبريم حوضين بسعة غسيل عملية تبلغ 6 كيلو، مثالية للاستخدام اليومي الخفيف والمساحات الصغيرة بفضل الحجم المتناسق والقوة الفائقة."
                                        data-spec-brand="جنرال سوبريم"
                                        data-spec-capacity="6 كيلو"
                                        data-spec-type="حوضين"
                                        data-spec-guarantee="سنتين شامل"
                                        data-spec-motor-guarantee="خمس سنوات على المحرك"
                                        data-spec-origin="صيني">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="rp-card-image">
                            <img src="https://placehold.co/200x200?text=غسالة+5" alt="غسالة">
                        </div>
                        <div class="rp-card-body">
                            <div class="rp-card-category">غسالات حوضين</div>
                            <h3 class="rp-card-title">غسالة جنرال سوبريم حوضين 6 كيلو...</h3>
                            <div class="rp-card-price">
                                <del class="rp-old-price">899 ر.س</del>
                                <span class="rp-current-price">629 ر.س</span>
                            </div>
                        </div>
                        <div class="rp-card-footer">
                            <button class="rp-add-to-cart">
                                <i class="fa fa-shopping-cart"></i>
                                أضف للعربة
                            </button>
                        </div>
                    </div>
                </div>

                {{-- المنتج 6 --}}
                    <div class="swiper-slide">
                    <div class="rp-card">
                        <div class="rp-card-header">
                            <span class="rp-discount-badge">خصم 43%</span>
                            <div class="rp-card-actions">
                                <button class="rp-action-btn"><i class="fa fa-heart-o"></i></button>
                                <button class="rp-action-btn rp-quickview-btn"
                                        data-title="غسالة جنرال سوبريم تعبئة أمامية 10 كيلو / 14 برنامج"
                                        data-category="غسالات ملابس تعبأة من الامام"
                                        data-image="https://placehold.co/400x400?text=غسالة+6"
                                        data-price-current="1699 ر.س"
                                        data-price-old="2989 ر.س"
                                        data-discount="43%"
                                        data-desc="غسالة جنرال سوبريم تعبئة أمامية بسعة 10 كيلو مع 14 برنامج تشغيل متكامل لتناسب جميع أقمشة الملابس بدقة فائقة ومحرك هادئ وموفر للطاقة."
                                        data-spec-brand="جنرال سوبريم"
                                        data-spec-capacity="10 كيلو"
                                        data-spec-type="تعبئة أمامية"
                                        data-spec-guarantee="سنتين شامل"
                                        data-spec-motor-guarantee="10 سنوات على المحرك"
                                        data-spec-origin="صيني">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="rp-card-image">
                            <img src="https://placehold.co/200x200?text=غسالة+6" alt="غسالة">
                        </div>
                        <div class="rp-card-body">
                            <div class="rp-card-category">غسالات ملابس تعبأة من الامام</div>
                            <h3 class="rp-card-title">غسالة جنرال سوبريم تعبئة أمامية 10 كيلو، 14 برنامج...</h3>
                            <div class="rp-card-price">
                                <del class="rp-old-price">2989 ر.س</del>
                                <span class="rp-current-price">1699 ر.س</span>
                            </div>
                        </div>
                        <div class="rp-card-footer">
                            <button class="rp-add-to-cart">
                                <i class="fa fa-shopping-cart"></i>
                                أضف للعربة
                            </button>
                        </div>
                    </div>
                </div>

                {{-- المنتج 7 --}}
                    <div class="swiper-slide">
                    <div class="rp-card">
                        <div class="rp-card-header">
                            <span class="rp-discount-badge">خصم 39%</span>
                            <div class="rp-card-actions">
                                <button class="rp-action-btn"><i class="fa fa-heart-o"></i></button>
                                <button class="rp-action-btn rp-quickview-btn"
                                        data-title="جنرال سوبريم غسالة تعبئة أمامية 6 كيلو فضي"
                                        data-category="غسالات ملابس تعبأة من الامام"
                                        data-image="https://placehold.co/400x400?text=غسالة+7"
                                        data-price-current="1099 ر.س"
                                        data-price-old="1802 ر.س"
                                        data-discount="39%"
                                        data-desc="غسالة جنرال سوبريم تعبئة أمامية بسعة 6 كيلو ولون فضي أنيق، تناسب الاحتياجات اليومية بكفاءة عالية وأبعاد مدمجة ممتازة للشقق السكنية."
                                        data-spec-brand="جنرال سوبريم"
                                        data-spec-capacity="6 كيلو"
                                        data-spec-type="تعبئة أمامية"
                                        data-spec-guarantee="سنتين شامل"
                                        data-spec-motor-guarantee="10 سنوات على المحرك"
                                        data-spec-origin="صيني">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="rp-card-image">
                            <img src="https://placehold.co/200x200?text=غسالة+7" alt="غسالة">
                        </div>
                        <div class="rp-card-body">
                            <div class="rp-card-category">غسالات ملابس تعبأة من الامام</div>
                            <h3 class="rp-card-title">جنرال سوبريم غسالة تعبئة أمامية 6 كيلو، فضي...</h3>
                            <div class="rp-card-price">
                                <del class="rp-old-price">1802 ر.س</del>
                                <span class="rp-current-price">1099 ر.س</span>
                            </div>
                        </div>
                        <div class="rp-card-footer">
                            <button class="rp-add-to-cart">
                                <i class="fa fa-shopping-cart"></i>
                                أضف للعربة
                            </button>
                        </div>
                    </div>
                </div>

                </div>{{-- /swiper-wrapper --}}
                <div class="swiper-button-next rp-swiper-next"></div>
                <div class="swiper-button-prev rp-swiper-prev"></div>
                <div class="swiper-pagination rp-swiper-pagination"></div>
            </div>{{-- /swiper --}}
        </div>

        {{-- تاب 2: ثلاجات --}}
        <div class="rp-tab-pane" id="rp-pane-refrigerators" role="tabpanel">
            <div class="swiper rp-swiper" id="rp-swiper-refrigerators">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                    <div class="rp-card">
                        <div class="rp-card-header">
                            <span class="rp-discount-badge">خصم 10%</span>
                            <div class="rp-card-actions">
                                <button class="rp-action-btn"><i class="fa fa-heart-o"></i></button>
                                <button class="rp-action-btn rp-quickview-btn"
                                        data-title="ثلاجة دولابي جنرال سوبريم 18 قدم"
                                        data-category="ثلاجات دولابي"
                                        data-image="https://placehold.co/400x400?text=ثلاجة+1"
                                        data-price-current="3150 ر.س"
                                        data-price-old="3500 ر.س"
                                        data-discount="10%"
                                        data-desc="ثلاجة دولابي جنرال سوبريم بسعة 18 قدم بتوزيع هواء ذكي ونظام تبريد متكامل خالي من الثلج (No Frost)، موفرة للطاقة وبتصميم جذاب يضيف فخامة لمطبخك."
                                        data-spec-brand="جنرال سوبريم"
                                        data-spec-capacity="18 قدم"
                                        data-spec-type="ثلاجة دولابي"
                                        data-spec-guarantee="سنتين شامل"
                                        data-spec-motor-guarantee="خمس سنوات على الكمبروسر"
                                        data-spec-origin="صيني">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="rp-card-image">
                            <img src="https://placehold.co/200x200?text=ثلاجة+1" alt="ثلاجة">
                        </div>
                        <div class="rp-card-body">
                            <div class="rp-card-category">ثلاجات دولابي</div>
                            <h3 class="rp-card-title">ثلاجة دولابي جنرال سوبريم 18 قدم...</h3>
                            <div class="rp-card-price">
                                <del class="rp-old-price">3500 ر.س</del>
                                <span class="rp-current-price">3150 ر.س</span>
                            </div>
                        </div>
                        <div class="rp-card-footer">
                            <button class="rp-add-to-cart">
                                <i class="fa fa-shopping-cart"></i>
                                أضف للعربة
                            </button>
                        </div>
                    </div>
                </div>{{-- /swiper-slide --}}
                </div>{{-- /swiper-wrapper --}}
                <div class="swiper-button-next rp-swiper-next"></div>
                <div class="swiper-button-prev rp-swiper-prev"></div>
                <div class="swiper-pagination rp-swiper-pagination"></div>
            </div>{{-- /swiper --}}
        </div>

        {{-- تاب 3: مكيفات --}}
        <div class="rp-tab-pane" id="rp-pane-conditioners" role="tabpanel">
            <div class="swiper rp-swiper" id="rp-swiper-conditioners">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                    <div class="rp-card">
                        <div class="rp-card-header">
                            <span class="rp-discount-badge">خصم 15%</span>
                            <div class="rp-card-actions">
                                <button class="rp-action-btn"><i class="fa fa-heart-o"></i></button>
                                <button class="rp-action-btn rp-quickview-btn"
                                        data-title="مكيف اسبليت يونان 12100 وحدة بارد"
                                        data-category="مكيفات اسبليت"
                                        data-image="https://placehold.co/400x400?text=مكيف+1"
                                        data-price-current="1699 ر.س"
                                        data-price-old="1899 ر.س"
                                        data-discount="15%"
                                        data-desc="هو الخيار المثالي لأجوائك الحارة يقدم لك برودة سريعة وفعالة في كل زاوية من الغرفة تصميمه أنيق ومناسب لكل المساحات، سواء كان في البيت أو المكتب مع تقنيات حديثة، يعطيك جو بارد ومنعش بكل راحة وسهولة."
                                        data-spec-brand="يونان"
                                        data-spec-capacity="12100 وحدة بارد"
                                        data-spec-type="مكيف سبليت"
                                        data-spec-guarantee="سنتين شامل"
                                        data-spec-motor-guarantee="سبع سنوات على الكمبروسر"
                                        data-spec-origin="صيني">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="rp-card-image">
                            <img src="https://placehold.co/200x200?text=مكيف+1" alt="مكيف">
                        </div>
                        <div class="rp-card-body">
                            <div class="rp-card-category">مكيفات سبليت</div>
                            <h3 class="rp-card-title">مكيف سبليت جنرال سوبريم 18000 وحدة...</h3>
                            <div class="rp-card-price">
                                <del class="rp-old-price">2200 ر.س</del>
                                <span class="rp-current-price">1870 ر.س</span>
                            </div>
                        </div>
                        <div class="rp-card-footer">
                            <button class="rp-add-to-cart">
                                <i class="fa fa-shopping-cart"></i>
                                أضف للعربة
                            </button>
                        </div>
                    </div>
                </div>{{-- /swiper-slide --}}
                </div>{{-- /swiper-wrapper --}}
                <div class="swiper-button-next rp-swiper-next"></div>
                <div class="swiper-button-prev rp-swiper-prev"></div>
                <div class="swiper-pagination rp-swiper-pagination"></div>
            </div>{{-- /swiper --}}
        </div>

    </div>
    @endif


</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const swiperConfig = {
        dir: 'rtl',
        loop: false,
        spaceBetween: 12,
        slidesPerView: 2,
    navigation: false,
        pagination: {
            el: '.rp-swiper-pagination',
            clickable: true,
        },
        breakpoints: {
            480:  { slidesPerView: 3, spaceBetween: 12 },
            768:  { slidesPerView: 4, spaceBetween: 14 },
            992:  { slidesPerView: 5, spaceBetween: 14 },
            1200: { slidesPerView: 6, spaceBetween: 16 },
        }
    };

    const rpSwiperWashers      = new Swiper('#rp-swiper-washers',      swiperConfig);
    const rpSwiperFridges      = new Swiper('#rp-swiper-refrigerators', swiperConfig);
    const rpSwiperConditioners = new Swiper('#rp-swiper-conditioners',  swiperConfig);

    const tabBtns = document.querySelectorAll('#recommendedTabsNav .rp-tab-btn');
    const tabPanes = document.querySelectorAll('#recommendedTabsContent .rp-tab-pane');

    tabBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            tabBtns.forEach(b => b.classList.remove('active'));
            tabPanes.forEach(p => p.classList.remove('active'));
            btn.classList.add('active');
            var target = document.getElementById(btn.getAttribute('data-target'));
            if (target) {
                target.classList.add('active');
                [rpSwiperWashers, rpSwiperFridges, rpSwiperConditioners].forEach(s => s.update());
            }
        });
    });

    const qvBtns = document.querySelectorAll('.rp-quickview-btn');
    const modalContent = document.getElementById('quick-view-modal');

    qvBtns.forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();

            const title = this.getAttribute('data-title');
            const category = this.getAttribute('data-category');
            const image = this.getAttribute('data-image');
            const priceCurrent = this.getAttribute('data-price-current');
            const priceOld = this.getAttribute('data-price-old');
            const desc = this.getAttribute('data-desc');
            const specBrand = this.getAttribute('data-spec-brand');
            const specCapacity = this.getAttribute('data-spec-capacity');
            const specType = this.getAttribute('data-spec-type');
            const specGuarantee = this.getAttribute('data-spec-guarantee');
            const specMotor = this.getAttribute('data-spec-motor-guarantee');
            const specOrigin = this.getAttribute('data-spec-origin');

            const html = `
                <div class="modal-body rp-quickview-modal-body rtl">
                    <div class="rp-qv-top-row d-flex justify-content-between align-items-center mb-3">
                        <button type="button" class="rp-qv-close-btn" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <span class="rp-qv-code-badge">إستخدم كود OX26</span>
                    </div>

                    <div class="row g-4">
                        <div class="col-lg-5 col-md-5 col-12 text-center">
                            <div class="rp-qv-main-image-wrap mb-3">
                                <img id="rp-qv-main-image" src="${image}" alt="${title}" class="img-fluid" style="max-height: 280px; object-fit: contain;">
                            </div>
                            <div class="rp-qv-thumbnails-wrap d-flex justify-content-center gap-2">
                                <img src="${image}" class="rp-qv-thumb active" style="width: 60px; height: 60px; object-fit: contain; cursor: pointer;">
                                <img src="${image.replace('?text=', '?text=عرض+جانبي+')}" class="rp-qv-thumb" style="width: 60px; height: 60px; object-fit: contain; cursor: pointer;">
                                <img src="${image.replace('?text=', '?text=تفاصيل+')}" class="rp-qv-thumb" style="width: 60px; height: 60px; object-fit: contain; cursor: pointer;">
                            </div>
                        </div>

                        <div class="col-lg-7 col-md-7 col-12">
                            <div class="rp-qv-category-box mb-2">${category}</div>
                            <h2 class="rp-qv-title mb-3">${title}</h2>
                            <p class="rp-qv-desc mb-3">${desc}</p>

                            <div class="rp-qv-price-section mb-3">
                                <div class="d-flex align-items-center gap-3">
                                    <span class="rp-qv-price-current">${priceCurrent}</span>
                                    <del class="rp-qv-price-old">${priceOld}</del>
                                </div>
                                <div class="rp-qv-tax-note mt-1">السعر شامل الضريبة والتركيب و 4 متر نحاس + ريل او كرسي + تيب</div>
                            </div>

                            <div class="rp-qv-specs-container mb-4">
                                <div class="row g-3">
                                    <div class="col-6"><strong>الشركة:</strong> <span>${specBrand}</span></div>
                                    <div class="col-6"><strong>السعة / القوة:</strong> <span>${specCapacity}</span></div>
                                    <div class="col-6"><strong>نوع الجهاز:</strong> <span>${specType}</span></div>
                                    <div class="col-6"><strong>الضمان الشامل:</strong> <span>${specGuarantee}</span></div>
                                    <div class="col-6"><strong>ضمان الموتور:</strong> <span>${specMotor}</span></div>
                                    <div class="col-6"><strong>الصناعة:</strong> <span>${specOrigin}</span></div>
                                </div>
                            </div>

                            <div class="rp-qv-action-row d-flex align-items-center gap-3 mb-4">
                                <div class="rp-qv-qty-box d-flex align-items-center gap-2">
                                    <span class="rp-qv-qty-label">الكمية</span>
                                    <div class="d-flex align-items-center quantity-box rounded border">
                                        <button type="button" class="btn-number btn-sm px-2 py-1 rp-qty-minus">-</button>
                                        <input type="text" id="rp-qv-qty-input" value="1" class="form-control text-center border-0 p-0" style="width: 32px; background:transparent;" readonly>
                                        <button type="button" class="btn-number btn-sm px-2 py-1 rp-qty-plus">+</button>
                                    </div>
                                </div>
                                <button type="button" class="btn rp-qv-add-btn flex-grow-1"><i class="fa fa-shopping-cart ml-2"></i> أضف للعربة</button>
                            </div>

                            <div class="rp-qv-payments d-flex align-items-center gap-3 flex-wrap">
                                <img src="https://emkan.com.sa/wp-content/themes/emkan/assets/images/logo-ar.svg" alt="Emkan" class="payment-logo-img" style="height: 25px;">
                                <img src="https://cdn.tamara.co/assets/logo.svg" alt="Tamara" class="payment-logo-img" style="height: 25px;">
                                <img src="https://cdn.tabby.ai/assets/imgs/tabby-logo.svg" alt="Tabby" class="payment-logo-img" style="height: 25px;">
                            </div>
                        </div>
                    </div>
                </div>
            `;

            modalContent.innerHTML = html;

            const qtyInput = modalContent.querySelector('#rp-qv-qty-input');
            const btnMinus = modalContent.querySelector('.rp-qty-minus');
            const btnPlus = modalContent.querySelector('.rp-qty-plus');

            btnMinus.addEventListener('click', function() {
                let currentVal = parseInt(qtyInput.value);
                if (currentVal > 1) {
                    qtyInput.value = currentVal - 1;
                }
            });

            btnPlus.addEventListener('click', function() {
                let currentVal = parseInt(qtyInput.value);
                qtyInput.value = currentVal + 1;
            });

            const mainImg = modalContent.querySelector('#rp-qv-main-image');
            const thumbs = modalContent.querySelectorAll('.rp-qv-thumb');
            thumbs.forEach(thumb => {
                thumb.addEventListener('click', function() {
                    thumbs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    mainImg.src = this.src;
                });
            });

            $('#quick-view').modal('show');
        });
    });
});
</script>
