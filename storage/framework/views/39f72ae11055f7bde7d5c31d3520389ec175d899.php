<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>">
<head>
  <title><?php echo e(config('app.name')); ?></title>
  <!-- <?php echo e(config('app.version')); ?> -->

  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="<?php echo e(__('Fair online casino games')); ?>" />
  <meta name="keywords" content="casino,blackjack,poker,slots,slot machine,baccarat,dice,roulette,online games" />

  <!-- Favicon -->
  <link rel="apple-touch-icon" sizes="180x180" href="<?php echo e(asset('images/favicon/apple-touch-icon.png')); ?>">
  <link rel="icon" type="image/png" sizes="32x32" href="<?php echo e(asset('images/favicon/favicon-32x32.png')); ?>">
  <link rel="icon" type="image/png" sizes="16x16" href="<?php echo e(asset('images/favicon/favicon-16x16.png')); ?>">
  <link rel="manifest" href="<?php echo e(file_exists(public_path('manifest-udf.json')) ? asset('manifest-udf.json') : asset('manifest.json')); ?>">
  <link rel="mask-icon" href="<?php echo e(asset('images/favicon/safari-pinned-tab.svg')); ?>" color="#5bbad5">
  <link rel="shortcut icon" href="<?php echo e(asset('images/favicon/favicon.ico')); ?>">
  <meta name="msapplication-TileColor" content="#9f00a7">
  <meta name="msapplication-config" content="<?php echo e(asset('images/favicon/browserconfig.xml')); ?>">
  <meta name="theme-color" content="#ffffff">
  <!-- END Favicon -->

  <!--Open Graph tags-->
  <meta property="og:url" content="<?php echo e(url('/')); ?>" />
  <meta property="og:type" content="website" />
  <meta property="og:title" content="<?php echo e(config('app.name')); ?>" />
  <meta property="og:description" content="<?php echo e(__('Fair online casino games')); ?>" />
  <meta property="og:image" content="<?php echo e(File::exists(public_path('images/logo/og-image-udf.jpg')) ? asset('images/logo/og-image-udf.jpg') : asset('images/logo/og-image.jpg')); ?>" />
  <!--END Open Graph tags-->

  <!--Google Tag Manager-->
  <?php if(config('services.gtm.container_id')): ?>
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
          new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
      })(window,document,'script','dataLayer', '<?php echo e(config('services.gtm.container_id')); ?>');
    </script>
  <?php endif; ?>
  <!--END Google Tag Manager-->

  <?php if(file_exists(public_path('css/style-udf.css'))): ?>
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('css/style-udf.css')); ?>">
  <?php endif; ?>
  <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
  <noscript>
    <h3><?php echo e(__('Please enable JavaScript in your browser.')); ?></h3>
  </noscript>
</head>
<body class="theme-<?php echo e(config('settings.theme.mode')); ?>" onload="if(window !== window.top) window.top.location = window.location">
  
  <div id="app"></div>

  
  <script>
    window.config = <?php echo json_encode($config, JSON_NUMERIC_CHECK, 512) ?>;
    window.routes = <?php echo json_encode($routes, JSON_NUMERIC_CHECK, 512) ?>;
    window.packages = <?php echo json_encode($packages, JSON_NUMERIC_CHECK, 512) ?>;
    window.user = <?php echo json_encode($user, JSON_NUMERIC_CHECK, 512) ?>;
    window.games = <?php echo json_encode($games, JSON_NUMERIC_CHECK, 512) ?>;
    window.assets = <?php echo json_encode($assets, JSON_NUMERIC_CHECK, 512) ?>;
  </script>

  <script src="<?php echo e(mix('js/app.js')); ?>"></script>
</body>
</html>
<?php /**PATH /home/zeroormax/bizverse-gamecenter/resources/views/index.blade.php ENDPATH**/ ?>