@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h4 mb-0 text-gray-800"> <i class="fas fa-tachometer-alt"></i> Dashboard</h1>
    </div>

    @can('browse dashboard')
    

        @section('css')
            
        @endsection

        @section('script')
            
        @endsection
        
    @else
        <div class="card shadow mb-4">
            <div class="card-body text-center">
                <h4>No tienes permiso para ver este contenido <i class="fas fa-user-shield"></i></h4>
            </div>
        </div>
    @endcan
@endsection