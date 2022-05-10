<?php

namespace App\Http\Traits;

use GuzzleHttp\Client;

trait CallApiTraits {
  
  public function callapi($method, $url, $data){

    $client = new Client();

    $res = $client->request($method, $url, $data);

    $body = $res->getBody()->getContents() ?? null;

    $body = isset($body) ? json_decode($body, true) : [];

    return $body;
  }
}