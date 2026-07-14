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

<!-- <div class="ox_checkout_payment_method">

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
                    <div><span class="label">الإسم:</span> مؤسسة ذرة أكسجين التجارية</div>
                    <div><span class="label">العنوان:</span> address 1 SA</div>
                    <div><span class="label">المدينة:</span> الرياض</div>
                    <div><span class="label">رقم الهاتف:</span> <span class="phone" dir="ltr">+966 536652244</span></div>
                </div>

                <a href="#" class="ox-edit-link" id="ox-toggle-address-btn" onclick="oxToggleAddressForm(event)">تعديل العنوان</a>
            </div>

            <div class="ox-edit-address-form" id="ox-edit-address-form">
                <div class="ox-form-row">
                    <div class="ox-form-group">
                        <label>الاسم الأول <span class="req">*</span></label>
                        <input type="text" value="مؤسسة ذرة أكسجين" placeholder="الاسم الأول">
                    </div>
                    <div class="ox-form-group">
                        <label>الاسم الأخير <span class="req">*</span></label>
                        <input type="text" value="التجارية" placeholder="الاسم الأخير">
                    </div>
                </div>
                <div class="ox-form-row">
                    <div class="ox-form-group">
                        <label>المنطقة <span class="req">*</span></label>
                        <select>
                            <option selected>الرياض</option>
                            <option>جدة</option>
                            <option>مكة المكرمة</option>
                            <option>المدينة المنورة</option>
                            <option>الدمام</option>
                        </select>
                    </div>
                    <div class="ox-form-group">
                        <label>الشارع والحي <span class="req">*</span></label>
                        <input type="text" value="address 1" placeholder="الشارع والحي">
                    </div>
                </div>
                <div class="ox-form-row">
                    <div class="ox-form-group">
                        <label>رقم الهاتف <span class="req">*</span></label>
                        <input type="tel" value="966536652244+" placeholder="رقم الهاتف" dir="ltr">
                    </div>
                    <div class="ox-form-group">
                        <label>البريد الإلكتروني <span class="req">*</span></label>
                        <input type="email" value="admin@oxygenstores.com.sa" placeholder="البريد الإلكتروني" dir="ltr">
                    </div>
                </div>
                <div class="ox-save-btn">
                    <button type="button">تأكيد رقم الهاتف وحفظ العنوان</button>
                </div>
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
                    <span class="ox-products-count">( 1 منتج )</span>
                    <img class="ox-product-thumb"
                         src="{{ theme_asset(path: 'public/assets/front-end/img/placeholder.png') }}"
                         alt="product thumbnail">
                </div>

                <a href="#" class="ox-edit-link">مراجعة المنتجات</a>
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
                <a href="#" class="ox-edit-link">تغيير</a>
            </div>

            <div class="ox-payment-options">
                <div class="ox-payment-option">
                    <img src="{{ theme_asset(path: 'public/assets/front-end/img/card-payment.png') }}" alt="Visa">
                </div>
                <div class="ox-payment-option active">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M0 3l8-2 8 2v1H0V3zm0 2h16v1H0V5zm1 2h14v7H1V7zm2 1v5h2V8H3zm4 0v5h2V8H7zm4 0v5h2V8h-2z"/>
                    </svg>
                </div>
                <div class="ox-payment-option" style="min-width: 100%; display: block; border: none; padding: 0;">
                    <tamara-widget type="tamara-summary" amount="3099" uuid="5f3df4db-995d-4aa6-b0ac-5708d379f06e"></tamara-widget>
                </div>
            </div>

            <div class="ox-additional-info">
                <h6>معلومات إضافية</h6>
                <p class="sub">ملاحظات الطلب (اختياري)</p>
                <textarea placeholder="ملاحظات حول الطلب، مثال: ملاحظة خاصة بتسليم الطلب."></textarea>
            </div>
        </div>

    </div>

</div> -->



                                <div class="p-20">
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
                                                                           value="cash_on_delivery" checked>
                                                                    <input type="hidden" class="form-control"
                                                                           name="bring_change_amount"
                                                                           id="bring_change_amount_value">
                                                                    <span
                                                                        class="btn btn-block click-if-alone py-3 d-flex gap-2 align-items-center cursor-pointer">
                                                                        <input type="radio" id="cash_on_delivery"
                                                                               class="custom-radio" checked>
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
    <script defer src="https://cdn.tamara.co/widget-v2/tamara-widget.js"></script>

    <script src="{{ theme_asset(path: 'public/assets/front-end/js/payment.js') }}"></script>
    <script>
        function oxToggleAddressForm(e) {
            e.preventDefault();
            const formObj = document.getElementById('ox-edit-address-form');
            const addressBlock = document.getElementById('ox-address-block');
            const iconObj = document.getElementById('ox-address-icon');
            
            if (formObj) {
                formObj.classList.toggle('open');
            }
            if (iconObj) {
                iconObj.classList.toggle('editing');
            }
            if (addressBlock) {
                if (formObj && formObj.classList.contains('open')) {
                    addressBlock.style.display = 'none';
                    addressBlock.classList.remove('fade-in');
                } else {
                    addressBlock.style.display = 'block';
                    addressBlock.classList.add('fade-in');
                }
            }
        }
    </script>
@endpush
