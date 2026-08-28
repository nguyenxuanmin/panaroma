<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <div class="sidebar-brand">
        <a href="{{route('admin')}}" class="brand-link">
            @if (!empty($company->logo))
                <img src="{{asset('storage/company/logo/'.$company->logo)}}" alt="{{$company->name}}" class="brand-image opacity-75 shadow" />
            @else
                <img src="{{asset('library/admin/AdminLTEFullLogo.png')}}" alt="AdminLTE Logo" class="brand-image opacity-75 shadow" />
            @endif
        </a>
    </div>
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{route('list_floor')}}" class="nav-link @if (request()->is('floor*')) active @endif">
                        <p>Panorama Category</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('list_panorama')}}" class="nav-link @if (request()->is('panorama*')) active @endif">
                        <p>Panorama</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('list_hotspot')}}" class="nav-link @if (request()->is('hotspot*')) active @endif">
                        <p>Hotspot</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>