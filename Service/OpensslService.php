<?php
namespace Kit\CryptBundle\Service;

class OpensslService
{
    private $secretKey;
    private $secretIv;
    private $method;
    /**
     * 
     * @param unknown $secretKey
     * @param unknown $secretIv
     * @param unknown $method
     */
    public function __construct($secretKey, $secretIv, $method)
    {
        $this->secretKey = $secretKey;
        $this->secretIv = $secretIv;
        $this->method = $method;
    }
    /**
     * 
     * @param unknown $string
     * @param unknown $iv
     * @return boolean|string
     */
    public function encrypt($string, $iv = null) {
        
        // hash
        $key = hash('sha256', $this->secretKey);
        $iv = ($iv === null) ? $this->secretIv : $iv;
        if(!$this->checkIv($iv)){
            return false;
        }
        return base64_encode(openssl_encrypt($string, $this->method, $key, 0, $iv));
    }
    /**
     * 
     * @param unknown $string
     * @param unknown $iv
     * @return boolean|string
     */
    public function decrypt($string, $iv = null)
    {
        // hash
        $key = hash('sha256', $this->secretKey);
        $iv = ($iv === null) ? $this->secretIv : $iv;
        if(!$this->checkIv($iv)){
            return false;
        }
        return openssl_decrypt(base64_decode($string), $this->method, $key, 0, $iv);
    }
    
    private function checkMethod()
    {
        return in_array($this->method, openssl_get_cipher_methods(true));
    }
    /**
     * 
     * @param unknown $iv
     * @return boolean
     */
    private function checkIv($iv)
    {
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        return strlen($iv) === 16;
    }
}