@extends('auth.layouts.master')

@section('title', 'Recuperar contraseña')

@section('content')
<div class="container">

    <!-- Outer Row -->
    <div class="row justify-content-center pt-5">

        <div class="col-md-6 pt-5">

            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <!-- Nested Row within Card Body -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="h4 text-gray-900 mb-4">Olvidaste tu contraseña</h1>
                                </div>
                                @if (session('status'))
                                    <div class="alert alert-success" role="alert">
                                        {{ session('status') }}
                                    </div>
                                @endif
            
                                <form method="POST" action="{{ route('password.email') }}">
                                    @csrf
            
                                    <div class="form-group row">
                                        <label>Ingresa tu Email</label>
                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="alert alert-info" role="alert">
                                            <h5 class="alert-heading">Importante!</h5>
                                            <p>En caso de no encontrar el correo electrónico en tu bandeja de entrada verifica en tu lista de <b>Span</b>.</p>
                                        </div>
                                    </div>
            
                                    <div class="form-group row mb-0">
                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            Enviar mensaje <i class="fas fa-paper-plane"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
