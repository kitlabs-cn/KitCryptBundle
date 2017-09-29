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
### Setp 3: config 

	# app/config/config.yml
	kit_crypt:
	    method: 'AES-256-CBC'
	    secret_key: 'ThisIsSecret'
	    secret_iv: '1234567890abcdef'
	
PS:params  

- **method** list [openssl cipher methods](cipher_methods.md)
- **secret_iv** iv encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
	

## Usage
	
	/**
     * 
     * @var \Kit\CryptBundle\Service\OpensslService $opensslService
     */
    $opensslService = $this->get('kit_crypt.openssl');
    $encrypt = $opensslService->encrypt('lcp0578');
    dump($encrypt);
    dump($opensslService->decrypt($encrypt));
