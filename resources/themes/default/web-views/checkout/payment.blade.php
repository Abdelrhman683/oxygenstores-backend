@extends('layouts.front-end.app')

@section('title', translate('choose_Payment_Method'))

@push('css_or_js')
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/payment.css') }}">
    <script src="https://js.stripe.com/v3/"></script>
@endpush

@section('content')
    <div class="container pb-5 mb-2 mb-md-4 rtl px-0 px-md-3 text-align-direction">
        <div class="row mx-max-md-0">
            <section class="col-lg-8 px-max-md-0 mt-4">
                <div class="checkout_details">
                    <div class="px-3 px-md-0">
                        @include('web-views.partials._checkout-steps', ['step' => 3])
                    </div>
                    <div class=" mt-3">
                        <div class="card-body p-0">

                            @if (!$activeMinimumMethods)
                                <div class="d-flex justify-content-center py-3">
                                    <div class="text-center">
                                        <img src="{{ theme_asset(path: 'public/assets/front-end/img/icons/nodata.svg') }}"
                                             alt="" class="mb-4" width="70">
                                        <h5 class="fs-14 text-muted">
                                            {{ translate('payment_methods_are_not_available_at_this_time.') }}</h5>
                                    </div>
                                </div>
                            @else
                                @if (($cashOnDeliveryBtnShow && $cash_on_delivery['status']) || (auth('customer')->check() && $wallet_status == 1))
                                    <div class="gap-2 py-3 px-3">
                                        <div class="d-flex justify-content-between">
                                            <h5 class="mb-0 text-nowrap">{{ translate('payment_method') }}</h5>
                                            <a href="{{ route('checkout-details') }}"
                                               class="d-flex align-items-center gap-2 text-primary font-weight-bold text-nowrap">
                                                <i class="tio-back-ui fs-12 text-capitalize"></i>
                                                {{ translate('go_back') }}
                                            </a>
                                        </div>
                                    </div>
                                @endif

@php($address = $shippingAddress ?? $billingAddress)
@php($cart = \App\Utils\CartManager::getCartListQuery(type: 'checked'))
@php($tamaraConfigSetting = DB::table('addon_settings')->where('key_name', 'tamara')->where('settings_type', 'payment_config')->first())
@php($tamaraPublicKey = '')
@if($tamaraConfigSetting)
    @php($mode = $tamaraConfigSetting->mode)
    @php($values = json_decode($mode == 'live' ? $tamaraConfigSetting->live_values : $tamaraConfigSetting->test_values))
    @php($tamaraPublicKey = $values?->public_key ?? '')
@endif

