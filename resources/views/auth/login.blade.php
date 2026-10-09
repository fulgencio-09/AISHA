@extends('layouts.app')

@section('content')

<div class="container-xxl">

  <div class="authentication-wrapper authentication-basic container-p-y">

    <div class="authentication-inner">

      <div class="card">

        <div class="card-body">

          <!-- Logo -->
          <div class="container text-center">

            <img
              src="{{ asset('images/aisha.png') }}"
              alt="Logo"
              title="Logo"
              width="108">

          </div>

          <p class="text-center mb-4">
            Fundacion para el Desarrollo Educativo y Social de la Guajira
          </p>


          <!-- FORMULARIO LOGIN -->

          <form
            id="formAuthentication"
            class="mb-3"
            action="{{ route('login') }}"
            method="POST">

            @csrf


            <!-- USUARIO -->

            <div class="mb-3">

              <label
                for="username"
                class="form-label">
                Usuario
              </label>

              <input
                type="text"
                class="form-control @error('username') is-invalid @enderror"
                id="username"
                name="username"
                value="{{ old('username') }}"
                placeholder="Ingrese su usuario"
                autofocus>

              @error('username')

              <span class="invalid-feedback">
                <strong>{{ $message }}</strong>
              </span>

              @enderror

            </div>


            <!-- CONTRASEÑA -->

            <div class="mb-3 form-password-toggle">

              <div class="d-flex justify-content-between">

                <label
                  class="form-label"
                  for="password">
                  Contraseña
                </label>

              </div>


              <div class="input-group input-group-merge">

                <input
                  type="password"
                  id="password"
                  class="form-control @error('password') is-invalid @enderror"
                  name="password"
                  placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                  aria-describedby="password">

                <span
                  class="input-group-text cursor-pointer"
                  id="togglePassword">
                  <i class="bx bx-hide"></i>
                </span>

              </div>


              @error('password')

              <span class="invalid-feedback d-block">
                <strong>{{ $message }}</strong>
              </span>

              @enderror

            </div>


            <!-- CAPTCHA -->

            <div class="mb-3">

              <label class="form-label">
                Código de validación
              </label>


              <div class="d-flex align-items-center gap-2">

                <img
                  src="/captcha?v={{ time() }}"
                  id="captchaImage"
                  alt="Código de validación"
                  title="Código de validación"
                  style="
        width:220px;
        height:60px;
        border:1px solid #ddd;
        border-radius:5px;
        cursor:pointer;
        background:#f5f5f5;
    "
                  onclick="recargarCaptcha()">


                <button
                  type="button"
                  class="btn btn-outline-secondary"
                  onclick="recargarCaptcha()"
                  title="Generar nuevo código">
                  <i class="bx bx-refresh"></i>
                </button>

              </div>


              <small class="text-muted">
                Haga clic en ↻ para generar otro código.
              </small>

            </div>


            <!-- CAMPO CAPTCHA -->

            <div class="mb-3">

              <input
                type="text"
                class="form-control @error('captcha') is-invalid @enderror"
                id="captcha"
                name="captcha"
                placeholder="Ingrese el código de validación"
                maxlength="6"
                autocomplete="off"
                style="text-transform: uppercase;">


              @error('captcha')

              <span class="invalid-feedback d-block">
                <strong>{{ $message }}</strong>
              </span>

              @enderror

            </div>


            <!-- BOTÓN INGRESAR -->

            <div class="mb-3">

              <button
                class="btn btn-primary d-grid w-100"
                type="submit">
                Ingresar
              </button>

            </div>

          </form>


          <!-- MENSAJE DEL SISTEMA -->

          <p class="text-center mb-0">

            <span>
              Sistema de registros de procesos academicos
            </span>

          </p>


          @if(Session::has('message'))

          <div class="alert alert-warning alert-dismissible fade show mt-3">

            <strong>¡Cuidado!</strong>

            {{ Session::get('message') }}

            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="alert"></button>

          </div>

          @endif

        </div>

      </div>

    </div>

  </div>

</div>


<!-- RECARGAR CAPTCHA -->

<script>
  function recargarCaptcha() {

    const captcha = document.getElementById('captchaImage');

    captcha.src = "{{ route('captcha') }}?v=" + new Date().getTime();

    document.getElementById('captcha').value = '';
  }
</script>

@endsection