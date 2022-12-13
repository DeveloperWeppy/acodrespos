<div class="row align-items-center justify-content-xl-between">
    <div class="col-md-6">
        <div class="copyright">
          @if (Request::route()->getName()=='pqrs.validateaccespqr')
            &copy; {{ date('Y') }} <a href="" target="_blank" class="text-primary">{{ config('global.site_name', 'mResto') }}</a>.
          @else
            &copy; {{ date('Y') }} <a href="" target="_blank">{{ config('global.site_name', 'mResto') }}</a>.
          @endif
        </div>
      </div>
      <div class="col-md-6">
        <ul id="footer-pages" class="nav nav-footer justify-content-end">
          @if (Request::route()->getName()=='pqrs.validateaccespqr')
            <li v-for="page in pages" class="nav-item" v-cloak>
              <a :href="'/pages/' + page.id" class="nav-link text-white">@{{ page.title }}</a>
            </li>
            <li class="nav-item">
              <a  target="_blank" class="button nav-link nav-link-icon text-white" href="{{ route('pqrs.index') }}">Centro de Ayuda</a>
            </li>
          @else
            <li v-for="page in pages" class="nav-item" v-cloak>
              <a :href="'/pages/' + page.id" class="nav-link text-primary">@{{ page.title }}</a>
            </li>
            <li class="nav-item">
              <a  target="_blank" class="button nav-link nav-link-icon text-primary" href="{{ route('pqrs.index') }}">Centro de Ayuda</a>
          </li>
          @endif
          
        {{-- @if (!config('settings.single_mode')&&config('settings.restaurant_link_register_position')=="footer")
          <li class="nav-item">
            <a  target="_blank" class="button nav-link nav-link-icon" href="{{ route('newrestaurant.register') }}">Agrega tu Restaurante</a>
          </li>
        @endif 
        @if (config('app.isft')&&config('settings.driver_link_register_position')=="footer")
        <li class="nav-item">
            <a target="_blank" class="button nav-link nav-link-icon" href="{{ route('driver.register') }}">¿Quieres conducir para nosotros?</a>
          </li>
          @endif--}}

        </ul>
    </div>
</div>