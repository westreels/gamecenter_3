<?php

namespace Packages\Payments\Helpers;

use Elliptic\EC;
use kornrunner\Keccak;

class Ethereum
{
    /**
     * Verify personal signature
     *
     * @param $message
     * @param $signature
     * @param $address
     * @return bool
     * @throws Exception
     */
    public static function isSignatureValid($message, $signature, $address)
    {
        $address = strtolower($address);
        $messageLength = strlen($message);
        $hash = Keccak::hash("\x19Ethereum Signed Message:\n{$messageLength}{$message}", 256);
        $sign = ['r' => substr($signature, 2, 64), 's' => substr($signature, 66, 64)];
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
        return '0x' . substr(Keccak::hash(substr(hex2bin($pubkey->encode('hex')), 1), 256), 24);
    }

    /**
     * Convert from wei to eth
     *
     * @param string $value
     * @param int $decimals
     * @return float
     */
    public static function fromWei(string $value, int $decimals = 18): float
    {
        // 1 ETH = 10^18 wei
        return $value * pow(10, -$decimals);
    }

    /**
     * Convert from eth to wei
     *
     * @param float $value
     * @return int
     */
    public static function toWei(float $value, int $decimals = 18): string
    {
        // 1 ETH = 10^18 wei
        // it's important to use BC Math functions to avoid integer overflow
        return  bcmul(sprintf('%.8f', $value), bcpow(10, $decimals));
    }

    /**
     * Convert a number from wei to padded hexadecimal
     * Example: 33333333330000000000 => 000000000000000000000000000000000000000000000001ce97ca0e5a56b400
     *
     * @param string $value
     * @return string
     */
    public static function fromWeiToPaddedHex(string $value): string
    {
        return str_pad(self::bcdechex($value), 64, '0', STR_PAD_LEFT);
    }

    /**
     * convert large numbers to hex
     * https://www.php.net/manual/en/ref.bc.php#99130
     *
     * @param $decimal
     * @return string
     */
    public static function bcdechex($decimal): string
    {
        $last = bcmod($decimal, 16);
        $remain = bcdiv(bcsub($decimal, $last), 16);

        if ($remain == 0) {
            return dechex($last);
        } else {
            return self::bcdechex($remain) . dechex($last);
        }
    }

    /**
     * convert large hex to decimal
     * https://www.php.net/manual/en/ref.bc.php#99130
     *
     * @param $decimal
     * @return string
     */
    public static function bchexdec($hex): string
    {
        if (strlen($hex) == 1) {
            return ctype_xdigit($hex) ? (string) hexdec($hex) : '0';
        } else {
            $remain = substr($hex, 0, -1);
            $last = substr($hex, -1);
            return bcadd(bcmul(16, self::bchexdec($remain)), ctype_xdigit($last) ? hexdec($last) : 0);
        }
    }
}
