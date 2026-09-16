<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>تسجيل الدخول - نظام إدارة المخاطر</title>

  <link href="{{ asset('assets/img/favicon.png') }}" rel="icon">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.rtl.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">

  <style>
    /* ════════════════════════════════════════════════════════════════ */
    /* PAGE LOADER - Full screen, blocks everything on load */
    /* ════════════════════════════════════════════════════════════════ */
    #page-loader {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      width: 100%;
      height: 100%;
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(6px);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 9999999;
      opacity: 1;
      transition: opacity 0.5s ease;
    }

    #page-loader.fade-out {
      opacity: 0;
      pointer-events: none;
    }

    /* ════════════════════════════════════════════════════════════════ */
    /* SUBMIT LOADER - Shows when form is submitted */
    /* ════════════════════════════════════════════════════════════════ */
    #submit-loader {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      width: 100%;
      height: 100%;
      background: rgba(255, 255, 255, 0.92);
      backdrop-filter: blur(6px);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 999999;
    }

    #submit-loader.show {
      display: flex;
    }

    /* ════════════════════════════════════════════════════════════════ */
    /* LOADER BOX STYLING */
    /* ════════════════════════════════════════════════════════════════ */
    .loader-box {
      text-align: center;
    }

    .spinner {
      width: 55px;
      height: 55px;
      border: 5px solid #e5e7eb;
      border-top: 5px solid #003b8e;
      border-radius: 50%;
      animation: spin 0.8s linear infinite;
      margin: auto;
    }

    @keyframes spin {
      100% {
        transform: rotate(360deg);
      }
    }

    .loader-box p {
      margin-top: 12px;
      font-weight: 600;
      color: #003b8e;
      font-size: 15px;
    }

    /* ════════════════════════════════════════════════════════════════ */
    /* GENERAL STYLES */
    /* ════════════════════════════════════════════════════════════════ */
    * {
      box-sizing: border-box;
    }

    body {
      font-family: 'Cairo', sans-serif;
      background: #f0f4f9;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0;
      padding: 20px;
    }

    .login-wrapper {
      width: 100%;
      max-width: 440px;
    }

    /* ════════════════════════════════════════════════════════════════ */
    /* LOGO CARD */
    /* ════════════════════════════════════════════════════════════════ */
    .logo-card {
      background: #fff;
      border-radius: 20px 20px 0 0;
      padding: 32px 40px 24px;
      text-align: center;
      border-bottom: 2px solid #f0f4f9;
      box-shadow: 0 4px 24px rgba(0, 59, 142, 0.07);
    }

    .logo-card img {
      width: 240px;
      max-width: 100%;
      height: auto;
    }

    .system-title {
      font-size: 18px;
      font-weight: 700;
      color: #003b8e;
      margin: 14px 0 0;
      letter-spacing: 0.3px;
    }

    /* ════════════════════════════════════════════════════════════════ */
    /* FORM CARD */
    /* ════════════════════════════════════════════════════════════════ */
    .form-card {
      background: #fff;
      border-radius: 0 0 20px 20px;
      padding: 28px 40px 36px;
      box-shadow: 0 8px 32px rgba(0, 59, 142, 0.10);
    }

    .form-card h5 {
      font-size: 15px;
      font-weight: 600;
      color: #6b7280;
      text-align: center;
      margin-bottom: 24px;
    }

    /* ════════════════════════════════════════════════════════════════ */
    /* INPUT GROUPS & FIELDS */
    /* ════════════════════════════════════════════════════════════════ */
    .field-group {
      margin-bottom: 18px;
    }

    .field-group label {
      display: block;
      font-size: 13px;
      font-weight: 600;
      color: #374151;
      margin-bottom: 7px;
    }

    .input-with-icon {
      position: relative;
    }

    .input-with-icon .icon {
      position: absolute;
      right: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: #9ca3af;
      font-size: 16px;
      pointer-events: none;
    }

    .input-with-icon input {
      width: 100%;
      padding: 12px 42px 12px 16px;
      border: 1.5px solid #e5e7eb;
      border-radius: 12px;
      font-family: 'Cairo', sans-serif;
      font-size: 14px;
      color: #111827;
      background: #f9fafb;
      transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
      outline: none;
      direction: ltr;
      text-align: right;
    }

    .input-with-icon input:focus {
      border-color: #003b8e;
      background: #fff;
      box-shadow: 0 0 0 3px rgba(0, 59, 142, 0.10);
    }

    .input-with-icon input::placeholder {
      color: #d1d5db;
    }

    /* ════════════════════════════════════════════════════════════════ */
    /* REMEMBER ME */
    /* ════════════════════════════════════════════════════════════════ */
    .remember-row {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 18px;
    }

    .remember-row input {
      width: 15px;
      height: 15px;
      accent-color: #003b8e;
      cursor: pointer;
    }

    .remember-row label {
      font-size: 13px;
      color: #6b7280;
      cursor: pointer;
      margin: 0;
    }

    /* ════════════════════════════════════════════════════════════════ */
    /* ALERT MESSAGES */
    /* ════════════════════════════════════════════════════════════════ */
    .alert-danger {
      background: #fff0f0;
      border: 1px solid #fecaca;
      color: #dc2626;
      border-radius: 10px;
      padding: 10px 14px;
      font-size: 13px;
      margin-bottom: 18px;
      text-align: right;
    }

    /* ════════════════════════════════════════════════════════════════ */
    /* SUBMIT BUTTON */
    /* ════════════════════════════════════════════════════════════════ */
    .btn-login {
      width: 100%;
      padding: 13px;
      background: linear-gradient(135deg, #003b8e 0%, #0056cc 100%);
      color: #fff;
      border: none;
      border-radius: 12px;
      font-family: 'Cairo', sans-serif;
      font-size: 15px;
      font-weight: 700;
      cursor: pointer;
      margin-top: 6px;
      transition: opacity 0.2s, transform 0.1s;
      letter-spacing: 0.5px;
    }

    .btn-login:hover {
      opacity: 0.92;
    }

    .btn-login:active {
      transform: scale(0.99);
    }

    .btn-login:disabled {
      cursor: not-allowed;
    }

    /* ════════════════════════════════════════════════════════════════ */
    /* FOOTER */
    /* ════════════════════════════════════════════════════════════════ */
    .login-footer {
      text-align: center;
      margin-top: 18px;
      font-size: 12px;
      color: #9ca3af;
    }

    /* ════════════════════════════════════════════════════════════════ */
    /* RESPONSIVE DESIGN */
    /* ════════════════════════════════════════════════════════════════ */
    @media (max-width: 480px) {
      .logo-card {
        padding: 24px 24px 16px;
      }

      .form-card {
        padding: 20px 24px 28px;
      }

      .logo-card img {
        width: 200px;
      }

      .system-title {
        font-size: 16px;
      }

      .input-with-icon input {
        padding: 11px 38px 11px 14px;
        font-size: 13px;
      }

      .btn-login {
        padding: 12px;
        font-size: 14px;
      }
    }
  </style>
</head>

<body>
  <!-- ════════════════════════════════════════════════════════════════ -->
  <!-- PAGE LOADER - Shows immediately on load -->
  <!-- ════════════════════════════════════════════════════════════════ -->
  <div id="page-loader">
    <div class="loader-box">
      <div class="spinner"></div>
      <p>جاري التحميل...</p>
    </div>
  </div>

  <!-- ════════════════════════════════════════════════════════════════ -->
  <!-- FORM SUBMIT LOADER - Shows when form is submitted -->
  <!-- ════════════════════════════════════════════════════════════════ -->
  <div id="submit-loader">
    <div class="loader-box">
      <div class="spinner"></div>
      <p>جاري تسجيل الدخول...</p>
    </div>
  </div>

  <!-- ════════════════════════════════════════════════════════════════ -->
  <!-- LOGIN FORM WRAPPER -->
  <!-- ════════════════════════════════════════════════════════════════ -->
  <div class="login-wrapper">

    <!-- ── Logo Section ── -->
    <div class="logo-card">
      <img src="{{ asset('msmeda_logo_web.png') }}" alt="MSMEDA Logo">
      <p class="system-title">نظام إدارة المخاطر</p>
    </div>

    <!-- ── Form Section ── -->
    <div class="form-card">
      <h5>أدخل بيانات الدخول للمتابعة</h5>

      {{-- Display Error Messages --}}
      @if ($errors->any())
        <div class="alert-danger">{{ $errors->first() }}</div>
      @endif

      <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Username Field --}}
        <div class="field-group">
          <label for="userID">اسم المستخدم</label>
          <div class="input-with-icon">
            <i class="bi bi-person icon"></i>
            <input type="text"
                   id="userID"
                   name="userID"
                   value="{{ old('userID') }}"
                   placeholder="أدخل اسم المستخدم"
                   required
                   autofocus
                   autocomplete="username">
          </div>
        </div>

        {{-- Password Field --}}
        <div class="field-group">
          <label for="password">كلمة المرور</label>
          <div class="input-with-icon">
            <i class="bi bi-lock icon"></i>
            <input type="password"
                   id="password"
                   name="password"
                   placeholder="أدخل كلمة المرور"
                   required
                   autocomplete="current-password">
          </div>
        </div>

        {{-- Remember Me --}}
        <div class="remember-row">
          <input type="checkbox" name="remember" value="1" id="rememberMe">
          <label for="rememberMe">تذكرني</label>
        </div>

        {{-- Submit Button --}}
        <button type="submit" class="btn-login" id="loginBtn">
          <span id="btnText">تسجيل الدخول</span>
        </button>
      </form>
    </div>

    {{-- Footer --}}
    <p class="login-footer">جهاز تنمية المشروعات المتوسطة والصغيرة ومتناهية الصغر &copy; {{ date('Y') }}</p>
  </div>

  <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script>
    /**
     * Hide page loader when page is fully loaded
     */
    window.addEventListener("load", function () {
      const loader = document.getElementById("page-loader");
      if (loader) {
        loader.classList.add("fade-out");

        // Remove from DOM after fade completes
        setTimeout(() => {
          loader.style.display = "none";
        }, 500);
      }
    });

    /**
     * Safety net - hide loader after 3 seconds max
     * (in case page takes too long to load)
     */
    setTimeout(() => {
      const loader = document.getElementById("page-loader");
      if (loader) {
        loader.classList.add("fade-out");
      }
    }, 3000);

    /**
     * Form submit handler - show submit loader
     */
    const form = document.querySelector("form");
    if (form) {
      form.addEventListener("submit", function (e) {
        // Show submit loader
        const submitLoader = document.getElementById("submit-loader");
        if (submitLoader) {
          submitLoader.classList.add("show");
        }

        // Disable button and change text
        const btn = document.getElementById("loginBtn");
        const btnText = document.getElementById("btnText");

        if (btn) {
          btn.disabled = true;
          btn.style.opacity = "0.7";
        }

        if (btnText) {
          btnText.innerText = "جاري تسجيل الدخول...";
        }
      });
    }
  </script>
</body>

</html>
