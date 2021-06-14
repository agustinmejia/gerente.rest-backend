@extends('layouts.master')

@section('title', $type == 'create' ? 'Nuevo Usuario' : 'Editar Usuario')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h3 class="h4 mb-0 text-gray-800"> <i class="fas fa-user"></i> {{ $type == 'create' ? 'Nuevo' : 'Editar' }} Usuario</h3>
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
        <form class="user" action="{{ $type == 'create' ? route('users.store') : route('users.update', ['user' => $reg->id]) }}" method="POST" enctype="multipart/form-data" >
            @csrf
            @if ($type == 'edit')
            @method('PUT')
            @endif
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
                                        <label class="label-required" for="input-name">Nombre</label>
                                        <input type="text" name="name" id="input-name" class="form-control @error('name') is-invalid @enderror" value="{{ isset($reg) ? $reg->name : old('name') }}" placeholder="John Doe" required>
                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label class="label-required" for="input-email">Email</label>
                                        <input type="text" name="email" id="input-email" class="form-control @error('email') is-invalid @enderror" value="{{ isset($reg) ? $reg->email : old('email') }}" placeholder="example@domain.com" required>
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label {{ $type == 'create' ? 'class="label-required"' : '' }} for="input-password">Password</label>
                                        <input type="password" name="password" id="input-password" class="form-control @error('password') is-invalid @enderror" value="{{ old('password') }}" {{ $type == 'create' ? 'required' : '' }} />
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
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

                                    {{-- Datos de formulario de propietario --}}
                                    <div class="div-hide div-owner" style="display:none">
                                        <div class="form-group">
                                            <label for="input-company_name">Nombre de su Restaurante</label>
                                            <input type="text" name="company_name" id="input-company_name" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label for="select-companies_type_id">Tipo de Restaurante</label>
                                            <select name="companies_type_id" id="select-companies_type_id" class="form-control">
                                                @foreach (\App\Models\CompaniesType::where('status', 1)->where('deleted_at', NULL)->get() as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="select-city_id">Ciudad</label>
                                            <select name="city_id" id="select-city_id" class="form-control">
                                                @foreach (\App\Models\City::where('active', 1)->where('deleted_at', NULL)->get() as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }} {{ $item->state }} - {{ $item->country }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="input-phone">Telefono/celular</label>
                                        <input type="text" name="phone" id="input-phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="462 1234, 75199157" />
                                        @error('phone')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="textarea-address">Dirección</label>
                                        <textarea name="address" id="textarea-address" class="form-control @error('address') is-invalid @enderror" rows="3" placeholder="Av. 1 de mayo casi esq. La Paz">{{ old('address') }}</textarea>
                                        @error('address')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="row">
                                        <div class="form-groupm col-md-8">
                                            <label for="input-avatar">Avatar</label>
                                            <input type="file" name="avatar" id="input-avatar" class="form-control" accept="image/*">
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @php
                                                if(isset($reg)){
                                                    $image = $reg->avatar;
                                                    if($image == '../images/user.svg'){
                                                        $image = asset('storage/'.$image);
                                                    }else{
                                                        if(!str_contains($image, 'https')){
                                                            $image = asset('storage/'.str_replace('.', '-small.', $image));
                                                        }
                                                    }
                                                }else{
                                                    $image = asset('images/user.svg');
                                                }
                                            @endphp
                                            <img class="img-profile rounded-circle" src="{{ $image }}" width="100px">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-12 text-right">
                                    <div class="form-group">
                                        @if ($type == 'create')
                                        <div class="custom-control custom-checkbox small">
                                            <input type="checkbox" name="back_route" class="custom-control-input" id="customCheck" {{ old('back_route') ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="customCheck" style="padding-top: 0px">Guardar y volver a la lista</label>
                                        </div>
                                        @endif
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
                @if ($type == 'edit')
                $('#select-role_id').val('{{ $reg->roles[0]->id }}');
                @endif
                $('#select-role_id').select2()
                .change(function(){
                    let id = $('#select-role_id option:selected').val();

                    $('.div-hide').fadeOut();
                    if(id == 2){
                        $('.div-owner').fadeIn()
                    }
                });
            });
        </script>
    @endsection
@endsection
