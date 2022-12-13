<ul class="navbar-nav">
    <li class="nav-item">
        <a class="nav-link" href="{{ route('orders.index') }}">
            <i class="ni ni-basket text-orange"></i> {{ __('My Orders') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('addresses.index') }}">
            <i class="ni ni-map-big text-green"></i> {{ __('My Addresses') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('home') }}">
            <i class="fa fa-cutlery" aria-hidden="true" style="font-size:20px"></i> {{ __('Ver Menú') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('pqrs.index') }}">
            <i class="fas fa-info-circle text-info" aria-hidden="true" style="font-size:20px"></i> {{ __('Centro de Ayuda') }}
        </a>
    </li>
</ul>
