# Omnipay: Trongrid.io gateway
[Trongrid.io](https://trongrid.io) driver for the [Omnipay](https://omnipay.thephpleague.com) PHP payment processing library.

## Installation
```
composer require financialplugins/omnipay-trongrid
```
## Usage
### Initialize
This step is required before using other methods.
```
$gateway = Omnipay::create('Trongrid');
   
$gateway->initialize([
    'network' => 'api.trongrid.io' // 'api.shasta.trongrid.io'
]);
```

### Fetch Address Balance

```
$response = $gateway->fetchBalance(['address' => '4158b637e78d6f2069a50976c4e3719a1d8bea7cea'])->send();

if ($response->isSuccessful()) {
    $data = $response->getData();
} else {
    $errorMessage = $response->getMessage();
}
```

Note that the account balance is returned in SUN, not decimal.

### Fetch Transaction Info

```
$response = $gateway->fetchTransaction(['transaction_id' => '109a0ea9b4c50f15f6a73ee823dabafe020471b6fcb4340059252bd19fd6320f'])->send();

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
