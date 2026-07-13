<div class="col-12 best-selling-section ">
    <div class="h-100">
        <div class="card-body p-0">
            <div class="row d-flex justify-content-between align-items-center mx-1 mb-3">
                <div class="d-flex gap-1 align-items-center">
                    <!-- <img loading="lazy"  class="size-30" src="{{theme_asset(path: "public/assets/front-end/png/best-sellings.png")}}"
                         alt=""> -->
                    <h2 class="header_section_title">{{ translate('best_sellings')}}</h2>
                </div>
                <div>
                    <a class="view-all-btn-yellow"
                       href="{{ route('best-selling-products') }}">
                        {{ translate('view_all') }}
                        <!-- <i class="czi-arrow-{{Session::get('direction') === "rtl" ? 'left mr-1 ml-n1 mt-1 float-left' : 'right ml-1 mr-n1'}}"></i> -->
                    </a>
                </div>
            </div>
            <div class="owl-carousel owl-theme premium-product-carousel" data-slide-items="{{ $bestSellProduct->count() }}">
                @foreach($bestSellProduct as $key=> $bestSellItem)
                    @if($bestSellItem)
                        @php($wishlist_status = Auth::guard('customer')->check() ? \App\Models\Wishlist::where('customer_id', Auth::guard('customer')->id())->where('product_id', $bestSellItem->id)->count() : (session()->has('wish_list') && in_array($bestSellItem->id, session('wish_list')) ? 1 : 0))
                        <div class="premium-card-owl-item">
                            <div class="premium-card">
                                <div class="premium-product-media">
                                     @if($bestSellItem->getActiveCouponCode())
                                         <span class="premium-promo-badge promo-code-badge">
                                             {{ translate('استخدم كود') }} {{ $bestSellItem->getActiveCouponCode() }}
                                         </span>
                                     @elseif(getProductPriceByType(product: $bestSellItem, type: 'discount', result: 'value') > 0)
                                         <span class="premium-promo-badge">
                                             {{ translate('discount') }} {{ getProductPriceByType(product: $bestSellItem, type: 'discount', result: 'string') }}
                                         </span>
                                     @endif

                                    <div class="premium-card-actions">
                                        <button type="button" data-product-id="{{ $bestSellItem['id'] }}"
                                                class="premium-action-btn product-action-add-wishlist"
                                                title="{{ translate('Add_to_wishlist') }}">
                                            <i class="fa {{($wishlist_status == 1?'fa-heart text-danger':'fa-heart-o')}} wishlist_icon_{{$bestSellItem['id']}}"></i>
                                        </button>
                                        <a class="premium-action-btn stopPropagation action-product-quick-view"
                                           href="javascript:"
                                           data-product-id="{{ $bestSellItem->id }}"
                                           title="{{ translate('Quick_View') }}">
                                            <i class="czi-eye align-middle"></i>
                                        </a>
                                    </div>

                                    <div class="premium-card-image">
                                        <a href="{{route('product',$bestSellItem->slug)}}" class="d-block">
                                            <img loading="lazy" src="{{ getStorageImages(path: $bestSellItem->thumbnail_full_url, type: 'product') }}" alt="{{ $bestSellItem['name'] }}">
                                        </a>
                                        @if($bestSellItem->product_type == 'physical' && $bestSellItem->current_stock <= 0)
                                            <span class="out_fo_stock">{{translate('out_of_stock')}}</span>
                                        @endif
                                    </div>
                                    @if($bestSellItem->isAirConditioner())
                                        <span class="product-tax-badge ac-tax-badge">السعر شامل الضريبة والتركيب و 4 متر نحاس + ربل او كرسي + تيب</span>
                                    @else
                                        <span class="product-tax-badge">السعر شامل الضريبة</span>
                                    @endif
                                </div>

                                <div class="premium-card-details">
                                    {{-- Top: dynamic content, grows to fill space --}}
                                    <div class="premium-card-top-info">
                                        @if($bestSellItem->category)
                                            <span class="premium-category-tag">{{ $bestSellItem->category->name }}</span>
                                        @endif

                                        <a href="{{route('product',$bestSellItem->slug)}}" class="premium-product-title" title="{{ $bestSellItem['name'] }}">
                                            {{ $bestSellItem['name'] }}
                                        </a>

                                        @php($overallRating = getOverallRating($bestSellItem['reviews']))
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
                                                    <label class="badge-style">( {{ count($bestSellItem['reviews']) }} )</label>
                                                </span>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Bottom: always at the bottom --}}
                                    <div class="premium-card-bottom-info">
                                        <div class="premium-product-prices">
                                            @if(getProductPriceByType(product: $bestSellItem, type: 'discount', result: 'value') > 0)
                                                <del class="premium-price-old">
                                                    {{ webCurrencyConverter(amount: $bestSellItem->unit_price) }}
                                                </del>
                                            @endif
                                            <span class="premium-price-new">
                                                {{ getProductPriceByType(product: $bestSellItem, type: 'discounted_unit_price', result: 'string') }}
                                            </span>
                                        </div>

                                        <form class="addToCartDynamicForm d-none" id="add-to-cart-form-{{ $bestSellItem->id }}">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $bestSellItem->id }}">
                                            <input type="hidden" name="quantity" value="{{ $bestSellItem->minimum_order_qty ?? 1 }}">
                                        </form>

                                        <button class="premium-add-to-cart product-add-to-cart-button"
                                                type="button"
                                                data-form="#add-to-cart-form-{{ $bestSellItem->id }}"
                                                data-update="{{ translate('update_cart') }}"
                                                data-add="{{ translate('add_to_cart') }}">
                                            <i class="fa fa-shopping-cart"></i>
                                            <span class="ms-1">أضف للعربة</span>
                                        </button>
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
