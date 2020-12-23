@extends('layouts.master')

@section('title', 'Nuevo rol')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h3 class="h4 mb-0 text-gray-800"> <i class="fas fa-user"></i> Nuevo rol</h3>
    </div>

    {{-- Information --}}
    {{-- <div class="card mb-4 py-3 border-left-info">
        <div class="card-body">
            <span class="text-info">Información</span><br>
            <small>Descripción</small>
        </div>
    </div> --}}

    {{-- Form --}}
    @can('create roles')
        <form class="user" action="{{ route('roles.store') }}" method="POST" >
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
                                    <input type="text" name="name" id="input-name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Root" required>
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="input-guard_name">Guardia</label>
                                    <input type="text" name="guard_name" id="input-guard_name" class="form-control @error('guard_name') is-invalid @enderror" value="{{ old('guard_name') }}" placeholder="Root">
                                    @error('guard_name')
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
                                    <a href="{{ route('roles.index') }}" class="btn btn-secondary btn-sm">
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
                <h4>No tienes permiso para ver este contenido <i class="fas fa-user-shield"></i></h4>
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