<?php

use Packages\Payments\Http\Controllers\Admin\DashboardController;
use Packages\Payments\Http\Controllers\Admin\DepositController as AdminDepositController;
use Packages\Payments\Http\Controllers\Admin\DepositMethodController as AdminDepositMethodController;
use Packages\Payments\Http\Controllers\Admin\PaymentGatewayController;
use Packages\Payments\Http\Controllers\Admin\WithdrawalController as AdminWithdrawalController;
use Packages\Payments\Http\Controllers\Admin\WithdrawalMethodController as AdminWithdrawalMethodController;
use Packages\Payments\Http\Controllers\BscAddressController;
use Packages\Payments\Http\Controllers\DepositController;
use Packages\Payments\Http\Controllers\DepositMethodController;
use Packages\Payments\Http\Controllers\EthereumAddressController;
use Packages\Payments\Http\Controllers\PolygonAddressController;
use Packages\Payments\Http\Controllers\TronAddressController;
use Packages\Payments\Http\Controllers\WebhookController;
use Packages\Payments\Http\Controllers\WithdrawalController;
use Packages\Payments\Http\Controllers\WithdrawalMethodController;

Route::name('payments.')
    ->prefix('api')
    ->middleware(['api', 'auth:sanctum', 'verified', 'active', '2fa', 'last_seen'])
    ->group(function () {
        // deposits
        Route::get('deposits', [DepositController::class, 'index']);
        Route::get('deposits/{deposit}', [DepositController::class, 'show']);
        Route::post('deposits/methods/{depositMethod}', [DepositController::class, 'store'])->middleware('concurrent');
        Route::patch('deposits/methods/{depositMethod}/{deposit}', [DepositController::class, 'update'])->middleware('concurrent');
        // withdrawals
        Route::get('withdrawals', [WithdrawalController::class, 'index']);
        Route::post('withdrawals/methods/{withdrawalMethod}', [WithdrawalController::class, 'store'])->middleware('concurrent');
        // deposit methods
        Route::get('deposit-methods', [DepositMethodController::class, 'index']);
        // withdrawal methods
        Route::get('withdrawal-methods', [WithdrawalMethodController::class, 'index']);
        // blockchain address storage & confirmation
        collect([
                'ethereum' => EthereumAddressController::class,
                'bsc' => BscAddressController::class,
                'polygon' => PolygonAddressController::class,
                'tron' => TronAddressController::class
            ])
            ->each(function ($controller, $blockchain) {
                Route::get('blockchains/' . $blockchain . '/addresses', [$controller, 'index']);
                Route::post('blockchains/' . $blockchain . '/addresses', [$controller, 'store'])->middleware('concurrent')->name('blockchains.addresses.store');
                Route::post('blockchains/' . $blockchain . '/addresses/{blockchainAddress}/verify', [$controller, 'verify'])->middleware('concurrent');
            });
    });

Route::name('payments.')
    ->prefix('api')
    ->middleware(['api'])
    ->group(function () {
        // webhooks / IPN
        Route::get('webhooks/deposit-methods/{depositMethod}/complete', [WebhookController::class, 'completeDeposit'])->name('webhooks.deposits.complete');
        Route::get('webhooks/deposit-methods/{depositMethod}/cancel', [WebhookController::class, 'cancelDeposit'])->name('webhooks.deposits.cancel');
        Route::post('webhooks/deposit-methods/{depositMethod}/ipn', [WebhookController::class, 'ipnDeposit'])->name('webhooks.deposits.ipn');
        Route::post('webhooks/withdrawal-methods/{withdrawalMethod}/ipn', [WebhookController::class, 'ipnWithdrawal'])->name('webhooks.withdrawals.ipn');
    });

// ADMIN ROUTES
Route::namespace('Packages\Payments\Http\Controllers\Admin')
    ->name('admin.')
    ->prefix('api/admin')
    ->middleware(['api', 'auth:sanctum', 'verified', 'active', '2fa', 'role:' . App\Models\User::ROLE_ADMIN, 'last_seen'])
    ->group(function () {
        // dashboard
        Route::get('dashboard/payments/data/{key}', [DashboardController::class, 'getData']);
        // deposits
        Route::get('deposits', [AdminDepositController::class, 'index']);
        Route::get('deposits/{deposit}', [AdminDepositController::class, 'show']);
        Route::get('deposits/{deposit}/transaction', [AdminDepositController::class, 'transaction']);
        Route::post('deposits/{deposit}/cancel', [AdminDepositController::class, 'cancel']);
        Route::post('deposits/{deposit}/complete', [AdminDepositController::class, 'complete']);
        // withdrawals
        Route::get('withdrawals', [AdminWithdrawalController::class, 'index']);
        Route::get('withdrawals/{withdrawal}', [AdminWithdrawalController::class, 'show']);
        Route::patch('withdrawals/{withdrawal}', [AdminWithdrawalController::class, 'update']);
        Route::get('withdrawals/{withdrawal}/transaction', [AdminWithdrawalController::class, 'transaction']);
        Route::post('withdrawals/{withdrawal}/send', [AdminWithdrawalController::class, 'send']);
        Route::post('withdrawals/{withdrawal}/cancel', [AdminWithdrawalController::class, 'cancel']);
        Route::post('withdrawals/{withdrawal}/complete', [AdminWithdrawalController::class, 'complete']);
        // payment gateways
        Route::get('payment-gateways', [PaymentGatewayController::class, 'index']);
        // deposit methods
        Route::get('deposit-methods', [AdminDepositMethodController::class, 'index']);
        Route::post('deposit-methods', [AdminDepositMethodController::class, 'store']);
        Route::get('deposit-methods/{depositMethod}', [AdminDepositMethodController::class, 'show']);
        Route::get('deposit-methods/{depositMethod}/info', [AdminDepositMethodController::class, 'info']);
        Route::get('deposit-methods/{depositMethod}/balance', [AdminDepositMethodController::class, 'balance']);
        Route::patch('deposit-methods/{depositMethod}', [AdminDepositMethodController::class, 'update'])->name('deposit-methods.update');
        Route::delete('deposit-methods/{depositMethod}', [AdminDepositMethodController::class, 'destroy']);
        // withdrawals methods
        Route::get('withdrawal-methods', [AdminWithdrawalMethodController::class, 'index']);
        Route::post('withdrawal-methods', [AdminWithdrawalMethodController::class, 'store']);
        Route::get('withdrawal-methods/{withdrawalMethod}', [AdminWithdrawalMethodController::class, 'show']);
        Route::get('withdrawal-methods/{withdrawalMethod}/info', [AdminWithdrawalMethodController::class, 'info']);
        Route::get('withdrawal-methods/{withdrawalMethod}/balance', [AdminWithdrawalMethodController::class, 'balance']);
        Route::patch('withdrawal-methods/{withdrawalMethod}', [AdminWithdrawalMethodController::class, 'update'])->name('withdrawal-methods.update');
        Route::delete('withdrawal-methods/{withdrawalMethod}', [AdminWithdrawalMethodController::class, 'destroy']);
    });
