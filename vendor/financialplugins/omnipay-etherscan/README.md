# Omnipay: Etherscan.io gateway
[Etherscan.io](https://etherscan.io) driver for the [Omnipay](https://omnipay.thephpleague.com) PHP payment processing library.

## Installation
```
composer require financialplugins/omnipay-etherscan
```
## Usage
### Initialize
This step is required before using other methods.
```
$gateway = Omnipay::create('Etherscan');
   
$gateway->initialize([
    'api_key' => '...',
    'network' => 'main' // kovan can also be used
]);
```

### Fetch Address Balance

```
$response = $gateway->fetchBalance(['address' => '0xAAAAAAAAAA...'])->send();

if ($response->isSuccessful()) {
    $data = $response->getData();
} else {
    $errorMessage = $response->getMessage();
}
```

### Fetch Contract (ERC20 token) Address Balance

```
$response = $gateway->fetchTokenBalance([
    'address' => '0xAAAAAAAAAA...',
    'contract_address' => '0xAAAAAAAAAA...'
])->send();

if ($response->isSuccessful()) {
    $data = $response->getData();
} else {
    $errorMessage = $response->getMessage();
}
```


### Fetch Transaction Info

```
$response = $gateway->fetchTransaction(['transactionReference' => '0xAAAAAAAAAA...'])->send();

if ($response->isSuccessful()) {
    $data = $response->getData();
} else {
    $errorMessage = $response->getMessage();
}
```

## Support
If you are having general issues with Omnipay, we suggest posting on [Stack Overflow](http://stackoverflow.com/). Be sure to add the [omnipay](omnipay) tag so it can be easily found.

If you want to keep up to date with release anouncements, discuss ideas for the project, or ask more detailed questions, there is also a [mailing list](https://groups.google.com/forum/#!forum/omnipay) which you can subscribe to.

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/financialplugins/omnipay-etherscan/issues), or better yet, fork the library and submit a pull request.
