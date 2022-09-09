<?php
namespace oangia;

class NCrypt {
    function __construct($secret_key, $cipher_algo = 'AES-128-CBC')
    {
        $this->secret_key = $secret_key;
        $this->cipher_algo = $cipher_algo;
    }

    public function encrypt($data)
    {
        $plaintext = $data;
        $ivlen = openssl_cipher_iv_length($cipher = $this->cipher_algo);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($plaintext, $cipher, $this->secret_key, $options = OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, $this->secret_key, $as_binary = true);
        $ciphertext = base64_encode($iv . $hmac . $ciphertext_raw);
        return $ciphertext;
    }

    public function decrypt($data)
    {
        $c = base64_decode($data);
        $ivlen = openssl_cipher_iv_length($cipher = $this->cipher_algo);
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len = 32);
        $ciphertext_raw = substr($c, $ivlen + $sha2len);
        $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $this->secret_key, $options = OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac('sha256', $ciphertext_raw, $this->secret_key, $as_binary = true);
        if (hash_equals($hmac, $calcmac))
        {
            return $original_plaintext;
        }
    }
}
