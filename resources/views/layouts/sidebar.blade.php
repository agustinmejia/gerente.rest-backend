<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ url('') }}">
        {{-- <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div> --}}
        <div class="sidebar-brand-text mx-3">Gerente Admin</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item active">
        @can('browse dashboard')
            <a class="nav-link" href="{{ url('') }}"><i class="fas fa-fw fa-tachometer-alt"></i><span>Dashboard</span></a>
        @endcan
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Principal
    </div>

    <!-- Nav Item - Tables -->
    <li class="nav-item {{ stristr(URL::current(), 'companies') ? 'active' : '' }}">
        @can('browse companies')
            <a class="nav-link" href="{{ route('companies.index') }}"><i class="fas fa-fw fa-home"></i><span>Restaurantes</span></a>
        @endcan
    </li>

    <!-- Nav Item - Utilities Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link {{ stristr(URL::current(), 'users') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseUtilities"
            aria-expanded="true" aria-controls="collapseUtilities">
            <i class="fas fa-fw fa-wrench"></i>
            <span>Administración</span>
        </a>
        <div id="collapseUtilities" class="collapse {{ stristr(URL::current(), 'users') || stristr(URL::current(), 'roles') ? 'show' : '' }}" aria-labelledby="headingUtilities"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Seguridad</h6>
                @can('browse roles')
                    <a class="collapse-item {{ stristr(URL::current(), 'roles') ? 'active' : '' }}" href="{{ route('roles.index') }}">Roles</a>
                @endcan
                @can('browse users')
                    <a class="collapse-item {{ stristr(URL::current(), 'users') ? 'active' : '' }}" href="{{ route('users.index') }}">Usuarios</a>
                @endcan
                <h6 class="collapse-header">Seguridad</h6>
                <a class="collapse-item" href="#">Animations</a>
                <a class="collapse-item" href="#">Other</a>
            </div>
        </div>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->