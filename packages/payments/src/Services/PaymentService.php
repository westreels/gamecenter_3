<?php

namespace Packages\Payments\Services;

use Packages\Payments\Models\Method;
use Packages\Payments\Models\PaymentGateway;
use Packages\Payments\Models\PaymentGatewayMethod;

abstract class PaymentService
{
    protected $response;

    private $method;
    private $paymentMethod;
    private $gateway;

    public function __construct(Method $method)
    {
        $this->method = $method;

        $this->init();

        return $this;
    }

    /**
     * Get linked DepositMethod or WithdrawalMethod model
     *
     * @return Method
     */
    public final function getMethod(): Method
    {
        return $this->method;
    }

    /**
     * Get PaymentGatewayMethod model
     *
     * @return PaymentGatewayMethod|null
     */
    public final function getPaymentGatewayMethod(): ?PaymentGatewayMethod
    {
        return $this->method->paymentMethod;
    }

    /**
     * Get PaymentGateway model
     *
     * @return PaymentGateway|null
     */
    public final function getPaymentGateway(): ?PaymentGateway
    {
        return $this->getPaymentGatewayMethod() ? $this->getPaymentGatewayMethod()->gateway : NULL;
    }

    public final function getMethodCurrency(): string
    {
        return $this->getMethod()->currency;
    }

    public final function getMethodRate(): float
    {
        return $this->getMethod()->rate;
    }

    public final function getDepositReturnUrl(): string
    {
        return route('payments.webhooks.deposits.complete', ['depositMethod' => $this->method]);
    }

    public final function getDepositCancelUrl(): string
    {
        return route('payments.webhooks.deposits.cancel', ['depositMethod' => $this->method]);
    }

    public final function getDepositNotifyUrl(): string
    {
        return route('payments.webhooks.deposits.ipn', ['depositMethod' => $this->method]);
    }

    public final function getWithdrawalNotifyUrl(): string
    {
        return route('payments.webhooks.withdrawals.ipn', ['withdrawalMethod' => $this->method]);
    }

    protected function getDepositDescription(): string
    {
        return __('Credits purchase');
    }

    /**
     * Get deposit extra parameters (such as destination tag for instance)
     *
     * @return object|null
     */
    public function getDepositParameters(array $request): ?array
    {
        return NULL;
    }

    public function getWithdrawalParameters(array $request): ?array
    {
        return NULL;
    }

    /**
     * Get payment amount
     *
     * @return float
     */
    public function getPaymentAmount(): float
    {
        return (float) $this->response->getRequest()->getAmount();
    }

    /**
     * Get payment currency (child classes can override this method)
     *
     * @return string
     */
    public function getPaymentCurrency(): string
    {
        return $this->response->getRequest()->getCurrency();
    }

    /**
     * Get API response object
     *
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    public function isResponseSuccessful()
    {
        return is_object($this->response) ? $this->response->isSuccessful() : FALSE;
    }

    public function isResponseRedirect()
    {
        return is_object($this->response) ? $this->response->isRedirect() : FALSE;
    }

    public function getResponseData()
    {
        return is_object($this->response) ? $this->response->getData() : NULL;
    }

    public function getErrorMessage()
    {
        return is_object($this->response) ? $this->response->getMessage() : NULL;
    }

    public function getRedirectUrl(): ?string
    {
        return is_object($this->response) ? $this->response->getRedirectUrl() : NULL;
    }

    public function isExternalRedirect(): bool
    {
        return TRUE;
    }

    /**
     * Create a PaymentService instance using deposit / withdrawal method model
     *
     * @param Method $method
     * @return PaymentService
     */
    public final static function createFromModel(Method $method): PaymentService
    {
        $paymentServiceClass = 'Packages\\Payments\\Services\\' . ucfirst($method->paymentMethod->gateway->code) . 'PaymentService';

        return new $paymentServiceClass($method);
    }

    // init payment service
    abstract protected function init();

    abstract public function createDeposit(array $params): PaymentService;

    abstract public function completeDeposit(array $params): PaymentService;

    abstract public function getTransactionReferenceParameterName(): string;

    abstract public function getTransactionReference(): string;

    // parameters required to complete a payment
    abstract public function getCompletePaymentParameters(): array;

    // parameters required to cancel a payment
    abstract public function getCancelPaymentParameters(): array;
}
