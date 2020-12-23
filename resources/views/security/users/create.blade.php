@extends('layouts.master')

@section('title', 'Nuevo usuarios')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h3 class="h4 mb-0 text-gray-800"> <i class="fas fa-user"></i> Nuevo usuarios</h3>
    </div>

    {{-- Information --}}
    {{-- <div class="card mb-4 py-3 border-left-info">
        <div class="card-body">
            <span class="text-info">Información</span><br>
            <small>Descripción</small>
        </div>
    </div> --}}

    @can('create users')
        {{-- Form --}}
        <form class="user" action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data" >
            @csrf
            <div class="row">
                <div class="col-lg-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Formulario de registro</h6>
                        </div>
                        <div class="card-body" style="padding-bottom: 50px">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="label-required" for="input-name">Nombre</label>
                                    <input type="text" name="name" id="input-name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="John Doe" required>
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="label-required" for="input-email">Email</label>
                                    <input type="text" name="email" id="input-email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="example@domain.com" required>
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="label-required" for="input-password">Password</label>
                                    <input type="password" name="password" id="input-password" class="form-control @error('password') is-invalid @enderror" value="{{ old('password') }}" required>
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="label-required" for="select-role_id">Rol</label>
                                    <select name="role_id" id="select-role_id" class="form-control @error('role_id') is-invalid @enderror" required>
                                        <option value="">Seleccione el rol</option>
                                        @foreach (\Spatie\Permission\Models\Role::all() as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('role_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="input-avatar">Avatar</label>
                                    <input type="file" name="avatar" id="input-avatar" class="form-control" accept="image/*">
                                    @error('avatar')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-12 text-right">
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox small">
                                            <input type="checkbox" name="back_route" class="custom-control-input" id="customCheck" {{ old('back_route') ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="customCheck" style="padding-top: 0px">Guardar y volver a la lista</label>
                                        </div>
                                    </div>
                                    <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-times-circle"></i> <span class="hidden-sm">Cancelar</span>
                                    </a>
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fas fa-save"></i> <span class="hidden-sm">Guardar</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @else
        <div class="card shadow mb-4">
            <div class="card-body text-center">
                <h3>No tienes permiso para ver este contenido <i class="fas fa-user-shield"></i></h3>
            </div>
        </div>
    @endcan

    @section('css')

    @endsection

    @section('script')
        <!-- Page level plugins -->
        <script src="{{ url('js/main.js') }}"></script>
        <script>
            $(document).ready(function(){
            });
        </script>
    @endsection
@endsection