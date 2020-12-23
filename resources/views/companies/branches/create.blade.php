@extends('layouts.master')

@section('title', 'Nueva sucursal')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h3 class="h4 mb-0 text-gray-800"> <i class="fas fa-home"></i> Nueva sucursal de <b>"{{ $company->name }}"</b></h3>
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
            <input type="hidden" name="company_id" value="{{ $company->id }}">
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
                                        <label class="label-required" for="select-city">Ciudad</label>
                                        <select name="city" id="select-city" class="form-control @error('city') is-invalid @enderror" required>
                                            <option value="">Departamento - ciudad</option>
                                            @foreach (\App\Models\Company::groupBy('city')->get() as $company)
                                            <option value="{{ $company->city }}">{{ $company->city }}</option>
                                            @endforeach
                                        </select>
                                        @error('city')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label class="label-required" for="textarea-address">Dirección</label>
                                        <textarea name="address" id="textarea-address" class="form-control @error('address') is-invalid @enderror" rows="2" placeholder="Av. 1 de mayo casi esq. La Paz">{{ old('address') }}</textarea>
                                        @error('address')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label class="label-required" for="input-phones">Telefono/celular</label>
                                        <input type="text" name="phones" id="input-phones" class="form-control @error('phones') is-invalid @enderror" value="{{ old('phones') }}" placeholder="462 1234, 75199157" required>
                                        @error('phones')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="label-required">Localización</label>
                                    <div id="map"></div>
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
                                    <a href="{{ route('companies.braches', ['company' => $company->id]) }}" class="btn btn-secondary btn-sm">
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
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin=""/>
        <style>
            #map { height: 320px; width: 100%; }
        </style>
    @endsection

    @section('script')
        <!-- Page level plugins -->
        <script src="{{ url('js/main.js') }}"></script>
        <script>
            $(document).ready(function(){
                $('#select-city').select2({tags: true});
            });
        </script>

        <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>
        <script>
            var map = L.map('map').fitWorld();

            L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
                maxZoom: 18,
                attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, ' +
                    'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
                id: 'mapbox/streets-v11',
                tileSize: 512,
                zoomOffset: -1
            }).addTo(map);

            function onLocationFound(e) {
                var radius = e.accuracy / 2;

                L.marker(e.latlng).addTo(map)
                    .bindPopup("Localización actual").openPopup();

                L.circle(e.latlng, radius).addTo(map);
            }

            function onLocationError(e) {
                alert(e.message);
            }

            map.on('locationfound', onLocationFound);
            map.on('locationerror', onLocationError);

            map.locate({setView: true, maxZoom: 16});
        </script>

    @endsection
@endsection