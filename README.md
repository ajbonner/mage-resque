# Mage Resque

Mage Resque is a lightweight [http://www.magentocommerce.com](Magento) module  implementing the [PHP Resque](https://github.com/chrisboulton/php-resque/) library. PHP Resque is a PHP Implementation of Ruby's [Resque](https://github.com/resque/resque), a Redis backed background job processing library.

## Getting started

### Licence
Mage Resque is covered by the [MIT](http://opensource.org/licenses/MIT) opensource licence. Unless specifically stated otherwise, terms of use are as laid out in the included LICENCE file.

### Requirements
- PHP 5.3+
- Redis 2.2+
- ext-pcntl

### Installation
Mage Resque uses [Composer](http://getcomposer.org) and [Magento Composer Installer](https://github.com/magento-hackathon/magento-composer-installer) to handle installation of the module and its
dependencies. To install Mage Resque you will need a copy of _composer.phar_ in your path. If you do not have it availble, run the following commands from your terminal.

    $ curl -sS https://getcomposer.org/installer | php
    $ chmod a+x composer.phar
    
If you are already using Magento Composer Installer and have an existing composer.json, add _https://github.com/ajbonner/mage-resque_ to the repositories list and _ajbonner/mage-resque_ as a required dependency for your proejct. That's it!

If you do not have an existing Magento Composer Installer composer.json file defined, you can use the following template.

	{
	    "require": {
	        "ajbonner/mage-resque": "*"
	    },
	    "require-dev": {
	        "fbrnc/Aoe_Profiler": "*",
	        "ajbonner/Mage-Test": "*"
	    },
	    "repositories": [
	    {
	        "type": "vcs",
	        "url": "https://github.com/magento-hackathon/magento-composer-installer"
	    },
	    {
	        "type": "vcs",
	        "url": "https://github.com/ajbonner/mage-composer-autoload"
	    },
	    {
	        "type": "vcs",
	        "url": "https://github.com/ajbonner/mage-resque"
	    },
	    {
	        "type": "vcs",
	        "url": "https://github.com/ajbonner/Mage-Test"
	    },
	    {
	        "type": "vcs",
	        "url": "https://github.com/fbrnc/Aoe_Profiler"
	    }
	    ],
	    "extra":{
	        "magento-root-dir": "./"
	    },
	    "config": {
	        "bin-dir": "shell"
	    },
	    "minimum-stability": "dev"
	}
    
To install Mage Resque and its dependencies just run composer.phar.

    $ ./composer.phar install

### Configuration
PHP Resque is a Redis backed job queue, and you will need access to a running Redis instance. To use a particular Redis server and database, add the following xml snippet to local.xml.

    <!-- This is a child node of config for Magento CE -->
    <default>
        <mnsredis>
            <redis>
                <backend>localhost:6379</backend>
                <database>1</database>
            </redis>
        </mnsredis>
    </default>

If you use Redis for Magento caching or as session store, e.g. you use [one of](https://github.com/colinmollenhour/Cm_Cache_Backend_Redis) Colin Mollenhour's [excellent modules](https://github.com/colinmollenhour/Cm_RedisSession), then make sure you select an alternate database that is exclusively for Mage Resque. 

### Usage
PHP Resque has two functions, add jobs to a Redis backed queue, and to run the job queue.

As an example, let's add a simple job to the queue that writes a message to Magento's system log. Mage Resque bundles Mns_Resque_Model_Job_Logmessage to do just this.

	$resque = Mage::getSingleton('mnsresque/factory')->create();
    $resque->addJob(
    	'Mns_Resque_Model_Job_Logmessage',
    	array('message'=>'foo'
    );

You can pass any class name to addJob that implements a process() method. You can find out more about how Job classes work in the [PHP Redis README](https://github.com/chrisboulton/php-resque/blob/master/README.md).

To actually process the queue and run background jobs, Mage Resque provides a job runner in the shell directory. To start it run the following command from your terminal.

    $ php shell/resque.php --daemon
    
### Running Unit Tests
Mage Resque comes bundled with a Unit Test Suite. This suite serves as a regression safety net and as rough documentation on the use the module. To execute the tests you will need to install Mage Resque's development dependencies. 

    $ composer.phar install --dev
   
If you have ran _composer.phar install_, you will need to delete composer.lock and the vendor directory, then re-run install with the --dev argument.
    
To run the tests, in the magento root directory, issue the following command from your terminal.

    $ phpunit -c vendor/ajbonner/mage-resque/tests/phpunit.xml
