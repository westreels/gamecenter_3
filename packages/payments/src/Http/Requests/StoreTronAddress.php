<?php

namespace Packages\Payments\Http\Requests;

class StoreTronAddress extends StoreBlockchainAddress
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'address' => 'required|size:34' //base58 format
        ];
    }
}
