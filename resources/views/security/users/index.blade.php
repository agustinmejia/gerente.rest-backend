@extends('layouts.master')

@section('title', 'Usuarios')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h4 mb-0 text-gray-800"> <i class="fas fa-user"></i> Usuarios</h1>
        @can('browse users')
        <a href="{{ route('users.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm"><i class="fas fa-plus fa-sm text-white-50"></i> Añadir</a>
        @endcan
    </div>

    @can('browse users')
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Lista de Usuarios</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Avatar</th>
                                <th style="max-width: 250px">Opciones</th>
                            </tr>
                        </thead>
                        
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="card shadow mb-4">
            <div class="card-body text-center">
                <h3>No tienes permiso para ver este contenido <i class="fas fa-user-shield"></i></h3>
            </div>
        </div>
    @endcan

    @section('css')
        <!-- Custom styles for this page -->
        <link href="{{ url('admin/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    @endsection

    @section('script')
        <!-- Page level plugins -->
        <script src="{{ url('js/main.js') }}"></script>
        <script src="{{ url('admin/vendor/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ url('admin/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
        <script>
            $(document).ready(function() {
                let columns = [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'img', name: 'img', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
                customDataTable("{{ url('users/list/registers') }}", columns);
            });
        </script>
    @endsection
@endsection