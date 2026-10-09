<!DOCTYPE html>
<html
    lang="es"
    class="light-style layout-menu-fixed"
    dir="ltr"
    data-theme="theme-default"
    data-assets-path="{{ asset('assets') }}/"
    data-template="vertical-menu-template-free"
>
<head>

    <meta charset="utf-8">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user" content="{{ Auth::user() }}">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0,
        user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    >

    <title>AISHA</title>

    <link
        rel="icon"
        type="image/x-icon"
        href="{{ asset('assets/img/favicon/LOGO.ico') }}"
    >

    <!-- =========================
         BOOTSTRAP 5
    ========================== -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- =========================
         GOOGLE FONT
    ========================== -->
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <!-- =========================
         BOXICONS
    ========================== -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css"
    >

    <!-- =========================
         SNEAT
    ========================== -->
    <link
        rel="stylesheet"
        href="{{ asset('assets/vendor/css/core.css') }}"
    >

    <link
        rel="stylesheet"
        href="{{ asset('assets/vendor/css/theme-default.css') }}"
    >

    <link
        rel="stylesheet"
        href="{{ asset('assets/css/demo.css') }}"
    >

    <!-- Perfect Scrollbar -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/perfect-scrollbar@1.5.6/css/perfect-scrollbar.css"
    >

    <!-- Toastr -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"
    >

    <!-- DataTables -->
    <link
        rel="stylesheet"
        href="https://cdn.datatables.net/2.0.0/css/dataTables.dataTables.css"
    >

    <link
        rel="stylesheet"
        href="https://cdn.datatables.net/buttons/3.0.0/css/buttons.dataTables.css"
    >

    <!-- CSS propios -->
    <link
        rel="stylesheet"
        href="{{ asset('css/modals.css') }}"
    >

    <link
        rel="stylesheet"
        href="{{ asset('css/buttons.css') }}"
    >

    <link
        rel="stylesheet"
        href="{{ asset('css/font-awesome.min.css') }}"
    >

    <link
        rel="stylesheet"
        href="{{ asset('css/educate-custon-icon.css') }}"
    >

</head>

<body>

<div id="app">

    @yield('content')

</div>

<input
    type="hidden"
    id="csrf_token"
    value="{{ csrf_token() }}"
>

<script src="{{ asset('js/app.js') }}"></script>
<script src="{{asset('js/main.js')}}"></script>
<script src="../assets/vendor/js/menu.js"></script>
<!-- =====================================================
     JAVASCRIPT
===================================================== -->

<!-- jQuery UNA SOLA VEZ -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>


<!-- Bootstrap 5 UNA SOLA VEZ -->
<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js">
</script>


<!-- Vue -->
<script src="https://cdn.jsdelivr.net/npm/vue@2.7.16/dist/vue.min.js"></script>


<!-- Vue Resource -->
<script
    src="https://cdn.jsdelivr.net/npm/vue-resource@1.5.3/dist/vue-resource.min.js">
</script>


<!-- Vee Validate -->
<script
    src="https://cdn.jsdelivr.net/npm/vee-validate@2.2.15/dist/vee-validate.min.js">
</script>


<!-- Toastr -->
<script
    src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js">
</script>


<!-- Moment -->
<script
    src="https://cdn.jsdelivr.net/npm/moment@2.30.1/min/moment.min.js">
</script>


<!-- Perfect Scrollbar -->
<script
    src="https://cdn.jsdelivr.net/npm/perfect-scrollbar@1.5.6/dist/perfect-scrollbar.min.js">
</script>


<!-- Sneat Helpers -->
<script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>

<script src="{{ asset('assets/js/config.js') }}"></script>


<!-- =====================================================
     TU APLICACIÓN
===================================================== -->

<script src="{{ asset('js/app.js') }}"></script>


<!-- =====================================================
     SNEAT MENU
===================================================== -->

<script src="{{ asset('assets/vendor/js/menu.js') }}"></script>


<!-- =====================================================
     SNEAT MAIN
===================================================== -->

<script src="{{ asset('assets/js/main.js') }}"></script>


<!-- DataTables -->
<script src="https://cdn.datatables.net/2.0.0/js/dataTables.min.js"></script>

<script src="https://cdn.datatables.net/buttons/3.0.0/js/dataTables.buttons.min.js"></script>

<script src="https://cdn.datatables.net/buttons/3.0.0/js/buttons.dataTables.min.js"></script>


<!-- JSZip -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>


<!-- PDFMake -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>


<!-- Exportación -->
<script src="https://cdn.datatables.net/buttons/3.0.0/js/buttons.html5.min.js"></script>

<script src="https://cdn.datatables.net/buttons/3.0.0/js/buttons.print.min.js"></script>


<!-- jQuery Mask -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

</body>
</html>