<div class="ox_checkout_payment_method">

    <div class="ox-checkout-summary px-3 px-md-0 mb-3">

        <div class="ox-summary-card">
            <div class="ox-summary-card-header">
                <div class="ox-summary-card-title">
                    <div class="ox-check-icon" id="ox-address-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <span class="icon-num">1</span>
                    </div>
                    الفاتورة &amp; الشحن
                </div>
                
                <div class="ox-address-block" id="ox-address-block">
                    @if($address)
                        <div><span class="label">الإسم:</span> {{ $address->contact_person_name }}</div>
                        <div><span class="label">العنوان:</span> {{ $address->address }}</div>
                        <div><span class="label">المدينة:</span> {{ $address->city }}</div>
                        <div><span class="label">رقم الهاتف:</span> <span class="phone" dir="ltr">{{ $address->phone }}</span></div>
                    @else
                        <div class="text-danger">يرجى تحديث معلومات العنوان</div>
                    @endif
                </div>

                <a href="{{ route('checkout-details') }}" class="ox-edit-link" id="ox-toggle-address-btn">تعديل العنوان</a>
            </div>
        </div>

        <div class="ox-summary-card">
            <div class="ox-summary-card-header">
                <div class="ox-summary-card-title">
                    <div class="ox-check-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                    </div>
                    مراجعة المنتجات
                </div>
                
                <div class="d-flex align-items-center flex-column gap-3">
                    @php($cartCount = $cart->count())
                    @if($cartCount == 1)
                        <span class="ox-products-count">( منتج واحد )</span>
                    @elseif($cartCount == 2)
                        <span class="ox-products-count">( منتجان )</span>
                    @elseif($cartCount >= 3 && $cartCount <= 10)
                        <span class="ox-products-count">( {{ $cartCount }} منتجات )</span>
                    @else
                        <span class="ox-products-count">( {{ $cartCount }} منتج )</span>
                    @endif
                    
                    <div class="d-flex gap-2 align-items-center flex-wrap justify-content-center mt-2">
                        @foreach($cart as $cartItem)
                            <img class="ox-product-thumb"
                                 src="{{ getStorageImages(path: $cartItem?->product?->thumbnail_full_url, type: 'product') }}"
                                 alt="{{ $cartItem?->product?->name }}"
                                 title="{{ $cartItem?->product?->name }}">
                        @endforeach
                    </div>
                </div>

                <a href="{{ route('shop-cart') }}" class="ox-edit-link">مراجعة المنتجات</a>
            </div>
        </div>

        <div class="ox-summary-card">
            <div class="ox-summary-card-header">
                <div class="ox-summary-card-title">
                    <div class="ox-check-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                    </div>
                    طريقة الدفع
                </div>
                <a href="{{ route('checkout-details') }}" class="ox-edit-link">تغيير</a>
            </div>

            <div class="ox-payment-options">
                @if ($digital_payment['status'] == 1)
                    @foreach ($payment_gateways_list as $payment_gateway)
                        @if ($payment_gateway->key_name == 'paymob_accept')
                            <div class="ox-payment-option {{ $loop->first ? 'active' : '' }}" data-gateway="paymob_accept" title="Paymob accept">
                                <img src="{{ theme_asset(path: 'public/assets/front-end/img/card-payment.png') }}" alt="Paymob accept">
                            </div>
                        @elseif ($payment_gateway->key_name == 'tamara')
                            <div class="ox-payment-option {{ $loop->first ? 'active' : '' }}" data-gateway="tamara" title="Tamara">
                                <img src="https://cdn.tamara.co/widget-v2/assets/lavendar-logo.703d190a.svg" alt="Tamara">
                            </div>
                        @elseif ($payment_gateway->key_name == 'tabby')
                            <div class="ox-payment-option {{ $loop->first ? 'active' : '' }}" data-gateway="tabby" title="Tabby">
                                <img src="{{ theme_asset(path: 'public/assets/front-end/img/tabby-icon.webp') }}" alt="Tabby" onerror="this.src='https://checkout.tabby.ai/assets/logo-green.svg'">
                            </div>
                        @else
                            @php($additionalData = $payment_gateway['additional_data'] != null ? json_decode($payment_gateway['additional_data']) : [])
                            <?php
                            $gatewayImgPath = dynamicAsset(path: 'public/assets/back-end/img/modal/payment-methods/' . $payment_gateway->key_name . '.png');
                            if ($additionalData != null && $additionalData?->gateway_image && file_exists(base_path('storage/app/public/payment_modules/gateway_image/' . $additionalData->gateway_image))) {
                                $gatewayImgPath = $additionalData->gateway_image ? dynamicStorage(path: 'storage/app/public/payment_modules/gateway_image/' . $additionalData->gateway_image) : $gatewayImgPath;
                            }
                            ?>
                            <div class="ox-payment-option {{ $loop->first ? 'active' : '' }}" data-gateway="{{ $payment_gateway->key_name }}" title="{{ str_replace('_', ' ', $payment_gateway->key_name) }}">
                                @if (!empty($gatewayImgPath))
                                    <img src="{{ $gatewayImgPath }}" alt="{{ $payment_gateway->key_name }}">
                                @else
                                    <span class="fs-12 fw-bold">{{ ucwords(str_replace('_', ' ', $payment_gateway->key_name)) }}</span>
                                @endif
                            </div>
                        @endif
                    @endforeach
                @endif

                @if (isset($offline_payment) && $offline_payment['status'] && count($offline_payment_methods) > 0)
                    <div class="ox-payment-option {{ ($digital_payment['status'] == 0 || count($payment_gateways_list) == 0) ? 'active' : '' }}" data-gateway="pay_offline" title="تحويل مصرفي">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M0 3l8-2 8 2v1H0V3zm0 2h16v1H0V5zm1 2h14v7H1V7zm2 1v5h2V8H3zm4 0v5h2V8H7zm4 0v5h2V8h-2z"/>
                        </svg>
                    </div>
                @endif
            </div>

            @if (isset($offline_payment) && $offline_payment['status'] && count($offline_payment_methods) > 0)
                <div class="mt-3 pay_offline_card d-none">
                    <div class="p-3 bg-light rounded border">
                        <h6 class="fs-13 fw-bold mb-2">{{ translate('select_offline_payment_method') }}</h6>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($offline_payment_methods as $method)
                                <button type="button"
                                        class="btn btn-sm btn-outline-primary offline_payment_button text-capitalize"
                                        id="{{ $method->id }}">{{ $method->method_name }}</button>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <div class="ox-additional-info">
                <h6>معلومات إضافية</h6>
                <p class="sub">ملاحظات الطلب (اختياري)</p>
                <textarea id="order_note" placeholder="ملاحظات حول الطلب، مثال: ملاحظة خاصة بتسليم الطلب.">{{ session('order_note') }}</textarea>
            </div>
        </div>

    </div>

