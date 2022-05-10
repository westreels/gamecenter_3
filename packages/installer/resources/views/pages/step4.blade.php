@extends('installer::layouts.install')

@section('content')
  <p>Congratulations! Installation is completed and <b>{{ config('app.name') }}</b> is now up and running.</p>
  <p>
    In order for the application and all games to work correctly please add the following cron job to your server
    (if you add the cron job via cPanel you need to omit the leading asterisk symbols):
  </p>
  <div class="alert alert-info mb-3">
    <pre class="mb-0">{{ \App\Helpers\Utils::getCronJobCommand() }}</pre>
  </div>
  <a href="/" class="btn btn-primary" target="_blank">Front page</a>
  <a href="/login" class="btn btn-primary" target="_blank">Log in</a>
@endsection
