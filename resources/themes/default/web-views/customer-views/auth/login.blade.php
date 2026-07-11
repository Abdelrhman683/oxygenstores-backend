@extends('layouts.front-end.app')

@section('title', translate('sign_in'))

@push('css_or_js')
    <link rel="stylesheet"
        href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
@endpush

@section('content')

    <?php
    $customerManualLogin = $web_config['customer_login_options']['manual_login'] ?? 0;
    $customerOTPLogin = $web_config['customer_login_options']['otp_login'] ?? 0;
    $customerSocialLogin = $web_config['customer_login_options']['social_login'] ?? 0;

    if (!$customerOTPLogin && $customerManualLogin && $customerSocialLogin) {
        $multiColumn = 1;
    } elseif ($customerOTPLogin && !$customerManualLogin && $customerSocialLogin) {
        $multiColumn = 1;
    } elseif ($customerOTPLogin && $customerManualLogin && !$customerSocialLogin) {
        $multiColumn = 1;
    } elseif ($customerOTPLogin && $customerManualLogin && $customerSocialLogin) {
        $multiColumn = 1;
    } else {
        $multiColumn = 0;
    }
    ?>


    {{-- Old Design  --}}
    {{--
    <div class="container py-4 py-lg-5 my-4 text-align-direction">
        <div class="row justify-content-center">
            <div class="{{ $multiColumn ? 'col-md-9' : 'col-md-6' }} login-card">
                <div class="d-flex justify-content-center align-items-center flex-column">
                    <img src="{{ theme_asset(path: 'public/assets/front-end/img/icons/user-vector.svg') }}" alt=""
                        class="w-70px">
                    <h2 class="text-center font-bold text-capitalize fs-20 my-4 fs-18-mobile">
                        {{ translate('Sign_In') }}
                    </h2>
                </div>
                <div class="position-relative">
                    <div
                        class="row justify-content-center align-items-center g-4 {{ $multiColumn ? 'or-sign-in-with-row' : '' }}">
                        @if ($customerOTPLogin && !$customerManualLogin && !$customerSocialLogin)
                            <div class="col-md-12">
                                <form autocomplete="off" action="{{ route('customer.auth.login') }}" method="post"
                                    id="customer-otp-login-form" class="customer-centralize-login-form"
                                    data-firebase-auth="{{ $web_config['firebase_otp_verification_status'] ? 'active' : 'deactivate' }}">
                                    @csrf
                                    <input type="hidden" name="keep_customer_login_redirect_url" value="{{ $keepCustomerLoginRedirectUrl ?? old('keep_customer_login_redirect_url', url()->previous()) }}">
                                    <input type="hidden" name="login_type" value="otp-login">
                                    @include('web-views.customer-views.auth.partials._phone')

                                    @include('web-views.customer-views.auth.partials._recaptcha')

                                    <button class="btn btn--primary btn-block btn-shadow font-semi-bold" type="submit">
                                        {{ translate('Get_OTP') }}
                                    </button>
                                </form>
                            </div>
                        @elseif(!$customerOTPLogin && $customerManualLogin && !$customerSocialLogin)
                            <div class="col-md-12">
                                <form autocomplete="off" class="customer-centralize-login-form mt-2"
                                    action="{{ route('customer.auth.login') }}" method="post" id="customer-login-form">
                                    @csrf
                                    <input type="hidden" name="keep_customer_login_redirect_url" value="{{ $keepCustomerLoginRedirectUrl ?? old('keep_customer_login_redirect_url', url()->previous()) }}">
                                    <input type="hidden" name="login_type" value="manual-login">
                                    @include('web-views.customer-views.auth.partials._email')
                                    @include('web-views.customer-views.auth.partials._password')
                                    @include('web-views.customer-views.auth.partials._remember-me', [
                                        'forgotPassword' => true,
                                    ])
                                    @include('web-views.customer-views.auth.partials._recaptcha')
                                    <button class="btn btn--primary btn-block btn-shadow font-semi-bold" type="submit">
                                        {{ translate('sign_in') }}
                                    </button>
                                    @if (!$multiColumn)
                                        @include('web-views.customer-views.auth.partials._sign-up-instruction')
                                    @endif
                                </form>
                            </div>
                        @elseif(!$customerOTPLogin && $customerManualLogin && $customerSocialLogin)
                            <div class="col-md-6">
                                <form autocomplete="off" class="customer-centralize-login-form mt-2"
                                    action="{{ route('customer.auth.login') }}" method="post" id="customer-login-form">
                                    @csrf
                                    <input type="hidden" name="keep_customer_login_redirect_url" value="{{ $keepCustomerLoginRedirectUrl ?? old('keep_customer_login_redirect_url', url()->previous()) }}">
                                    <input type="hidden" name="login_type" value="manual-login">
                                    @include('web-views.customer-views.auth.partials._email')
                                    @include('web-views.customer-views.auth.partials._password')
                                    @include('web-views.customer-views.auth.partials._remember-me', [
                                        'forgotPassword' => true,
                                    ])
                                    @include('web-views.customer-views.auth.partials._recaptcha')
                                    <button class="btn btn--primary btn-block btn-shadow font-semi-bold" type="submit">
                                        {{ translate('sign_in') }}
                                    </button>
                                    @if (!$multiColumn)
                                        @include('web-views.customer-views.auth.partials._sign-up-instruction')
                                    @endif

                                </form>
                            </div>
                        @elseif($customerOTPLogin && !$customerManualLogin && $customerSocialLogin)
                            <div class="col-md-6">
                                <form autocomplete="off" class="customer-centralize-login-form mt-2"
                                    action="{{ route('customer.auth.login') }}" method="post" id="customer-otp-login-form"
                                    data-firebase-auth="{{ $web_config['firebase_otp_verification_status'] ? 'active' : 'deactivate' }}">
                                    @csrf
                                    <input type="hidden" name="keep_customer_login_redirect_url" value="{{ $keepCustomerLoginRedirectUrl ?? old('keep_customer_login_redirect_url', url()->previous()) }}">
                                    <input type="hidden" name="login_type" value="otp-login">
                                    @include('web-views.customer-views.auth.partials._phone')
                                    @include('web-views.customer-views.auth.partials._recaptcha')

                                    <button class="btn btn--primary btn-block btn-shadow font-semi-bold" type="submit">
                                        {{ translate('Get_OTP') }}
                                    </button>
                                </form>
                            </div>
                        @elseif($customerOTPLogin && $customerManualLogin)
                            <div class="col-md-6">
                                <div class="manual-login-container">
                                    <form autocomplete="off" class="customer-centralize-login-form mt-2"
                                        action="{{ route('customer.auth.login') }}" method="post"
                                        id="customer-login-form">
                                        @csrf

                                        <input type="hidden" name="keep_customer_login_redirect_url" value="{{ $keepCustomerLoginRedirectUrl ?? old('keep_customer_login_redirect_url', url()->previous()) }}">
                                        <input type="hidden" name="login_type" class="auth-login-type-input"
                                            value="manual-login">

                                        <div class="manual-login-items">
                                            @include('web-views.customer-views.auth.partials._email')
                                            @include('web-views.customer-views.auth.partials._password')
                                            @include(
                                                'web-views.customer-views.auth.partials._remember-me',
                                                ['forgotPassword' => true]
                                            )
                                        </div>

                                        <div class="otp-login-items d-none">
                                            @include('web-views.customer-views.auth.partials._phone')
                                        </div>

                                        @include('web-views.customer-views.auth.partials._recaptcha')

                                        <div class="manual-login-items">
                                            <button class="btn btn--primary btn-block btn-shadow font-semi-bold"
                                                type="submit">
                                                {{ translate('sign_in') }}
                                            </button>
                                        </div>

                                        <div class="otp-login-items d-none">
                                            <button class="btn btn--primary btn-block btn-shadow font-semi-bold"
                                                type="submit">
                                                {{ translate('Get_OTP') }}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif

                        @if ($multiColumn)
                            <div class="or-sign-in-with"><span>{{ translate('Or Sign in with') }}</span></div>
                        @endif

                        @if ($multiColumn || $customerSocialLogin)
                            <div class="{{ $multiColumn ? 'col-md-6' : 'col-12' }}">
                                <div class="d-flex justify-content-center flex-column align-items-center my-3 gap-3">
                                    @if ($customerSocialLogin)
                                        @foreach ($web_config['customer_social_login_options'] as $socialLoginServiceKey => $socialLoginService)
                                            @if ($socialLoginService && $socialLoginServiceKey != 'apple')
                                                <a class="social-media-login-btn"
                                                    href="{{ route('customer.auth.service-login', $socialLoginServiceKey) }}">
                                                    <img alt=""
                                                        src="{{ theme_asset(path: 'public/assets/front-end/img/icons/' . $socialLoginServiceKey . '.png') }}">
                                                    <span class="text">
                                                        {{ translate($socialLoginServiceKey) }}
                                                    </span>
                                                </a>
                                            @endif
                                        @endforeach
                                    @endif
                                    @if ($customerOTPLogin && $customerManualLogin)
                                        <a class="social-media-login-btn otp-login-btn" href="javascript:">
                                            <img alt=""
                                                src="{{ theme_asset(path: 'public/assets/front-end/img/icons/otp-login-icon.svg') }}">
                                            <span class="text">{{ translate('OTP_Sign_in') }}</span>
                                        </a>

                                        <a class="social-media-login-btn manual-login-btn d-none" href="javascript:">
                                            <img alt=""
                                                src="{{ theme_asset(path: 'public/assets/front-end/img/icons/otp-login-icon.svg') }}">
                                            <span class="text">{{ translate('Manual_Login') }}</span>
                                        </a>
                                    @endif
                                </div>
                                @if ($multiColumn)
                                    @include('web-views.customer-views.auth.partials._sign-up-instruction')
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    --}}

    {{-- New Design --}}
    <div class="container rtl">
              <nav aria-label="breadcrumb">
            <style>
                .breadcrumb-item + .breadcrumb-item::before {
                    display: none;
                }
            </style>
            <ol class="breadcrumb" style="background: transparent; padding: 0; margin: 3px 0;">
                <li class="breadcrumb-item d-flex align-items-center ">
                    <a href="{{route('home')}}" class="breadcrumb-title">
                        {{translate('home')}}
                    </a>
                    <i class="fa fa-angle-{{ session('direction') === 'rtl' ? 'left' : 'right' }} mx-2" ></i>
                </li>
                <li class="breadcrumb-item active d-flex align-items-center" aria-current="page" >
                                        <a class="breadcrumb-title" href="{{ route('customer.auth.login') }}"> {{translate('حسابى')}}</a>

               
                </li>
            </ol>
        </nav>
    <div class="login-split-container container">

        <div class="row w-100 m-0">
                <div class="col-lg-6 login-form-side">
        

                <div class="login-form-container">
                    <h1 class="login-title">تسجيل الدخول</h1>
                    <p class="login-subtitle">مرحبا بعودتك! سجل الدخول إلى حسابك.</p>

                    <form autocomplete="off" class="login-form mt-4 customer-centralize-login-form" action="{{ route('customer.auth.login') }}" method="post" id="customer-login-form">
                        @csrf
                        <input type="hidden" name="keep_customer_login_redirect_url" value="{{ $keepCustomerLoginRedirectUrl ?? old('keep_customer_login_redirect_url', url()->previous()) }}">
                        <input type="hidden" name="login_type" class="auth-login-type-input" value="manual-login">

                        <div class="manual-login-items">
                             <div class="form-group mb-3">
                                 <label class="form-label">إسم المستخدم او البريد الإلكتروني او رقم الهاتف *</label>
                                 <input class="form-control auth-email-input" type="text" name="user_identity" value="{{ old('user_identity') }}" placeholder="+966*********" required>
                             </div>

                            <div class="form-group mb-3 password-toggle" style="display: block;">
                                <input class="form-control auth-password-input" name="password" type="password" id="si-password" placeholder="كلمة المرور" required>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="d-flex align-items-center" style="gap: 8px;">
                                    <input type="checkbox" name="remember" id="remember-me" {{ old('remember') ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: var(--web-primary); cursor: pointer;">
                                    <label class="m-0" for="remember-me" style="cursor: pointer; color: var(--web-primary);">تذكرني</label>
                                </div>
                                <a class="forgot-password-link" href="{{ route('customer.auth.recover-password') }}">فقدت كلمة المرور الخاصة بك؟</a>
                            </div>
                        </div>

                        <div class="otp-login-items d-none mb-4">
                            @include('web-views.customer-views.auth.partials._phone')
                        </div>

                        @include('web-views.customer-views.auth.partials._recaptcha')

                        <div class="manual-login-items">
                            <button class="btn btn-primary btn-login" type="submit">تسجيل الدخول</button>
                        </div>

                        <div class="otp-login-items d-none">
                            <button class="btn btn-primary btn-login" type="submit">{{ translate('Get_OTP') }}</button>
                        </div>

                        <a href="{{ route('customer.auth.sign-up') }}" class="signup-link">ليس لديك حساب ؟ <span>انشاء حساب</span></a>

                        @if (($customerSocialLogin && $web_config['social_login_text']) || ($customerOTPLogin && $customerManualLogin))
                            <div class="or-divider my-4">
                                <span>{{ translate('Or_Sign_in_with') }}</span>
                            </div>

                            <div class="d-flex justify-content-center gap-3">
                                @if ($customerSocialLogin)
                                    @foreach ($web_config['customer_social_login_options'] as $socialLoginServiceKey => $socialLoginService)
                                        @if ($socialLoginService && $socialLoginServiceKey != 'apple')
                                            <a class="social-login-item" href="{{ route('customer.auth.service-login', $socialLoginServiceKey) }}">
                                                <img width="24" src="{{ theme_asset(path: 'public/assets/front-end/img/icons/' . $socialLoginServiceKey . '.png') }}" alt="">
                                            </a>
                                        @endif
                                    @endforeach
                                @endif

                                @if ($customerOTPLogin && $customerManualLogin)
                                    <a class="social-login-item otp-login-btn" href="javascript:">
                                        <img width="24" src="{{ theme_asset(path: 'public/assets/front-end/img/icons/otp-login-icon.svg') }}" alt="">
                                    </a>
                                    <a class="social-login-item manual-login-btn d-none" href="javascript:">
                                        <i class="fa fa-user" style="font-size: 20px; color: var(--web-primary); display: flex; align-items: center; justify-content: center; height: 100%;"></i>
                                    </a>
                                @endif
                            </div>
                        @endif
                    </form>
                </div>
            </div>
            <div class="col-lg-6 login-illustration-side">
                <img src="{{ theme_asset(path: 'public/assets/front-end/img/login-illustration.png') }}" alt="Login Illustration">
            </div>

        
        </div>
    </div>
    </div>


@endsection

@push('script')
    @php($recaptcha = getWebConfig(name: 'recaptcha'))
    @if ($web_config['firebase_otp_verification_status'])
        <script>
            $('.or-sign-in-with').css('width', $('.or-sign-in-with-row').height())
        </script>
    @endif
@endpush
