<!-- Top navbar -->
<nav class="navbar navbar-top navbar-expand-md  navbar-dark" id="navbar-main">
    <div class="container-fluid">

        <!-- Brand -->
        <a class="h4 mb-0 text-white text-uppercase d-none d-lg-inline-block">@yield('admin_title')</a>
        
        <!-- Form -->
        <form method="GET" class="navbar-search navbar-search-dark form-inline mr-3 d-none d-md-flex ml-lg-auto">
           
        </form>
       
        <!-- User -->
        <ul class="navbar-nav align-items-center d-none d-md-flex">
        <li class="nav-item dropdown">
                <a class="nav-link pr-0" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" onclick="notifivisto()">
                    <div class="media align-items-center">
                        <span class="avatar avatar-sm rounded-circle" style="background-color: transparent;">
                            <i class="ni ni-bell-55" style="font-size:22px"></i>
                            <div class="notifi-count-conter" id="notifCount">0</div>
                        </span>
                    </div>
                </a>
                <div id="listNotif" class="dropdown-menu dropdown-menu-arrow dropdown-menu-right " style="width: 330px; max-width: 80vw;max-height:60vh;overflow-y: auto;overflow-x: hidden;">
                   <div style='text-align:center;'> No hay notificaciones</div>
                </div>
          </li>
          <!-- If user is owner or staff, show go to store-->
          @if((auth()->user()->hasRole('owner')||auth()->user()->hasRole('staff'))&&!config('settings.is_pos_cloud_mode')&&!config('app.issd'))
            @if (auth()->user()->hasRole('owner'))
              <?php $urlToVendor=auth()->user()->restaurants()->get()->first()->getLinkAttribute(); ?>
            @endif  
            @if (auth()->user()->hasRole('staff'))
            <?php $urlToVendor=auth()->user()->restaurant->getLinkAttribute(); ?>
            @endif
            <a href="{{ $urlToVendor }}" target="_blank" class="nav-link" role="button">
              <i class="ni ni-shop"></i>  
              <span class="nav-link-inner--text bold">{{ __(config('settings.vendor_entity_name'))}}</span>
            </a>
          @endif
          <!-- End owner and staf -->
            
            @if(isset($availableLanguages)&&count($availableLanguages)>1&&isset($locale))
            <li class="nav-item dropdown">
              <a href="#" class="nav-link" data-toggle="dropdown" role="button">
                <i class="ni ni-world-2"></i>
                @foreach ($availableLanguages as $short => $lang)
                  @if(strtolower($short) == strtolower($locale))<span class="nav-link-inner--text">{{ __($lang) }}</span>@endif
                @endforeach
              </a>
              <div class="dropdown-menu">
                @foreach ($availableLanguages as $short => $lang)
                <a href="{{ route('home',$short)}}" class="dropdown-item">
                  <!--<img src="{{ asset('images') }}/icons/flags/{{ strtoupper($short)}}.png" /> -->
                  {{ __($lang) }}</a>
                @endforeach
              </div>
            </li>
          @endif

           

            <li class="nav-item dropdown">
                <a class="nav-link pr-0" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <div class="media align-items-center">
                        <span class="avatar avatar-sm rounded-circle">
                            
                            <img id="profile-image-nav" alt="..." src="{{'https://www.gravatar.com/avatar/'.md5(auth()->user()->email) }}">
                        </span>
                        <div class="media-body ml-2 d-none d-lg-block">
                            <span class="mb-0 text-sm  font-weight-bold">{{ auth()->user()->name }}</span>
                        </div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-right">
                    <div class=" dropdown-header noti-title">
                        <h6 class="text-overflow m-0">{{ __('Welcome!') }}</h6>
                    </div>
                    @if (auth()->user()->hasRole('client'))
                      <a href="{{ route('home') }}" class="dropdown-item">
                        <i class="fa fa-cutlery"></i>
                        <span>{{ __('Ver Men??') }}</span>
                      </a>
                    @endif
                    
                    <a href="{{ route('profile.edit') }}" class="dropdown-item">
                        <i class="ni ni-single-02"></i>
                        <span>{{ __('My profile') }}</span>
                    </a>
                    @if(auth()->user()->hasRole('owner'))
                    <div class=" dropdown-header noti-title text-center">
                      <span class="badge badge-primary badge-pill" style="font-size: 11px;">
                        <i class="fa fa-star" style="color:#dc3545"></i> 
                        <strong style="color: #000000;">{{number_format(auth()->user()->restaurant->averageRating, 1, '.', ',')}} <span class="small">/ 5 ({{ count(auth()->user()->restaurant->ratings) }})</span></strong>
                      </span>
                      
                    </div>
                    @endif
                    
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('logout') }}" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="ni ni-user-run"></i>
                        <span>{{ __('Logout') }}</span>
                    </a>
                </div>
            </li>
        </ul>

    </div>

</nav>
