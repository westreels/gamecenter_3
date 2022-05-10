<?php

namespace Packages\Payments\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Packages\Payments\Http\Requests\StoreBlockchainAddress;
use Packages\Payments\Http\Requests\VerifyBlockchainAddress;
use Packages\Payments\Models\BlockchainAddress;

class BlockchainAddressController extends Controller
{
    /**
     * @BlockchainAddress $model
     */
    protected $model;

    protected $helper;

    public function index(Request $request)
    {
        return $this->model::where('user_id', $request->user()->id)->confirmed()->get();
    }

    /**
     * Create or update an blockchain address
     *
     * @param StoreBlockchainAddress $request
     * @return mixed
     */
    public function store(StoreBlockchainAddress $request)
    {
        return $this->model::updateOrCreate(
            ['user_id' => $request->user()->id, 'address' => $request->address],
            ['message' => Str::random(20)]
        );
    }

    /**
     * Verify signtarure to confirm a given address belongs to user
     *
     * @param VerifyBlockchainAddress $request
     * @param $blockchainAddress
     * @return mixed
     */
    public function verify(VerifyBlockchainAddress $request, BlockchainAddress $blockchainAddress)
    {
        if ($this->helper::isSignatureValid($blockchainAddress->message, $request->signature, $blockchainAddress->address)) {
            $blockchainAddress->update(['confirmed' => TRUE]);
        }

        return $blockchainAddress;
    }
}
