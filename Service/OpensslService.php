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
        $key = ('AES-256-CBC' == $this->getMethod($name)) ? hash('sha256', $this->getSecretKey($name)) : $this->getSecretKey($name);
        $iv = ($iv === null) ? $this->getSecretIv($name) : $iv;
        if('AES-256-CBC' == $this->getMethod($name) && !$this->checkIv($iv)){
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
        $key = ('AES-256-CBC' == $this->getMethod($name)) ? hash('sha256', $this->getSecretKey($name)) : $this->getSecretKey($name);
        $iv = ($iv === null) ? $this->getSecretIv($name) : $iv;
        if('AES-256-CBC' == $this->getMethod($name) && !$this->checkIv($iv)){
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
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
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
    
}