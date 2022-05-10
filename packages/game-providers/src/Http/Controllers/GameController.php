<?php

namespace Packages\GameProviders\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Packages\GameProviders\Helpers\Api;

class GameController extends Controller
{
    public function __construct(Api $api)
    {
        $this->api = $api;
    }

    protected function getGameUrl(Request $request, string $provider, string $id)
    {
        if (!$this->api->getGame($id)) {
            abort(404);
        }

        return $this->api->getGameUrl($id, $request->user(), $request);
    }
}
