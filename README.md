# Mage Resque

Mage Resque is a magento module providing a thin wrapper around the
functionality of the [PHP Resque]
(https://github.com/chrisboulton/php-resque/) library. PHP Resque is a PHP Implementation of Ruby's Resque a Redis backed background job processing library.

## Getting started

### Licence
Mage Resque uses the [MIT](http://opensource.org/licenses/MIT) opensource licence. Unless specifically stated otherwise, terms of use are as laid out in the LICENCE file.

### Requirements
- PHP 5.3+
- Redis 2.2+
- ext-pcntl

### Installation
Mage Resque uses [Composer](http://getcomposer.org) and [Magento Composer Installer](https://github.com/magento-hackathon/magento-composer-installer) to handle installation of the module and its
dependencies. To install Mage Resque you will need a copy of _composer.phar_. If you don't have it availble already, run the following commands from your terminal.

    $ curl -sS https://getcomposer.org/installer | php
    $ chmod a+x composer.phar
    
If you are already using Magento Composer Installer and have an existing composer.json, simply add _https://github.com/ajbonner/mage-resque_ to the repositories list and _ajbonner/mage-resque_ as a required dependency for your proejct. 

If you do not have an existing Magento Composer Installer composer.json file defined you can use the following template

	{
	    "require": {
			"ajbonner/mage-resque": "*""
	    },
	    "repositories": [
	    {
	        "type": "vcs",
	        "url": "https://github.com/magento-hackathon/magento-composer-installer"
	    },
	    {
	        "type": "vcs",
	        "url": "https://github.com/ajbonner/mage-resque"
	    },
	    ],
	    "autoload": {
	        "psr-0": {
	            "": [
	                "vendor/magento/app",
	                "vendor/magento/app/code/local",
	                "vendor/magento/app/code/community",
	                "vendor/magento/app/code/core",
	                "vendor/magento/lib",
	                "app",
	                "app/code/local",
	                "app/code/community",
	                "app/code/core",
	                "lib"
	            ]
	        }
	    },
	    "extra":{
	        "magento-root-dir": "./"
	    },
	    "config": {
	        "bin-dir": "shell"
	    },
	    "minimum-stability": "dev"
	}
    
Now to install Mage Resque and its dependencies run composer.phar

    $ ./composer.phar install

### Configuration
PHP Resque is a Redis backed job queue, and you will need to define a Redis server and default database. The simplest we to do this is to add the following xml snippet to local.xml

    <!-- This is a child node of config for Magento CE -->
    <default>
        <mnsredis>
            <redis>
                <backend>localhost:6379</backend>
                <database>1</database>
            </redis>
        </mnsredis>
    </default>

If you use Redis for magento caching or as session store, ensure you select an alternate database exclusively for Mage Resque. 

### Usage
PHP Resque has two functions, 1) to add jobs to a redis backed queue, and 2) to process the job queue, executing tasks in the background.

As an example, let's add a simple job to the queue that writes a message to Magento's system log. Mage Resque bundles Mns_Resque_Model_Job_Logmessage to do just this.

    Mage::getModel('mnsresque/resque')->addJob(
    	'Mns_Resque_Model_Job_Logmessage',
    	array('message'=>'foo'
    );

You can pass any class name to addJob that implements a process() method. You can find out more about job classes in the [PHP Redis README](https://github.com/chrisboulton/php-resque/blob/master/README.md).

To actually process the queue and run background jobs, Mage Resque provides a job runner in the shell directory, to start it simply run

    $ php shell/resque.php --daemon
