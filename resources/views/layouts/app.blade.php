<!DOCTYPE html>
<html
  lang="en"
  class="light-style customizer-hide"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="../assets/"
  data-template="vertical-menu-template-free">

<head>

  <meta charset="utf-8">

  <meta
    name="csrf-token"
    content="{{ csrf_token() }}">

  <meta
    name="viewport"
    content="width=device-width, initial-scale=1.0,
        user-scalable=no, minimum-scale=1.0,
        maximum-scale=1.0">

  <title>AISHA</title>

  <meta name="description" content="">

  <!-- Favicon -->
  <link
    rel="icon"
    type="image/x-icon"
    href="{{ asset('assets/img/favicon/LOGO.ico') }}">

  <!-- Bootstrap -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
    rel="stylesheet">

  <!-- Sneat -->
  <link
    rel="stylesheet"
    href="{{ asset('assets/vendor/css/core.css') }}">

  <link
    rel="stylesheet"
    href="{{ asset('assets/vendor/css/theme-default.css') }}">

  <link
    rel="stylesheet"
    href="{{ asset('assets/css/demo.css') }}">

  <link
    rel="stylesheet"
    href="{{ asset('assets/vendor/fonts/boxicons.css') }}">
    <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/perfect-scrollbar@1.5.6/css/perfect-scrollbar.css">

  <link
    rel="stylesheet"
    href="{{ asset('assets/vendor/css/pages/page-auth.css') }}">

 

  <!-- Config -->
  <script src="{{ asset('assets/js/config.js') }}"></script>

</head>

<body>

  <main class="py-0">

    @yield('content')

  </main>


  <!-- Bootstrap -->
  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js">
  </script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {

      const password = document.getElementById('password');
      const togglePassword = document.getElementById('togglePassword');

      if (password && togglePassword) {

        togglePassword.addEventListener('click', function() {

          const icon = this.querySelector('i');

          if (password.type === 'password') {

            password.type = 'text';

            icon.classList.remove('bx-hide');
            icon.classList.add('bx-show');

          } else {

            password.type = 'password';

            icon.classList.remove('bx-show');
            icon.classList.add('bx-hide');

          }

        });

      }

    });
  </script>

</body>

</html>