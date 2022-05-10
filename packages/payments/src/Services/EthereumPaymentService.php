<?php

namespace Packages\Payments\Services;

use App\Helpers\Api\CryptoApi;
use App\Models\Asset;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Omnipay\Omnipay;
use Packages\Payments\Helpers\Ethereum;

class EthereumPaymentService extends PaymentService implements MulticurrencyPaymentService
{
    protected $omnipay;
    protected $amount;
    protected $addressFrom;
    protected $addressTo;
    protected $baseCurrency = 'ETH';

    protected $isRedirect = FALSE;

    protected function init()
    {
        $this->omnipay = Omnipay::create('Etherscan');

        $this->omnipay->initialize([
            'api_key' => config('payments.ethereum.api_key'),
            'network' => config('payments.ethereum.network')
        ]);
    }

    public function createDeposit(array $params): PaymentService
    {
        $this->amount = $params['amount'];
        $this->addressFrom = $params['parameters']['address'];
        $this->addressTo = $this->getDepositAddress();
        $this->isRedirect = TRUE;

        return $this;
    }

    public function completeDeposit(array $params): PaymentService
    {
        return $this;
    }

    public function getPaymentAmount(): float
    {
        return $this->amount / $this->getPaymentCurrencyRate();
    }

    public function getPaymentCurrency(): string
    {
        return !$this->isToken() && $this->getMethodCurrency() == 'USD'
            ? $this->baseCurrency
            : $this->getMethodCurrency();
    }

    public function getPaymentCurrencyRate(): float
    {
        return !$this->isToken() && $this->getMethodCurrency() == 'USD'
            ? Cache::remember(sprintf('payments.%s.currencies.%s.rate', class_basename(__CLASS__), $this->baseCurrency), 300, function () {
                $api = app()->make(CryptoApi::class);
                $asset = Asset::where('symbol', $this->baseCurrency)->first();
                return $api->getPrice($asset);
            }) * $this->getMethodRate()
            : $this->getMethodRate();
    }

    public function getDepositParameters(array $request): ?array
    {
        $params = $request['parameters'];
        $params['addressFrom'] = $request['parameters']['address'];
        $params['addressTo'] = $this->getDepositAddress();
        unset($params['address']);

        if ($this->isToken()) {
            $params['contractAddress'] = $this->getTokenContractAddress();
            $params['contractDecimals'] = $this->getTokenContractDecimals();
        }

        return $params;
    }

    public function getWithdrawalParameters(array $request): ?array
    {
        $params = $request['parameters'];

        if ($this->isToken()) {
            $params['contractAddress'] = $this->getTokenContractAddress();
            $params['contractDecimals'] = $this->getTokenContractDecimals();
        }

        return $params;
    }

    /**
     * Get balance of a given Ethereum or ERC20 address
     *
     * @param string $address
     * @return PaymentService
     */
    public function fetchAddressBalance(string $address): float
    {
        if ($address) {
            $this->response = $this->isToken()
                ? $this->omnipay->fetchTokenBalance(['address' => $address, 'contract_address' => $this->getTokenContractAddress()])->send()
                : $this->omnipay->fetchBalance(['address' => $address])->send();

            if ($this->isResponseSuccessful()) {
                $balance = $this->getResponseData(); // string
                // convert balance to ETH / tokens, because it's provided in wei
                return Ethereum::fromWei($balance, $this->getDecimals());
            }
        }

        return 0;
    }

    public function fetchTransaction(string $transactionId)
    {
        $this->response = $this->omnipay->fetchTransaction(['transactionReference' => $transactionId])->send();

        return $this->isResponseSuccessful() ? $this->getResponseData() : NULL;
    }

    public function isResponseRedirect()
    {
        return $this->isRedirect ?: parent::isResponseRedirect();
    }

    public function getRedirectUrl(): string
    {
        return 'user.account.deposits.complete';
    }

    public function isExternalRedirect(): bool
    {
        return FALSE;
    }

    public function getTransactionReferenceParameterName(): string
    {
        return '';
    }

    public function getTransactionReference(): string
    {
        return '';
    }

    public function getCompletePaymentParameters(): array
    {
        return [];
    }

    public function getCancelPaymentParameters(): array
    {
        return [];
    }

    public function isToken()
    {
        return isset($this->getMethod()->payment_method_parameters->contract_address);
    }

    public function getDepositAddress()
    {
        return $this->getMethod()->payment_method_parameters->address ?? NULL;
    }

    public function getDecimals()
    {
        return $this->isToken() ? $this->getTokenContractDecimals() : 18;
    }

    public function getTokenContractAddress()
    {
        return $this->getMethod()->payment_method_parameters->contract_address ?? NULL;
    }

    public function getTokenContractDecimals()
    {
        return (int) $this->getMethod()->payment_method_parameters->contract_decimals ?? NULL;
    }

    public function fetchPaymentCurrencies(): Collection
    {
        return collect([
            $this->getPaymentCurrency() => [
                'rate_credits' => $this->getPaymentCurrencyRate()
            ]
        ]);
    }
}
