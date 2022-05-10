<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <title>{{ config('app.name') }}</title>
  <!-- {{ config('app.version') }} -->

  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="{{ __('Fair online casino games') }}" />
  <meta name="keywords" content="casino,blackjack,poker,slots,slot machine,baccarat,dice,roulette,online games" />

  <!-- Favicon -->
  <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon/apple-touch-icon.png') }}">
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon/favicon-32x32.png') }}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon/favicon-16x16.png') }}">
  <link rel="manifest" href="{{ file_exists(public_path('manifest-udf.json')) ? asset('manifest-udf.json') : asset('manifest.json') }}">
  <link rel="mask-icon" href="{{ asset('images/favicon/safari-pinned-tab.svg') }}" color="#5bbad5">
  <link rel="shortcut icon" href="{{ asset('images/favicon/favicon.ico') }}">
  <meta name="msapplication-TileColor" content="#9f00a7">
  <meta name="msapplication-config" content="{{ asset('images/favicon/browserconfig.xml') }}">
  <meta name="theme-color" content="#ffffff">
  <!-- END Favicon -->

  <!--Open Graph tags-->
  <meta property="og:url" content="{{ url('/') }}" />
  <meta property="og:type" content="website" />
  <meta property="og:title" content="{{ config('app.name') }}" />
  <meta property="og:description" content="{{ __('Fair online casino games') }}" />
  <meta property="og:image" content="{{ File::exists(public_path('images/logo/og-image-udf.jpg')) ? asset('images/logo/og-image-udf.jpg') : asset('images/logo/og-image.jpg') }}" />
  <!--END Open Graph tags-->

  <!--Google Tag Manager-->
  @if(config('services.gtm.container_id'))
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
          new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
      })(window,document,'script','dataLayer', '{{ config('services.gtm.container_id') }}');
    </script>
  @endif
  <!--END Google Tag Manager-->

  @if(file_exists(public_path('css/style-udf.css')))
    <link rel="stylesheet" type="text/css" href="{{ asset('css/style-udf.css') }}">
  @endif
  <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
  <noscript>
    <h3>{{ __('Please enable JavaScript in your browser.') }}</h3>
  </noscript>
</head>
<body class="theme-{{ config('settings.theme.mode') }}" onload="if(window !== window.top) window.top.location = window.location">
  {{-- SPA container --}}
  <div id="app"></div>

  {{-- Global configuration and routes --}}
  <script>
    window.config = @json($config, JSON_NUMERIC_CHECK);
    window.routes = @json($routes, JSON_NUMERIC_CHECK);
    window.packages = @json($packages, JSON_NUMERIC_CHECK);
    window.user = @json($user, JSON_NUMERIC_CHECK);
    window.games = @json($games, JSON_NUMERIC_CHECK);
    window.assets = @json($assets, JSON_NUMERIC_CHECK);
  </script>

  <script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
