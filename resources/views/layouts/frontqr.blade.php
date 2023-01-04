
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('argonfront') }}/img/apple-icon.png">
    <link rel="icon" type="image/png" href="{{ asset('argonfront') }}/img/favicon.png">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ asset('argonfront') }}/js/core/jquery.min.js" type="text/javascript"></script>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    <link href="{{ asset('argon') }}/vendor/nucleo/css/nucleo.css" rel="stylesheet">
    <link href="{{ asset('argon') }}/vendor/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link type="text/css" href="{{ asset('argon') }}/css/argon.css?v=1.0.0" rel="stylesheet">
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
    <script>
      var LOCALE="<?php echo  App::getLocale() ?>";
      var SW_JS="{{asset('js/sw.js');}}";
      var SW_TYPE_N=1;
      var CASHIER_CURRENCY = "<?php echo  config('settings.cashier_currency') ?>";
      var USER_ID = '{{ isset($data->id)? $data->id:''}}';
      var PUSHER_APP_KEY = "{{ config('broadcasting.connections.pusher.key') }}";
      var PUSHER_APP_CLUSTER = "{{ config('broadcasting.connections.pusher.options.cluster') }}";
      var SITE_LOGO="{{ asset('apple-touch-icon.png') }}";
    </script>
    <script src="{{ asset('custom') }}/js/notify.min.js"></script>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script src="{{ asset('custom') }}/js/pusher.js"></script>
</head>
<body class="">
</body>
<div class="main-content">
   @yield('content')
 </div>
</html>
