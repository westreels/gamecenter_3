<html>
<head>
  <meta charset="utf-8">
  <title>{{ config('app.name') }}</title>
  <script>
    window.opener.postMessage({ user: @json($user) })
    window.close()
  </script>
</head>
<body>
</body>
</html>
