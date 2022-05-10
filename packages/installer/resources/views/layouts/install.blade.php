<!DOCTYPE html>
<html lang="en">
<head>
  <title>{{ config('app.name') }} | Installation</title>
  <meta charset="utf-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body>
  <div id="app">
    <div class="container">
      <div class="row mt-3 mb-3">
        <div class="col">
          <h1 class="border-bottom">
            Installation: {{ $title }}
          </h1>
        </div>
      </div>
      @if ($errors->any())
        <div class="row mt-3 mb-3">
          <div class="col">
            <div class="alert alert-danger">
              <h4 class="alert-heading">
                Error:
              </h4>
              <ul>
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          </div>
        </div>
      @endif
      <div class="row">
        <div class="col">
          @yield('content')
        </div>
      </div>
    </div>
  </div>
</body>
</html>
