<?php

namespace Core;

class AESCipher
{
    private string $key;
    private string $cipher;

    public function __construct(string $key, string $cipher = 'AES-256-CBC')
    {
        $this->key = $key;
        $this->cipher = $cipher;
    }

    public function encrypt(string $plaintext): string
    {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->cipher));
        $ciphertext = openssl_encrypt($plaintext, $this->cipher, $this->key, OPENSSL_RAW_DATA, $iv);

        return base64_encode($iv . $ciphertext);
    }

    public function decrypt(string $encryptedData): string
    {
        $data = base64_decode($encryptedData);
        $ivlen = openssl_cipher_iv_length($this->cipher);
        $iv = substr($data, 0, $ivlen);
        $ciphertext = substr($data, $ivlen);

        return openssl_decrypt($ciphertext, $this->cipher, $this->key, OPENSSL_RAW_DATA, $iv);
    }
}
