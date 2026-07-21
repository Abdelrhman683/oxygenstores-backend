@php use App\Enums\DemoConstant; @endphp
@php($locale = app()->getLocale())
@php($direction = $locale == 'ar' ? 'rtl' : 'ltr')
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $direction }}">

<head>
    <meta charset="utf-8">
    <meta name="_token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="robots" content="nofollow, noindex ">
    <title>{{ translate($role) }} | {{ translate('login') }}</title>
    <link rel="shortcut icon"
        href="{{ getStorageImages(path: getWebConfig(name: 'company_fav_icon'), type: 'backend-logo') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap">

    <link rel="stylesheet" href="{{ dynamicAsset(path: 'public/assets/backend/libs/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ dynamicAsset(path: 'public/assets/backend/webfonts/uicons-regular-rounded.css') }}">
    <link rel="stylesheet" href="{{ dynamicAsset(path: 'public/assets/backend/webfonts/uicons-solid-rounded.css') }}">

    <link rel="stylesheet" href="{{ dynamicAsset(path: 'public/assets/backend/admin/css/style.css') }}">
    <link rel="stylesheet" href="{{ dynamicAsset(path: 'public/assets/backend/admin/css/style-extended.css') }}">
    <link rel="stylesheet" href="{{ dynamicAsset(path: 'public/assets/backend/admin/css/custom.css') }}">
    <link rel="stylesheet" href="{{ dynamicAsset(path: 'public/assets/backend/libs/google-recaptcha/google-recaptcha-init.css') }}">

    @if ($web_config['primary_color'])
        <style>
            :root {
                --bs-primary: {!! $web_config['primary_color'] !!};
            }
        </style>
    @endif

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&family=Tajawal:wght@300;400;500;700;800&display=swap');

        /* Dynamic Fonts override */
        body, input, button, select, textarea, label, h1, h2, h3, h4, h5, h6, span, p, a {
            font-family: 'PingARLT', 'Cairo', 'Tajawal', sans-serif !important;
        }

        body {
            background: url("{{ dynamicAsset('public/assets/back-end/img/login-bg-appliances.png') }}") no-repeat center center / cover !important;
            color: #cbd5e1 !important;
            height: 100vh !important;
            min-height: 100vh !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            overflow: hidden !important;
            position: relative !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        body::before {
            content: "" !important;
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            background: rgba(10, 10, 18, 0.75) !important;
            z-index: 1 !important;
            pointer-events: none !important;
        }

        .main {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 100% !important;
            height: 100% !important;
            min-height: 100vh !important;
            z-index: 10 !important;
            position: relative !important;
            overflow: hidden !important;
        }

        .auth-wrapper {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 100% !important;
            height: 100% !important;
            min-height: 100vh !important;
            padding: 24px !important;
            z-index: 10 !important;
            position: relative !important;
            background: transparent !important;
            overflow: hidden !important;
        }

        .auth-wrapper::before, .auth-wrapper::after {
            content: "" !important;
            position: absolute !important;
            border-radius: 50% !important;
            filter: blur(120px) !important;
            z-index: -1 !important;
            pointer-events: none !important;
            opacity: 0.15 !important;
        }
        .auth-wrapper::before {
            width: 320px !important;
            height: 320px !important;
            background: #F26444 !important;
            top: 20% !important;
            left: 15% !important;
            animation: floatGlow1 20s infinite alternate !important;
        }
        .auth-wrapper::after {
            width: 380px !important;
            height: 380px !important;
            background: #e8441e !important;
            bottom: 20% !important;
            right: 15% !important;
            animation: floatGlow2 15s infinite alternate !important;
        }

        @keyframes floatGlow1 {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(40px, 60px) scale(1.1); }
        }
        @keyframes floatGlow2 {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(-50px, -30px) scale(1.15); }
        }

        .auth-wrapper-left {
            display: none !important;
        }

        .auth-wrapper-right {
            width: 100% !important;
            max-width: 460px !important;
            padding: 0 !important;
            background: transparent !important;
            display: block !important;
            flex: none !important;
        }

        .auth-card {
            background: rgba(15, 23, 42, 0.45) !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            border-radius: 24px !important;
            padding: 40px !important;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4) !important;
            backdrop-filter: blur(20px) !important;
            -webkit-backdrop-filter: blur(20px) !important;
            position: relative !important;
            overflow: hidden !important;
            transition: transform 0.4s ease, box-shadow 0.4s ease !important;
        }

        .auth-card:hover {
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.5), 0 0 30px rgba(242, 100, 68, 0.12) !important;
        }

        .software-version-badge {
            position: absolute !important;
            top: 20px !important;
            right: 20px !important;
            background: rgba(255, 255, 255, 0.06) !important;
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
            color: rgba(248, 250, 252, 0.8) !important;
            padding: 5px 10px !important;
            border-radius: 10px !important;
            font-size: 11px !important;
            font-weight: 500 !important;
            backdrop-filter: blur(10px) !important;
            line-height: 1 !important;
        }

        .auth-header-section {
            text-align: center !important;
            margin-bottom: 30px !important;
        }

        .auth-logo {
            display: inline-block !important;
            max-width: 170px !important;
            height: auto !important;
            margin-bottom: 24px !important;
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.2)) !important;
            transition: transform 0.3s ease !important;
        }

        .auth-logo:hover {
            transform: scale(1.03) !important;
        }

        .auth-title {
            font-size: 24px !important;
            font-weight: 700 !important;
            color: #ffffff !important;
            margin-bottom: 8px !important;
            letter-spacing: -0.5px !important;
        }

        .auth-subtitle {
            font-size: 14px !important;
            color: #94a3b8 !important;
            margin-bottom: 0 !important;
        }

        .form-group {
            margin-bottom: 22px !important;
        }

        .form-label {
            color: #cbd5e1 !important;
            font-weight: 500 !important;
            font-size: 12px !important;
            margin-bottom: 8px !important;
            display: block !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
        }

        .form-control {
            background: rgba(15, 23, 42, 0.55) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: #e2e8f0 !important;

            padding: 12px 16px !important;
            font-size: 14px !important;
            height: auto !important;
            transition: all 0.3s ease !important;
        }

        .form-control::placeholder {
            color: #475569 !important;
            opacity: 1 !important;
        }

        .form-control:focus {
            background: rgba(15, 23, 42, 0.75) !important;
            border-color: #F26444 !important;
            box-shadow: 0 0 0 3px rgba(242, 100, 68, 0.22) !important;
            outline: none !important;
        }

        .input-group {
            position: relative !important;
            display: flex !important;
            align-items: stretch !important;
            width: 100% !important;
        }

        .input-group .form-control {
            width: 100% !important;
            flex: 1 1 auto !important;
        }

        .auth-card .input-group-append,
        .auth-wrapper-right .input-group-append,
        .input-group .input-group-append {
            position: absolute !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            left: 16px !important;
            right: auto !important;
            display: flex !important;
            align-items: center !important;
            border: none !important;
            background: transparent !important;
            z-index: 99 !important;
            margin: 0 !important;
            padding: 0 !important;
            height: auto !important;
        }

        [dir="rtl"] .auth-card .input-group-append,
        [dir="rtl"] .auth-wrapper-right .input-group-append,
        [dir="rtl"] .input-group .input-group-append {
            right: auto !important;
            left: 16px !important;
        }

        .input-group .js-toggle-password {
            padding-left: 48px !important;
        }
        [dir="rtl"] .input-group .js-toggle-password {
            padding-left: 48px !important;
            padding-right: 16px !important;
        }

        [dir="rtl"] .auth-card {
            text-align: right !important;
        }
        [dir="rtl"] .form-group {
            text-align: right !important;
        }
        [dir="rtl"] .form-label {
            text-align: right !important;
        }
        [dir="rtl"] .auth-header-section {
            text-align: center !important; 
        }
        [dir="rtl"] .demo-credential-item {
            text-align: right !important;
        }

        .changePassTarget a {
            color: #64748b !important;
            font-size: 16px !important;
            display: flex !important;
            align-items: center !important;
            transition: color 0.2s ease !important;
            text-decoration: none !important;
        }

        .changePassTarget a:hover {
            color: #e2e8f0 !important;
        }

        .form-check {
            display: flex !important;
            align-items: center !important;
            margin-bottom: 22px !important;
            padding-left: 0 !important; 
        }

        .checkbox--input {
            width: 16px !important;
            height: 16px !important;
            border-radius: 4px !important;
            background: rgba(15, 23, 42, 0.55) !important;
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
            margin: 0px !important;
            cursor: pointer !important;
            appearance: none !important;
            -webkit-appearance: none !important;
            position: relative !important;
        }

        [dir="rtl"] .checkbox--input {
            margin: 0px !important;
        }

        .checkbox--input:checked {
            background-color: #F26444 !important;
            border-color: #F26444 !important;
        }

        .checkbox--input:checked::before {
            content: "✓" !important;
            position: absolute !important;
            top: 50% !important;
            left: 50% !important;
            transform: translate(-50%, -50%) !important;
            color: #ffffff !important;
            font-size: 11px !important;
            font-weight: bold !important;
        }

        .form-check-input:focus {
            box-shadow: none !important;
            outline: none !important;
        }

        .custom-control-label {
            margin-bottom: 0 !important;
            margin-left: 8px !important;
            color: #94a3b8 !important;
            font-size: 13px !important;
            cursor: pointer !important;
            user-select: none !important;
        }
        [dir="rtl"] .custom-control-label {
            margin-left: 0 !important;
            margin-right: 8px !important;
        }

        .btn-primary {
            background: linear-gradient(135deg, #F26444 0%, #c4431e 100%) !important;
            border: none !important;
            border-radius: 12px !important;
            padding: 14px 20px !important;
            font-size: 15px !important;
            font-weight: 600 !important;
            color: #ffffff !important;
            width: 100% !important;
            box-shadow: 0 4px 16px rgba(242, 100, 68, 0.3) !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            cursor: pointer !important;
            display: inline-block !important;
            text-align: center !important;
        }

        .btn-primary:hover {
            box-shadow: 0 8px 24px rgba(242, 100, 68, 0.45) !important;
        }

        .btn-primary:active {
            transform: translateY(0) !important;
        }

        .demo-credentials-card {
            background: rgba(255, 255, 255, 0.02) !important;
            border: 1px solid rgba(255, 255, 255, 0.06) !important;
            border-radius: 16px !important;
            padding: 16px !important;
            margin-top: 28px !important;
            backdrop-filter: blur(10px) !important;
        }

        .demo-title {
            font-size: 11px !important;
            font-weight: 600 !important;
            color: #64748b !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            margin-bottom: 12px !important;
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
        }

        .demo-credential-item {
            font-size: 13px !important;
            color: #94a3b8 !important;
            margin-bottom: 6px !important;
            display: block !important;
        }
        .demo-credential-item strong {
            color: #cbd5e1 !important;
        }

        .demo-credential-item:last-child {
            margin-bottom: 0 !important;
        }

        .demo-copy-btn {
            background: rgba(255, 255, 255, 0.06) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: #f8fafc !important;
            border-radius: 10px !important;
            width: 36px !important;
            height: 36px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 0 !important;
            transition: all 0.2s ease !important;
            box-shadow: none !important;
        }

        .demo-copy-btn:hover {
            background: rgba(255, 255, 255, 0.12) !important;
            border-color: rgba(255, 255, 255, 0.18) !important;
            transform: scale(1.05) !important;
        }
        .demo-copy-btn i {
            font-size: 14px !important;
        }

        @media (max-width: 575px) {
            body {
                height: auto !important;
                min-height: 100vh !important;
                overflow-y: auto !important;
                overflow-x: hidden !important;
            }
            .main, .auth-wrapper {
                height: auto !important;
                min-height: 100vh !important;
                overflow: visible !important;
            }
            .auth-card {
                padding: 30px 20px !important;
                border-radius: 20px !important;
                margin-top: 20px !important;
                margin-bottom: 20px !important;
            }
        }
        .admin-login-form-form {
            direction: rtl !important;
        }
    </style>

    {!! ToastMagic::styles() !!}
</head>
<body>
    <main id="content" role="main" class="main">
        <div class="auth-wrapper">
            <!-- <div class="auth-wrapper-left"></div> -->
            <div class="auth-wrapper-right">
                <div class="auth-card">
                    <!-- @if (SOFTWARE_VERSION)
                        <label class="software-version-badge user-select-none">
                            {{ translate('software_version') }} : {{ SOFTWARE_VERSION }}
                        </label>
                    @endif -->

                    @php($eCommerceLogo = getWebConfig(name: 'company_web_logo'))
                    <div class="auth-header-section mb-4">
                        <a href="{{ route('home') }}">
                            <img class="auth-logo" src="{{ getStorageImages(path: $eCommerceLogo, type: 'backend-logo') }}" alt="Logo">
                        </a>
                        <h2 class="auth-title">{{ translate('sign_in') }}</h2>
                        <p class="auth-subtitle">
                            {{ translate('welcome_back_to') }} {{ translate($role) }} {{ translate('Login') }}
                        </p>
                    </div>

                    <form action="{{ route('login') }}" method="post" id="admin-login-form"
                        class="admin-login-form-form">
                        @csrf
                        <input type="hidden" class="form-control mb-3" name="role" id="role"
                            value="{{ $role }}">

                        <div class="js-form-message form-group">
                            <label class="form-label user-select-none" for="signingAdminEmail">
                                {{ translate('your_email') }}
                            </label>

                            <input type="email" class="form-control" name="email"
                                id="signingAdminEmail" tabindex="1" placeholder="email@address.com"
                                value="{{ old('email') }}"
                                aria-label="email@address.com" required data-msg="Please enter a valid email address.">
                        </div>
                        <div class="js-form-message form-group">
                            <label class="form-label user-select-none" for="signingAdminPassword" tabindex="0">
                                <span class="d-flex justify-content-between align-items-center">
                                    {{ translate('password') }}
                                </span>
                            </label>

                            <div class="input-group">
                                <input type="password" class="js-toggle-password form-control"
                                    name="password" id="signingAdminPassword"
                                    placeholder="{{ translate('Minimum_8_characters_required') }}"
                                    aria-label="{{ translate('Minimum_8_characters_required') }}" required
                                    data-msg="{{ translate('your_password_is_invalid.') }} {{ translate('please_try_again.') }}">
                                <div id="changePassTarget" class="input-group-append changePassTarget">
                                    <a class="text-body-light" href="javascript:">
                                        <i id="changePassIcon" class="fi fi-sr-eye-crossed"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-2">
                            <div class="form-check d-flex gap-2">
                                <input type="checkbox" class="custom-control-input form-check-input checkbox--input"
                                    id="termsCheckbox" name="remember">
                                <label class="custom-control-label text-muted user-select-none" for="termsCheckbox">
                                    {{ translate('remember_me') }}
                                </label>
                            </div>
                        </div>

                        <div class="d-none">
                            @if(isset($recaptcha) && $recaptcha['status'] == 1)
                                <div class="dynamic-default-and-recaptcha-section">
                                    <input type="hidden" name="g-recaptcha-response" class="render-grecaptcha-response" data-action="login"
                                           data-input="#login-default-captcha-section"
                                           data-default-captcha="#login-default-captcha-section"
                                    >

                                    <div class="default-captcha-container d-none" id="login-default-captcha-section"
                                         data-placeholder="{{ translate('enter_captcha_value') }}"
                                         data-base-url="{{ route('g-recaptcha-session-store') }}"
                                         data-session="{{ $role == 'admin' ? 'adminRecaptchaSessionKey' : 'employeeRecaptchaSessionKey' }}"
                                    >
                                    </div>
                                </div>
                            @else
                                <div class="default-captcha-container"
                                     data-placeholder="{{ translate('enter_captcha_value') }}"
                                     data-base-url="{{ route('g-recaptcha-session-store') }}"
                                     data-session="{{ $role == 'admin' ? 'adminRecaptchaSessionKey' : 'employeeRecaptchaSessionKey' }}"
                                 >
                                </div>
                            @endif
                        </div>

                        <button type="submit" id="admin-login-btn" class="btn btn-primary mt-2">
                            {{ translate('sign_in') }}
                        </button>
                    </form>

                    @if (env('APP_MODE') == 'demo')
                        <div class="demo-credentials-card">
                            <div class="row align-items-center">
                                <div class="col-9">
                                    <div class="align-items-baseline d-flex flex-column gap-1">
                                        <span class="demo-credential-item" id="admin-email" data-email="{{ DemoConstant::ADMIN['email'] }}">
                                            {{ translate('email') }} : <strong>{{ DemoConstant::ADMIN['email'] }}</strong>
                                        </span>
                                        <span class="demo-credential-item" id="admin-password" data-password="{{ DemoConstant::ADMIN['password'] }}">
                                            {{ translate('password') }} : <strong>{{ DemoConstant::ADMIN['password'] }}</strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-3 text-end">
                                    <button class="btn demo-copy-btn ms-auto" id="copyLoginInfo">
                                        <i class="fi fi-rr-copy"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>

    <span id="message-please-check-recaptcha" data-text="{{ translate('please_check_the_recaptcha') }}"></span>
    <span id="message-copied_success" data-text="{{ translate('copied_successfully') }}"></span>
    <span id="route-get-session-recaptcha-code" data-route="{{ route('get-session-recaptcha-code') }}"
        data-mode="{{ env('APP_MODE') }}"></span>

    <script src="{{ dynamicAsset(path: 'public/assets/new/back-end/libs/jquery/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/backend/libs/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/new/back-end/js/script.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/new/back-end/js/script-extended.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/backend/admin/js/auth.js') }}"></script>

    {!! ToastMagic::scripts() !!}

    @if ($errors->any())
        <script>
            "use strict";
            @foreach ($errors->all() as $error)
                toastMagic.error('{{ $error }}');
            @endforeach
        </script>
    @endif

    @php($recaptcha = getWebConfig(name: 'recaptcha'))
    <span id="get-google-recaptcha-key"
          data-value="{{ isset($recaptcha) && $recaptcha['status'] == 1 ? $recaptcha['site_key'] : '' }}"></span>
    @if (isset($recaptcha) && $recaptcha['status'] == 1)
        <script src="https://www.google.com/recaptcha/api.js?render={{ $recaptcha['site_key'] }}"></script>
    @endif
    <script src="{{ dynamicAsset(path: 'public/assets/backend/libs/google-recaptcha/google-recaptcha-init.js') }}"></script>

</body>

</html>
