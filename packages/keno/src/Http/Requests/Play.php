<?php

namespace Packages\Keno\Http\Requests;

use App\Http\Requests\PlayGame;
use Packages\Keno\Models\Keno;
use Packages\Keno\Services\GameService;

class Play extends PlayGame
{
    protected $gamePackageId = 'keno';
    protected $gameableClass = Keno::class;

    public function rules()
    {
        return [
            'hash' => 'required|size:64',
            'numbers' => [
                'required',
                'array',
                'size:' . intval(config('keno.select_count'))
            ],
            'numbers.*' => [
                'integer',
                'distinct',
                'min:1',
                'max:' . GameService::getMaxNumber()
            ],
        ];
    }
}
