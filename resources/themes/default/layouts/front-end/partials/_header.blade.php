@php
    $announcement = getWebConfig(name: 'announcement');
@endphp

@if (isset($announcement) && $announcement['status']==1)
    <div class="text-center position-relative px-4 py-1 d--none" id="announcement"
         style="background-color: {{ $announcement['color'] }};color:{{$announcement['text_color']}}">
        <span>{{ $announcement['announcement'] }} </span>
        <span class="__close-announcement web-announcement-slideUp">X</span>
    </div>
@endif
<header class="rtl __inline-10">
    <!-- <div class="topbar">
        <div class="container">
            <div>
                <div class="topbar-text dropdown d-md-none ms-auto">
                    <a class="topbar-link direction-ltr" href="tel: {{ $web_config['phone'] }}">
                        <i class="fa fa-phone"></i> {{ $web_config['phone'] }}
                    </a>
                </div>
                <div class="d-none d-md-block mr-2 text-nowrap">
                    <a class="topbar-link d-none d-md-inline-block direction-ltr" href="tel:{{ $web_config['phone'] }}">
                        <i class="fa fa-phone"></i> {{ $web_config['phone'] }}
                    </a>
                </div>
            </div>

            <div>
                @php
                    $currency_model = getWebConfig(name: 'currency_model');
                @endphp
                @if($currency_model=='multi_currency')
                    <div class="topbar-text dropdown disable-autohide mr-4">
                        <a class="topbar-link dropdown-toggle" href="#" data-toggle="dropdown">
                            <span>{{session('currency_code')}} {{session('currency_symbol')}}</span>
                        </a>
                        <ul class="text-align-direction dropdown-menu dropdown-menu-{{Session::get('direction') === "rtl" ? 'right' : 'left'}} min-width-160px">
                            @foreach (\App\Models\Currency::where('status', 1)->get() as $key => $currency)
                                <li class="dropdown-item cursor-pointer get-currency-change-function"
                                    data-code="{{$currency['code']}}">
                                    {{ $currency->name }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="topbar-text dropdown disable-autohide  __language-bar text-capitalize">
                    <a class="topbar-link dropdown-toggle" href="#" data-toggle="dropdown">
                        @foreach($web_config['language'] as $data)
                            @if($data['code'] == getDefaultLanguage())
                                    <?php
                                    $langFlagCode = $data['code'];
                                    if (\Illuminate\Support\Str::contains($data['code'], '-')) {
                                        $countryCodeArr = explode('-', $data['code']);
                                        $langFlagCode = $countryCodeArr[0];
                                    }
                                    ?>
                                <img class="mr-2" width="20"
                                     src="{{theme_asset(path: 'public/assets/front-end/img/flags/'.strtolower($langFlagCode).'.png')}}"
                                     alt="{{ $data['name'] }}">
                                {{$data['name']}}
                            @endif
                        @endforeach
                    </a>
                    <ul class="text-align-direction dropdown-menu dropdown-menu-{{Session::get('direction') === "rtl" ? 'right' : 'left'}}">
                        @foreach($web_config['language'] as $key =>$data)
                            @if($data['status'] == 1)
                                    <?php
                                    $langFlagCode = $data['code'];
                                    if (\Illuminate\Support\Str::contains($data['code'], '-')) {
                                        $countryCodeArr = explode('-', $data['code']);
                                        $langFlagCode = $countryCodeArr[0];
                                    }
                                    ?>
                                <li class="change-language" data-action="{{route('change-language')}}" data-language-code="{{$data['code']}}">
                                    <a class="dropdown-item pb-1" href="javascript:">
                                        <img class="mr-2"
                                             width="20"
                                             src="{{theme_asset(path: 'public/assets/front-end/img/flags/'.strtolower($langFlagCode).'.png')}}"
                                             alt="{{$data['name']}}"/>
                                        <span class="text-capitalize">{{$data['name']}}</span>
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div> -->

    <div class="navbar-sticky bg-light mobile-head">
        <div class="navbar navbar-expand-lg top_navbar">
            <div class="my-container">
                          <div class="top-navbar-row1 d-flex align-items-center justify-content-between w-100  " >

                <button class="navbar-toggler p-0 text-white border-0 d-lg-none flex-shrink-0" type="button" data-toggle="collapse" data-target="#navbarCollapse">
                    <i class="czi-menu fs-20" style="color: #fff !important;"></i>
                </button>

                <a class="navbar-brand d-none d-sm-block flex-shrink-0 __min-w-7rem" href="{{route('home')}}">
                    <img class="__inline-10-"
                         src="{{ getStorageImages(path: $web_config['web_logo'], type: 'logo') }}"
                         alt="{{$web_config['company_name']}}">
                </a>
                <a class="navbar-brand d-sm-none flex-grow-1 text-center" href="{{route('home')}}">
                    <img class="__inline-12_header"
                         src="{{ getStorageImages(path: $web_config['mob_logo'], type: 'logo') }}"
                         alt="{{$web_config['company_name']}}">
                </a>

                <div class="search-section flex-grow-1 mx-3 mx-lg-5 d-none d-lg-block">
                    <form action="{{route('products')}}" type="submit" class="search_form m-0">
                        <input type="hidden" name="global_search_input" value="1">
                        <input name="data_from" value="search" hidden>
                        <input name="page" value="1" hidden>
                        <div class="custom-search-input-group">
                            <input class="form-control search-bar-input custom-search-field text-align-direction"
                                   type="search" autocomplete="off" data-given-value=""
                                   placeholder="كيف نقدر نساعدك؟ ابحث هنا"
                                   name="name" value="{{ request('name') }}">
                            <button class="custom-search-btn" type="submit">
                                <i class="fa fa-search"></i>
                            </button>
                            <div class="card search-card position-absolute w-100 mt-1" style="display: none; z-index: 9999; box-shadow: 0 4px 12px rgba(0,0,0,0.15); top: 100%;">
                                <div class="card-body p-2">
                                    <div class="search-result-box __h-400px overflow-x-hidden overflow-y-auto"></div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Actions -->
                <div class="actions-section d-flex align-items-center text-white flex-shrink-0">
                    @php
                        $currentBranchId = auth('customer')->check()
                            ? auth('customer')->user()->branch_id
                            : session('branch_id');
                        $currentBranch = $currentBranchId
                            ? \App\Models\Branch::find($currentBranchId)
                            : null;
                    @endphp
                    <div class="action-item d-none d-md-inline-flex cursor-pointer" data-toggle="modal" data-target="#branchModal" style="cursor: pointer;" id="branch-selector-btn">
                        <i class="fa fa-map-marker"></i>
                        <span class="d-none d-lg-block">الفرع (<span id="current-branch-name">{{ $currentBranch?->name ?? 'اختر الفرع' }}</span>)</span>
                    </div>
                    <span class="separator-line d-none d-md-inline-block">|</span>
                    <a href="{{route('wishlists')}}" class="action-item d-none d-md-inline-flex">
                        <i class="fa fa-heart-o"></i>
                        <span class="d-none d-lg-block">المفضلة</span>
                    </a>
                    <span class="separator-line d-none d-md-inline-block">|</span>
                    <a href="{{route('products')}}" class="action-item d-none d-md-inline-flex">
                        <i class="fa fa-shopping-bag"></i>
                        <span class="d-none d-lg-block">المتجر</span>
                    </a>
                    <span class="separator-line d-none d-md-inline-block">|</span>
                    @if(auth('customer')->check())
                        <div class="dropdown d-none d-md-inline-block user_drop">
                            <a class="action-item dropdown-toggle cursor-pointer" data-toggle="dropdown">
                                <i class="fa fa-user-circle"></i>
                                <span>{{ Str::limit(auth('customer')->user()->f_name, 10) }}</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-{{Session::get('direction') === "rtl" ? 'left' : 'right'}}">
                                <a class="dropdown-item" href="{{route('account-oder')}}"> {{ translate('my_Order')}} </a>
                                <a class="dropdown-item" href="{{route('user-account')}}"> {{ translate('my_Profile')}}</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{route('customer.auth.logout')}}">{{ translate('logout')}}</a>
                            </div>
                        </div>
                    @else
                        <a href="{{route('customer.auth.login')}}" class="action-item d-none d-md-inline-flex">
                            <i class="fa fa-user-o"></i>
                            <span class="d-none d-lg-block">تسجيل الدخول</span>
                        </a>
                    @endif
                    <span class="separator-line d-none d-md-inline-block">|</span>
                    <!-- Cart (visible on all screens) -->
                    <div id="cart_items" class="m-0">
                        @include('layouts.front-end.partials._cart')
                    </div>
                </div>

            </div>
            </div>           
   

            <div class="top-navbar-row2 d-lg-none w-100 px-2 pb-2">
                <form action="{{route('products')}}" type="submit" class="search_form m-0">
                    <input type="hidden" name="global_search_input" value="1">
                    <input name="data_from" value="search" hidden>
                    <input name="page" value="1" hidden>
                    <div class="custom-search-input-group">
                        <input class="form-control search-bar-input search-bar-input-mobile custom-search-field text-align-direction"
                               type="search" autocomplete="off" data-given-value=""
                               placeholder="كيف نقدر نساعدك؟ ابحث هنا"
                               name="name" value="{{ request('name') }}">
                        <button class="custom-search-btn" type="submit">
                            <i class="fa fa-search"></i>
                        </button>
                        <div class="card search-card mobile-search-card position-absolute w-100 mt-1" style="display: none; z-index: 9999; box-shadow: 0 4px 12px rgba(0,0,0,0.15); top: 100%;">
                            <div class="card-body p-2">
                                <div class="search-result-box __h-400px overflow-x-hidden overflow-y-auto"></div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>

        <div class="navbar navbar-expand-lg navbar-stuck-menu bottom_navbar">
            <div class="container px-10px">
                <div class="collapse navbar-collapse text-align-direction" id="navbarCollapse">
                    <div class="mobile-drawer-header d-lg-none">
                        <span class="mobile-drawer-title">القائمة</span>
                        <button class="mobile-drawer-close" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-label="Close menu">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>

                    <ul class="navbar-nav w-100  header-categories-nav align-items-center justify-content-center" >
                        <li class="nav-item {{request()->is('/')?'active':''}}">
                            <a class="nav-link" href="{{route('home')}}">{{ translate('الرئيسية')}}</a>
                        </li>

                        @foreach($categories as $category)
                            @if ($category->childes->count() > 0)
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="{{ route('category-products', ['slug' => $category['slug']]) }}" data-toggle="dropdown">
                                        {{ $category['name'] }}
                                    </a>
                                    <ul class="dropdown-menu text-align-direction">
                                        @foreach($category['childes'] as $subCategory)
                                            <li class="@if($subCategory->childes->count() > 0) dropdown @endif">
                                                <a class="dropdown-item @if($subCategory->childes->count() > 0) dropdown-toggle @endif" href="{{ route('category-products', ['slug' => $subCategory['slug']]) }}" @if($subCategory->childes->count() > 0) data-toggle="dropdown" @endif>
                                                    {{ $subCategory['name'] }}
                                                </a>
                                                @if($subCategory->childes->count() > 0)
                                                    <ul class="dropdown-menu">
                                                        @foreach($subCategory['childes'] as $subSubCategory)
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('category-products', ['slug' => $subSubCategory['slug']]) }}">
                                                                    {{ $subSubCategory['name'] }}
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            @else
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('category-products', ['slug' => $category['slug']]) }}">
                                        {{ $category['name'] }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <!--
        <div class="megamenu-wrap">
            <div class="container">
                <div class="category-menu-wrap">
                    <ul class="category-menu">
                        @foreach ($categories as $key=>$category)
                            <li>
                                <a href="{{ route('category-products', ['slug' => $category['slug']]) }}">
                                    <span class="d-flex gap-10px justify-content-start align-items-center">
                                        <img class="aspect-1 rounded-circle" width="20" src="{{ getStorageImages(path: $category?->icon_full_url, type: 'category') }}" alt="{{ $category['name'] }}">
                                        <span class="line--limit-2">{{ $category->name }}</span>
                                    </span>
                                </a>
                                @if ($category->childes->count() > 0)
                                    <div class="mega_menu z-2">
                                        @foreach ($category->childes as $sub_category)
                                            <div class="mega_menu_inner">
                                                <h6>
                                                    <a href="{{ route('category-products', ['slug' => $sub_category['slug']]) }}">
                                                        {{ $sub_category->name }}
                                                    </a>
                                                </h6>
                                                @if ($sub_category->childes->count() >0)
                                                    @foreach ($sub_category->childes as $sub_sub_category)
                                                        <div>
                                                            <a href="{{ route('category-products', ['slug' => $sub_sub_category['slug']]) }}">
                                                                {{ $sub_sub_category->name }}
                                                            </a>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </li>
                        @endforeach
                        <li class="text-center">
                            <a href="{{route('categories')}}" class="text-primary font-weight-bold justify-content-center">
                                {{ translate('View_All') }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        -->
    </div>
</header>

{{-- Branch Selection Modal --}}
<div class="modal fade" id="branchModal" tabindex="-1" role="dialog" aria-labelledby="branchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 position-relative" style="border-radius: 8px; overflow: hidden; padding: 25px 20px;">

            <button type="button" class="close-region-modal" data-dismiss="modal" aria-label="Close"
                    @if(!$currentBranchId) style="display:none;" @endif>
                <i class="fa fa-times"></i>
            </button>

            <div class="text-center mb-4">
                <h3 class="modal-title-custom">يرجى اختيار الفرع لتخصيص العرض</h3>
                <p class="modal-subtitle-custom">
                    @if($currentBranch)
                        الفرع الحالي: "<span class="text-primary-color">{{ $currentBranch->name }}</span>"، يمكنك تغييره أدناه.
                    @else
                        اختر الفرع المناسب لك لعرض المنتجات المتاحة.
                    @endif
                </p>
            </div>

            <div class="regions-grid">
                @php
                    $allBranches = \App\Models\Branch::orderBy('name')->get();
                @endphp
                @foreach($allBranches as $branch)
                    <button type="button"
                            class="region-card {{ $currentBranchId == $branch->id ? 'active' : '' }}"
                            data-branch-id="{{ $branch->id }}"
                            data-branch-name="{{ $branch->name }}">
                        {{ $branch->name }}
                    </button>
                @endforeach
            </div>

            <div class="text-center mt-4">
                <button type="button" class="btn-save-region" id="save-branch-btn">حفظ</button>
            </div>

        </div>
    </div>
</div>

@push('script')
    <script>
        "use strict";

        $(".category-menu").find(".mega_menu").parents("li")
            .addClass("has-sub-item").find("> a")
            .append("<i class='czi-arrow-{{Session::get('direction') === "rtl" ? 'left' : 'right'}}'></i>");

        $(document).ready(function() {
            let tempSelectedBranchId   = null;
            let tempSelectedBranchName = null;

            // Auto-open modal if no branch selected
            @if(!$currentBranchId)
                $('#branchModal').modal({ backdrop: 'static', keyboard: false });
                $('#branchModal').modal('show');
            @endif

            // Branch card selection
            $(document).on('click', '.region-card', function() {
                $('.region-card').removeClass('active');
                $(this).addClass('active');
                tempSelectedBranchId   = $(this).data('branch-id');
                tempSelectedBranchName = $(this).data('branch-name');
            });

            // Save branch
            $('#save-branch-btn').on('click', function() {
                if (!tempSelectedBranchId) {
                    alert('يرجى اختيار فرع أولاً');
                    return;
                }

                $.ajax({
                    url: '{{ route("set-branch") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        branch_id: tempSelectedBranchId
                    },
                    success: function(res) {
                        if (res.success) {
                            $('#current-branch-name').text(tempSelectedBranchName);
                            $('#branchModal').modal('hide');
                            location.reload();
                        }
                    },
                    error: function() {
                        alert('حدث خطأ، يرجى المحاولة مجدداً');
                    }
                });
            });
        });
    </script>

    <script>
        (function () {
            if (!document.getElementById('mobile-menu-backdrop')) {
                var backdrop = document.createElement('div');
                backdrop.id = 'mobile-menu-backdrop';
                backdrop.className = 'mobile-menu-backdrop';
                document.body.appendChild(backdrop);
            }

            var backdrop = document.getElementById('mobile-menu-backdrop');
            var drawer   = document.getElementById('navbarCollapse');

            function openDrawer() {
                if (drawer) drawer.classList.add('show');
                backdrop.classList.add('active');
                document.body.style.overflow = 'hidden';
            }

            function closeDrawer() {
                if (drawer) drawer.classList.remove('show');
                backdrop.classList.remove('active');
                document.body.style.overflow = '';
            }

            document.querySelectorAll('[data-target="#navbarCollapse"]').forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (drawer && drawer.classList.contains('show')) {
                        closeDrawer();
                    } else {
                        openDrawer();
                    }
                });
            });

            backdrop.addEventListener('click', closeDrawer);

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') closeDrawer();
            });
        })();
    </script>
@endpush
