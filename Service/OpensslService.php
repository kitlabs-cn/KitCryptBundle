<?php
namespace Kit\CryptBundle\Service;

class OpensslService
{
    private $clients;
    
    /**
     * 
     * @param array $clients
     */
    public function __construct($clients)
    {
        $this->clients = $clients;
    }
    /**
     * 
     * @param string $string
     * @param string $iv
     * @return boolean|string
     */
    public function encrypt($string, $name = 'default', $iv = null) {
        // hash
        $key = (strcasecmp('AES-256-CBC', $this->getMethod($name)) == 0) ? hash('sha256', $this->getSecretKey($name)) : $this->getSecretKey($name);
        $iv = ($iv === null) ? $this->getSecretIv($name) : $iv;
        if(strcasecmp('AES-256-CBC', $this->getMethod($name)) == 0 && !$this->checkIv($iv)){
            return false;
        }
        if(!$this->checkMethod($name)){
            return false;
        }
        return base64_encode(openssl_encrypt($string, $this->getMethod($name), $key, $this->getOption($name), $iv));
    }
    /**
     * 
     * @param string $string
     * @param string $iv
     * @return boolean|string
     */
    public function decrypt($string, $name = 'default', $iv = null)
    {
        // hash
        $key = (strcasecmp('AES-256-CBC', $this->getMethod($name)) == 0) ? hash('sha256', $this->getSecretKey($name)) : $this->getSecretKey($name);
        $iv = ($iv === null) ? $this->getSecretIv($name) : $iv;
        if(strcasecmp('AES-256-CBC', $this->getMethod($name)) == 0 && !$this->checkIv($iv)){
            return false;
        }
        if(!$this->checkMethod($name)){
            return false;
        }
        return openssl_decrypt(base64_decode($string), $this->getMethod($name), $key, $this->getOption($name), $iv);
    }
    /**
     * 
     * @return boolean
     */
    private function checkMethod($name = 'default')
    {
        return in_array($this->getMethod($name), openssl_get_cipher_methods(true));
    }
    /**
     * 
     * @param string $iv
     * @return boolean
     */
    private function checkIv($iv)
    {
        // iv - encrypt method aes-256-cbc expects 16 bytes - else you will get a warning
        return strlen($iv) === 16;
    }
    /**
     * 
     * @param string $name
     * @return string
     */
    private function getSecretKey($name)
    {
        return $this->getClient($name)['secret_key']; 
    }
    /**
     * 
     * @param string $name
     * @return string
     */
    private function getSecretIv($name)
    {
        return $this->getClient($name)['secret_iv'];
    }
    /**
     * 
     * @param string $name
     * @return mixed
     */
    private function getMethod($name)
    {
        return $this->getClient($name)['method'];
    }
    
    /**
     *
     * @param string $name
     * @return mixed
     */
    private function getOption($name)
    {
        return $this->getClient($name)['option'];
    }
    
    /**
     * 
     * @param string $name
     * @return array
     */
    private function getClient($name)
    {
        return isset($this->clients[$name]) ? $this->clients[$name] : $this->clients['default'];
    }
    
    /**
     * Decrypt data from a CryptoJS json encoding string
     *
     * @param mixed $passphrase
     * @param mixed $jsonString
     * @return mixed
     */
    function cryptoJsAesDecrypt($passphrase, $ct, $iv, $s)
    {
        try {
            $salt = hex2bin($s);
            $iv = hex2bin($iv);
        } catch (\Exception $e) {
            return false;
        }
        $ct = base64_decode($ct);
        $concatedPassphrase = $passphrase . $salt;
        $md5 = array();
        $md5[0] = md5($concatedPassphrase, true);
        $result = $md5[0];
        for ($i = 1; $i < 3; $i ++) {
            $md5[$i] = md5($md5[$i - 1] . $concatedPassphrase, true);
            $result .= $md5[$i];
        }
        $key = substr($result, 0, 32);
        $data = openssl_decrypt($ct, 'aes-256-cbc', $key, true, $iv);
        return trim($data, '"');
    }
    
    /**
     * Encrypt value to a cryptojs compatiable json encoding string
     *
     * @param mixed $passphrase
     * @param mixed $value
     * @return string
     */
    function cryptoJsAesEncrypt($passphrase, $value)
    {
        $salt = openssl_random_pseudo_bytes(8);
        $salted = '';
        $dx = '';
        while (strlen($salted) < 48) {
            $dx = md5($dx . $passphrase . $salt, true);
            $salted .= $dx;
        }
        $key = substr($salted, 0, 32);
        $iv = substr($salted, 32, 16);
        $encrypted_data = openssl_encrypt(json_encode($value), 'aes-256-cbc', $key, true, $iv);
        $data = array(
            "ct" => base64_encode($encrypted_data),
            "iv" => bin2hex($iv),
            "s" => bin2hex($salt)
        );
        return json_encode($data);
    }
}