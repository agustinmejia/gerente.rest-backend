@extends('layouts.master')

@section('title', 'Nuevo restaurante')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h3 class="h4 mb-0 text-gray-800"> <i class="fas fa-home"></i> Nuevo restaurante</h3>
    </div>

    {{-- Information --}}
    {{-- <div class="card mb-4 py-3 border-left-info">
        <div class="card-body">
            <span class="text-info">Información</span><br>
            <small>Descripción</small>
        </div>
    </div> --}}

    @can('create companies')
        <form class="user" action="{{ route('companies.store') }}" method="POST" enctype="multipart/form-data" >
            @csrf
            <div class="row">
                <div class="col-lg-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Formulario de registro</h6>
                        </div>
                        <div class="card-body" style="padding-bottom: 50px">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="label-required" for="input-name">Nombre de su restaurante</label>
                                        <input type="text" name="name" id="input-name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Restaurante La clave" required>
                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="textarea-slogan">Slogan</label>
                                        <textarea name="slogan" id="textarea-slogan" class="form-control @error('slogan') is-invalid @enderror" rows="3" placeholder="El cliente primero">{{ old('slogan') }}</textarea>
                                        @error('slogan')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="textarea-short_description">Descripción corta</label>
                                        <textarea name="short_description" id="textarea-short_description" class="form-control @error('short_description') is-invalid @enderror" rows="3" placeholder="Servimos toda clase de delicias...">{{ old('short_description') }}</textarea>
                                        @error('short_description')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label class="label-required" for="select-city_id">Ciudad</label>
                                        <select name="city_id" id="select-city_id" class="form-control @error('city_id') is-invalid @enderror" required>
                                            <option disabled value="">Ciudad - Departamento</option>
                                            @foreach (\App\Models\City::where('deleted_at', NULL)->where('active', 1)->where('id', '>', 1)->orderBy('state', 'ASC')->orderBy('name', 'ASC')->get() as $city)
                                            <option value="{{ $city->id }}">{{ $city->name }} - {{ $city->state }}</option>
                                            @endforeach
                                            <option value="1">Otro</option>
                                        </select>
                                        @error('city_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="label-required" for="input-phones">Telefono/celular</label>
                                        <input type="text" name="phones" id="input-phones" class="form-control @error('phones') is-invalid @enderror" value="{{ old('phones') }}" placeholder="462 1234, 75199157" required>
                                        @error('phones')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label class="label-required" for="textarea-address">Dirección</label>
                                        <textarea name="address" id="textarea-address" class="form-control @error('address') is-invalid @enderror" rows="3" placeholder="Av. 1 de mayo casi esq. La Paz">{{ old('address') }}</textarea>
                                        @error('address')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label class="label-required" for="select-owner_id">Propietario</label>
                                        <select name="owner_id" id="select-owner_id" class="form-control @error('owner_id') is-invalid @enderror" required>
                                            <option value="">Seleccione al propietario</option>
                                            @foreach (\App\Models\Owner::with('person')->where('deleted_at', NULL)->get() as $owner)
                                            <option value="{{ $owner->id }}">{{ $owner->person->first_name }} {{ $owner->person->last_name }}</option>
                                            @endforeach
                                        </select>
                                        @error('owner_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label class="label-required" for="input-logo">Logo (512x512 px)</label>
                                        <input type="file" name="logo" id="input-logo" class="form-control @error('logo') is-invalid @enderror" accept="image/*" required>
                                        @error('logo')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="input-banner">Banner (1024x500 px)</label>
                                        <input type="file" name="banner" id="input-banner" class="form-control @error('banner') is-invalid @enderror" accept="image/*">
                                        @error('banner')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="row">
                                <div class="col-md-12 mt-5">
                                    <h4>Horarios de atención</h4>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Día</th>
                                                    <th>Inicio</th>
                                                    <th>Fin</th>
                                                    <th>Inicio</th>
                                                    <th>Fin</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $dias = ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo']
                                                @endphp
                                                @for ($i = 0; $i < count($dias); $i++)
                                                    <tr>
                                                        <td>
                                                            {{ $dias[$i] }}
                                                            <input type="hidden" name="day[]" value="{{ $i+1 }}">
                                                        </td>
                                                        <td>
                                                            <input type="time" name="start[]" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="time" name="end[]" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="time" name="start[]" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="time" name="end[]" class="form-control">
                                                        </td>
                                                    </tr>
                                                @endfor
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div> --}}
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-12 text-right">
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox small">
                                            <input type="checkbox" name="back_list" class="custom-control-input" id="customCheck" {{ old('back_list') ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="customCheck" style="padding-top: 0px">Guardar y volver a la lista</label>
                                        </div>
                                    </div>
                                    <a href="{{ route('companies.index') }}" class="btn btn-secondary btn-sm">
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
                $('#select-city_id').select2({tags: true});
                $('#select-owner_id').select2();
            });
        </script>
    @endsection
@endsection