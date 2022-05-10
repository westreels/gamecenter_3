<?php

namespace Packages\Payments\Helpers;

use Elliptic\EC;
use IEXBase\TronAPI\Support\Base58Check;
use kornrunner\Keccak;

class Tron
{
    const ADDRESS_PREFIX = '41';

    /**
     * Verify personal signature
     *
     * @param $message - raw message
     * @param $signature - starts with 0x
     * @param $address - address in hex format
     * @return bool
     * @throws Exception
     */
    public static function isSignatureValid($message, $signature, $address)
    {
        $address = Base58Check::decode($address, 0, 3);
        $hash = Keccak::hash("\x19TRON Signed Message:\n32{$message}", 256);
        $sign = [
            'recoveryParam' => substr($signature, 130, 2) == '1c' ? 1 : 0,
            'r' => substr($signature, 2, 64),
            's' => substr($signature, 66, 64)
        ];
        $recId  = ord(hex2bin(substr($signature, 130, 2))) - 27;

        if ($recId != ($recId & 1)) {
            return FALSE;
        }

        $ec = new EC('secp256k1');
        $publicKey = $ec->recoverPubKey($hash, $sign, $recId);

        return $address == self::publicKeyToAddress($publicKey);
    }

    public static function publicKeyToAddress($pubkey)
    {
        return self::ADDRESS_PREFIX . substr(Keccak::hash(substr(hex2bin($pubkey->encode('hex')), 1), 256), 24);
    }

    /**
     * Convert SUN to float
     *
     * @param string $value
     * @return float
     */
    public static function fromSun(string $value, int $decimals = 6): float
    {
        return bcdiv($value, bcpow(10, $decimals), 8);
    }

    /**
     * Convert float to SUN
     *
     * @param string $value
     * @return int
     */
    public static function toSun(float $value, int $decimals = 6): string
    {
        return bcmul($value, bcpow(10, $decimals));
    }
}