</div>



                                <div class="p-20 d-none" style="display: none !important;">
                                    @if (
                                        ($cashOnDeliveryBtnShow && $cash_on_delivery['status']) ||
                                            $digital_payment['status'] == 1 ||
                                            (auth('customer')->check() && $wallet_status == 1))
                                        @if (($cashOnDeliveryBtnShow && $cash_on_delivery['status']) || (auth('customer')->check() && $wallet_status == 1))
                                            <p class="text-capitalize mt-0">
                                                {{ translate('select_a_payment_method_to_proceed') }}</p>

                                            <div class="d-flex flex-sm-nowrap flex-wrap w-100 gap-3 mb-3">
                                                @if ($cashOnDeliveryBtnShow && $cash_on_delivery['status'])
                                                    <div id="cod-for-cart" class="w-100 h-100 cod-for-cart">
                                                        <div class="card cursor-pointer">
                                                            <form action="{{ route('checkout-complete') }}" method="get"
                                                                  class="needs-validation" id="cash_on_delivery_form">
                                                                <label class="m-0 pt-2 pb-1">
                                                                    <input type="hidden" name="payment_method"
                                                                           value="cash_on_delivery">
                                                                    <input type="hidden" class="form-control"
                                                                           name="bring_change_amount"
                                                                           id="bring_change_amount_value">
                                                                    <span
                                                                        class="btn btn-block click-if-alone py-3 d-flex gap-2 align-items-center cursor-pointer">
                                                                        <input type="radio" id="cash_on_delivery"
                                                                               class="custom-radio">
                                                                        <img width="20"
                                                                             src="{{ theme_asset(path: 'public/assets/front-end/img/icons/money.png') }}"
                                                                             alt="">
                                                                        <span class="fs-12">
                                                                            {{ translate('cash_on_Delivery') }}
                                                                        </span>
                                                                    </span>
                                                                </label>
                                                            </form>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if (auth('customer')->check() && $wallet_status == 1)
                                                    <div class="w-100 h-100">
                                                        <div class="card cursor-pointer">
                                                            <div class="btn btn-block click-if-alone d-flex justify-content-between gap-2 align-items-center">
                                                                <div class="d-flex gap-2 align-items-start">
                                                                    <img width="20"
                                                                         src="{{ theme_asset(path: 'public/assets/front-end/img/icons/wallet-sm.png') }}"
                                                                         alt="" />
                                                                    <span class="fs-12 text-start">
                                                                        {{ translate('pay_via_Wallet') }} <br>
                                                                        <span class="fs-18 fw-semibold text-dark">{{ webCurrencyConverter(amount:auth('customer')->user()?->wallet_balance ?? 0 ) }}</span>
                                                                    </span>
                                                                </div>
                                                                <button type="button"  data-toggle="modal"
                                                                data-target="#wallet_submit_button" class="btn btn-outline-primary">
                                                                    {{ translate('APPLY') }}
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>

                                            @if ($cashOnDeliveryBtnShow && $cash_on_delivery['status'])
                                                <div class="bring_change_amount_section">
                                                    <div class="collapse show mb-10px" id="bring_change_amount"
                                                         data-more="{{ translate('See_More') }}"
                                                         data-less="{{ translate('See_Less') }}">
                                                        <div
                                                            class="bring_change_amount_details row justify-content-start align-items-center rounded-10 g-2 px-3 py-12">
                                                            <div class="col-sm-6">
                                                                <h6 class="fs-12 mb-1 fw-bold">
                                                                    {{ translate('Bring_Change_Instruction') }}
                                                                </h6>
                                                                <p class="mb-0 fs-12 opacity-50">
                                                                    {{ translate('Insert_amount_if_you_need_deliveryman_to_bring') }}
                                                                </p>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <label class="fs-12 fw-bold" for="">
                                                                    {{ translate('Change_Amount') }}
                                                                    ({{ getCurrencySymbol(type: 'web') }})
                                                                </label>
                                                                <input type="text"
                                                                       class="form-control max-w-210px only-integer-input-field"
                                                                       id="bring_change_amount_input"
                                                                       placeholder="{{ translate('Amount') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="text-center mb-10px">
                                                        <a id="bring_change_amount_btn"
                                                           class="btn text-center text-capitalize text--primary fs-12 p-0"
                                                           data-toggle="collapse" href="#bring_change_amount" role="button"
                                                           aria-expanded="false" aria-controls="change_amount">
                                                            {{ translate('See_Less') }}
                                                        </a>
                                                    </div>
                                                </div>
                                            @endif

                                        @endif

                                    @endif

                                    <div class="bg-primary-light rounded p-4 mb-20">
                                        @if (($digital_payment['status'] == 1 && count($payment_gateways_list) > 0) ||
                                                    (isset($offline_payment) && $offline_payment['status'] && count($offline_payment_methods) > 0))
                                            <div class="gap-2 mb-4">
                                                <div class="d-flex justify-content-between">
                                                    <div class="d-flex align-items-end gap-2">
                                                        <h5 class="mb-0 text-nowrap">
                                                            {{ translate('pay_via_online') }}
                                                        </h5>
                                                        <span class="fs-10 text-capitalize mt-1">
                                                            ({{ translate('faster_&_secure_way_to_pay') }})
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if ($digital_payment['status'] == 1)
                                            <div class="row gx-4">
                                                @foreach ($payment_gateways_list as $payment_gateway)
                                                    @php($additionalData = $payment_gateway['additional_data'] != null ? json_decode($payment_gateway['additional_data']) : [])
                                                        <?php
                                                        $gatewayImgPath = dynamicAsset(path: 'public/assets/back-end/img/modal/payment-methods/' . $payment_gateway->key_name . '.png');
                                                        if ($additionalData != null && $additionalData?->gateway_image && file_exists(base_path('storage/app/public/payment_modules/gateway_image/' . $additionalData->gateway_image))) {
                                                            $gatewayImgPath = $additionalData->gateway_image ? dynamicStorage(path: 'storage/app/public/payment_modules/gateway_image/' . $additionalData->gateway_image) : $gatewayImgPath;
                                                        }
                                                        ?>


                                                    <div class="col-sm-6">
                                                        <form method="post" class="digital_payment"
                                                              id="{{ $payment_gateway->key_name }}_form"
                                                              action="{{ route('customer.web-payment-request') }}">
                                                            @csrf
                                                            <input type="hidden" name="user_id"
                                                                   value="{{ auth('customer')->check() ? auth('customer')->user()->id : session('guest_id') }}">
                                                            <input type="hidden" name="customer_id"
                                                                   value="{{ auth('customer')->check() ? auth('customer')->user()->id : session('guest_id') }}">
                                                            <input type="hidden" name="payment_method"
                                                                   value="{{ $payment_gateway->key_name }}">
                                                            <input type="hidden" name="payment_platform" value="web">

                                                            @if ($payment_gateway->mode == 'live' && isset($payment_gateway->live_values['callback_url']))
                                                                <input type="hidden" name="callback"
                                                                       value="{{ $payment_gateway->live_values['callback_url'] }}">
                                                            @elseif ($payment_gateway->mode == 'test' && isset($payment_gateway->test_values['callback_url']))
                                                                <input type="hidden" name="callback"
                                                                       value="{{ $payment_gateway->test_values['callback_url'] }}">
                                                            @else
                                                                <input type="hidden" name="callback" value="">
                                                            @endif

                                                            <input type="hidden" name="external_redirect_link"
                                                                   value="{{ route('web-payment-success') }}">
                                                            <label
                                                                class="d-flex align-items-center px-0 gap-2 mb-0 form-check py-2 cursor-pointer">
                                                                <input type="radio" id="{{ $payment_gateway->key_name }}"
                                                                       name="online_payment" class="form-check-input custom-radio"
                                                                       value="{{ $payment_gateway->key_name }}">
                                                                <img width="30"
                                                                     src="{{ $gatewayImgPath}}"
                                                                     alt="">
                                                                <span class="text-capitalize form-check-label">
                                                                    @if ($payment_gateway->additional_data && json_decode($payment_gateway->additional_data)->gateway_title != null)
                                                                        {{ json_decode($payment_gateway->additional_data)->gateway_title }}
                                                                    @else
                                                                        {{ str_replace('_', ' ', $payment_gateway->key_name) }}
                                                                    @endif
                                                                </span>
                                                            </label>
                                                        </form>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    @if (isset($offline_payment) && $offline_payment['status'] && count($offline_payment_methods) > 0)
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <div class="bg-primary-light rounded p-4">
                                                    <div
                                                        class="d-flex justify-content-between align-items-center gap-2 position-relative">
                                                        <span class="d-flex align-items-center gap-3">
                                                            <input type="radio" id="pay_offline" name="online_payment"
                                                                   class="custom-radio" value="pay_offline">
                                                            <label for="pay_offline"
                                                                   class="cursor-pointer d-flex align-items-center gap-2 mb-0 text-capitalize">{{ translate('pay_offline') }}</label>
                                                        </span>

                                                        <div data-toggle="tooltip"
                                                             title="{{ translate('for_offline_payment_options,_please_follow_the_steps_below') }}">
                                                            <i class="tio-info text-primary"></i>
                                                        </div>
                                                    </div>

                                                    <div class="mt-4 pay_offline_card d-none">
                                                        <div class="d-flex flex-wrap gap-3">
                                                            @foreach ($offline_payment_methods as $method)
                                                                <button type="button"
                                                                        class="btn btn-light offline_payment_button text-capitalize"
                                                                        id="{{ $method->id }}">{{ $method->method_name }}</button>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                            @endif

                        </div>
                    </div>
                </div>
            </section>
            @include('web-views.partials._order-summary')
        </div>
    </div>

    @if (isset($offline_payment) && $offline_payment['status'])
        <div class="modal fade" id="selectPaymentMethod" tabindex="-1" aria-labelledby="selectPaymentMethodLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered  modal-dialog-scrollable modal-lg">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('offline-payment-checkout-complete') }}" method="post"
                              class="needs-validation form-loading-button-form">
                            @csrf
                            <div class="d-flex justify-content-center mb-4">
                                <img width="52"
                                     src="{{ theme_asset(path: 'public/assets/front-end/img/select-payment-method.png') }}"
                                     alt="">
                            </div>
                            <p class="fs-14 text-center">
                                {{ translate('pay_your_bill_using_any_of_the_payment_method_below_and_input_the_required_information_in_the_form') }}
                            </p>

                            <select class="form-control mx-xl-5 max-width-661" id="pay_offline_method" name="payment_by"
                                    required>
                                <option value="" disabled>{{ translate('select_Payment_Method') }}</option>
                                @foreach ($offline_payment_methods as $method)
                                    <option value="{{ $method->id }}">{{ translate('payment_Method') }} :
                                        {{ $method->method_name }}</option>
                                @endforeach
                            </select>
                            <div class="" id="payment_method_field">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if (auth('customer')->check() && $wallet_status == 1)
        <div class="modal fade" id="wallet_submit_button" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">{{ translate('wallet_payment') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @php($customer_balance = auth('customer')->user()->wallet_balance)
                    @php($couponAmount = session()->has('coupon_discount') ? session('coupon_discount') : 0)
                    @php($totalAmount = $amount)
                    @php($remain_balance = $customer_balance - $totalAmount)
                    <form action="{{ route('checkout-complete-wallet') }}" method="get" class="needs-validation">
                        @csrf
                        <div class="modal-body">
                            <div class="form-row">
                                <div class="form-group col-12">
                                    <label for="">{{ translate('your_current_balance') }}</label>
                                    <input class="form-control" type="text"
                                           value="{{ webCurrencyConverter(amount: $customer_balance ?? 0) }}" readonly>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-12">
                                    <label for="">{{ translate('order_amount') }}</label>
                                    <input class="form-control" type="text"
                                           value="{{ webCurrencyConverter(amount: $totalAmount ?? 0) }}" readonly>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-12">
                                    <label for="">{{ translate('remaining_balance') }}</label>
                                    <input class="form-control" type="text"
                                           value="{{ webCurrencyConverter(amount: $remain_balance ?? 0) }}" readonly>
                                    @if ($remain_balance < 0)
                                        <label
                                            class="__color-crimson mt-1">{{ translate('you_do_not_have_sufficient_balance_for_pay_this_order!!') }}</label>
                                    @endif
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                    data-dismiss="modal">{{ translate('close') }}</button>
                            <button type="submit" class="btn btn--primary"
                                {{ $remain_balance > 0 ? '' : 'disabled' }}>{{ translate('submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <span id="route-action-checkout-function" data-route="checkout-payment"></span>
@endsection

@push('script')
    <script>
        window.tamaraWidgetConfig = {
            lang: '{{ session("direction") == "rtl" ? "ar" : "en" }}',
            country: 'SA',
            publicKey: '{{ $tamaraPublicKey ?? "d5eee77d-f5d5-4177-b6f6-8a0f913e61c0" }}'
        };
    </script>
    <script defer src="https://cdn.tamara.co/widget-v2/tamara-widget.js"></script>

    <script src="{{ theme_asset(path: 'public/assets/front-end/js/payment.js') }}"></script>
    <script>
        $(document).ready(function() {
            function updateProceedButtonStatus() {
                let termsChecked = $('.payment-input-checkbox').length === 0 || $('.payment-input-checkbox:checked').length > 0;
                let checkedRadio = $('input[type="radio"]:checked');
                let isOffline = checkedRadio.attr('id') === 'pay_offline';
                let hasSelectedGateway = checkedRadio.length > 0 && !isOffline;

                if (termsChecked && hasSelectedGateway) {
                    $(".proceed_to_next_button").removeClass("disabled").removeAttr("disabled");
                } else {
                    $(".proceed_to_next_button").addClass("disabled");
                }

                if (isOffline) {
                    $(".pay_offline_card").removeClass("d-none");
                    $(".proceed_to_next_button").addClass("disabled");
                } else {
                    $(".pay_offline_card").addClass("d-none");
                }
            }

            function selectPaymentGateway(gateway) {
                if (!gateway) return;

                // Uncheck all radios across all forms first
                $('input[type="radio"]').prop('checked', false).removeAttr('checked');

                // Check the target gateway radio
                let radioInput = $('#' + gateway);
                if (radioInput.length) {
                    radioInput.prop('checked', true).attr('checked', 'checked').trigger('change');
                }

                updateProceedButtonStatus();
            }

            $(document).on('click', '.ox-payment-option', function() {
                let gateway = $(this).data('gateway');
                $('.ox-payment-option').removeClass('active');
                $(this).addClass('active');

                selectPaymentGateway(gateway);
            });

            $(document).on('change click', '.payment-input-checkbox', function() {
                updateProceedButtonStatus();
            });

            // Initialize default active selection on page load
            setTimeout(function() {
                let activeCard = $('.ox-payment-option.active');
                if (!activeCard.length) {
                    activeCard = $('.ox-payment-option').first();
                    activeCard.addClass('active');
                }
                if (activeCard.length) {
                    let gateway = activeCard.data('gateway');
                    selectPaymentGateway(gateway);
                } else {
                    updateProceedButtonStatus();
                }
            }, 100);

            $('#order_note').on('change', function() {
                let orderNote = $(this).val();
                $.post({
                    url: $("#route-order-note").data("url"),
                    data: {
                        _token: $('meta[name="_token"]').attr("content"),
                        order_note: orderNote,
                    },
                    success: function (response) {
                        console.log('Order note updated in session');
                    }
                });
            });
        });
    </script>
@endpush
