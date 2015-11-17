# Mage Resque

Mage Resque is a lightweight [Magento](http://www.magentocommerce.com) implementation of the [PHP Resque](https://github.com/chrisboulton/php-resque/) library and job runner. PHP Resque itself is based on Ruby's [Resque](https://github.com/resque/resque), a Redis backed background job processing library.

## Getting started

### Licence
Mage Resque is covered by the [MIT](http://opensource.org/licenses/MIT) opensource licence. Unless specifically stated otherwise, terms of use are as laid out in the included LICENCE file.

### Requirements
- PHP 5.3+
- Redis 2.2+
- ext-pcntl
- Magento 1.7+
- Composer

### Installation
Mage Resque uses [Composer](http://getcomposer.org) and [Magento Composer Installer](https://github.com/magento-hackathon/magento-composer-installer) to handle installation of the module and its dependencies. To install Mage Resque you will need a copy of _composer.phar_ in your path. If you do not have it availble, run the following commands from your terminal.

    $ curl -sS https://getcomposer.org/installer | php
    $ chmod a+x composer.phar
    
If you are already using Magento Composer Installer and have an existing _composer.json_, add _https://github.com/ajbonner/mage-resque_ to the repositories list and _ajbonner/mage-resque_ as a required dependency for your project. That's it!

If you do not have an existing Magento Composer Installer _composer.json_ file defined, you can use the following template.

	{
	    "require": {
	        "ajbonner/mage-resque": "*"
	    },
	    "require-dev": {
	        "fbrnc/Aoe_Profiler": "*",
	        "MageTest/Mage-Test": "*"
	    },
	    "repositories": [
	    {
	        "type": "vcs",
	        "url": "https://github.com/magento-hackathon/magento-composer-installer"
	    },
	    {
	        "type": "vcs",
	        "url": "https://github.com/ajbonner/magento-composer-autoload"
	    },
	    {
	        "type": "vcs",
	        "url": "https://github.com/ajbonner/mage-resque"
	    },
	    {
	        "type": "vcs",
	        "url": "https://github.com/MageTest/Mage-Test"
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
        <mnsresque>
            <redis>
                <backend>localhost:6379</backend>
                <database>1</database>
            </redis>
        </mnsresque>
    </default>

If you use Redis for Magento caching or as a session store, e.g. you use [one of](https://github.com/colinmollenhour/Cm_Cache_Backend_Redis) [Colin Mollenhour's](https://twitter.com/colinmollenhour) [excellent modules](https://github.com/colinmollenhour/Cm_RedisSession), then make sure you select an alternate database, or better yet, a separate Redis instance that is exclusively for Mage Resque.

If you are storing your binaries in a different directory, you can specify it in local.xml as well:

    <default>
        <mnsresque>
           ...
           <env>
             <bin_dir>bin</bin_dir>
           </env>
        </mnsresque>
    </default>

You can configure the number of resque workers to start by adding a <num_workers/> element to your mnsresque env configuation:

    <default>
        <mnsresque>
           ...
           <env>
             ...
             <num_workers>4</num_workers>
           </env>
        </mnsresque>
    </default>
    
All configuration options can be found in __app/code/community/ajbonner/mage-resque/etc/config.xml__ in the **defaults** section.

### Usage
PHP Resque has two functions, to add jobs to Redis backed job queues, and to manage workers processing jobs from those queues.

As an example, let's add a simple job to a queue that writes a message to Magento's system log. Mage Resque bundles Mns_Resque_Model_Job_Logmessage to do just this.

    $resque = Mage::getSingleton('mnsresque/factory')->create();
    $resque->addJob(
    	'Mns_Resque_Model_Job_Logmessage',
    	array('message'=>'foo'));

You can pass any classname to addJob that identifies a class implementing a process() method. You can find out more about how Job classes work in the [PHP Redis README](https://github.com/chrisboulton/php-resque/blob/master/README.md).

To process the queue and run background jobs, Mage Resque provides a job runner in the shell directory. To start it run the following command from your terminal.

    $ php shell/resque.php --daemon
    
To stop the daemon gracefully (allow workers to finish their current job) run the following command.

    $ php shell/resque.php --quit
    
To stop the daemon immediately (which means stopping workers potentially in the middle of a job) use the --terminate option.

    $ php shell/resque.php --terminate
    
### Running Unit Tests
Mage Resque comes bundled with a UnitTest Suite. This suite serves as a regression safety net and as rough documentation on the use of the module. To execute the tests you will need to install Mage Resque's development dependencies. 

    $ composer.phar install --dev
   
If you have ran _composer.phar install_, you will need to delete composer.lock and the vendor directory, then re-run install with the --dev argument.
    
To run the tests, in your Magento root directory, issue the following command from your terminal.

    $ phpunit -c vendor/ajbonner/mage-resque/tests/phpunit.xml

Feedback and pull requests are very welcome. You can get in touch with me on twitter [@ajbonner](https://twitter.com/ajbonner) or via the [issues](https://github.com/ajbonner/mage-resque/issues) system here on github.
