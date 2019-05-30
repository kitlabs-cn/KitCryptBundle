# KitCryptBundle
Symfony Crypt Bundle(use openssl)


## Installation
 
### Step 1: Download the Bundle
---------------------------
 
Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:
 
	
	$ composer require kitlabs/kit-crypt-bundle

 
This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.
 
### Step 2: Enable the Bundle
---------------------------
 
Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:
``` php
<?php
// app/AppKernel.php
 
// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
 
            new Kit\CryptBundle\KitCryptBundle(),
        );
 
        // ...
    }
 
    // ...
}
```
### Setp 3: config 
``` yaml
# app/config/config.yml
kit_crypt:
    clients:
        default:
            method: 'AES-256-CBC'
            secret_key: 'Kit@Crypt!Bundle'
            secret_iv: '12345!@#$%^67890' #16 bit
            option: 0 # 0默认值;OPENSSL_RAW_DATA = 1,采用PKCS7填充;OPENSSL_ZERO_PADDING = 2，采用0填充; OPENSSL_NO_PADDING = 3,不填充
        data_api:# client name,可以是多个
            method: 'DES-CBC'
            secret_key: 'Kit@Crypt!Bundle'
            secret_iv: 'q1w2e3r4'
            option: 1
```
	
PS:params  

- **method** list [openssl cipher methods](cipher_methods.md)
- **secret_iv** iv encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
	

## Usage
``` php
/**
 * 
 * @var \Kit\CryptBundle\Service\OpensslService $opensslService
 */
$opensslService = $this->get('kit_crypt.openssl');
$encrypt = $opensslService->encrypt('lcp0578', 'data_api'); //public function encrypt($string, $name = 'default', $iv = null)
dump($encrypt);
dump($opensslService->decrypt($encrypt, 'data_api')); //public function decrypt($string, $name = 'default', $iv = null)
```
