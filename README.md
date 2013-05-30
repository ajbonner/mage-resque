# Mage Resque

Mage Resque is a magento module that provides a thin wrapper around the
functionality of the [php-resque
library](https://github.com/chrisboulton/php-resque/). PHP Resque is a PHP Implementation of
Ruby's Resque a Redis backed background job processing library.

## Getting started

## Licence
Mage Resque uses the MIT open-source licence.

### Requirements
PHP 5.3+
Redis 2.2+
ext-pcntl

### Installation
Mage Resque uses composer to handle installation of the module and its
dependencies. To install you will need a copy of composer.phar if you don't have
it availble already.

    $ curl -sS https://getcomposer.org/installer | php
    $ chmod a+x composer.phar
    $ ./composer.phar install

### Configuration
PHP Resque is a redis backed queue, and you will need to define a redis server
and default database. In local.xml add the following

    <!-- This is a child node of config for Magento CE -->
    <default>
        <mnsredis>
            <redis>
                <backend>localhost:6379</backend>
                <database>1</database>
            </redis>
        </mnsredis>
    </default>

If you use redis for magento caching be careful to select an alternative
database exclusively for mage resque. 

### Usage
PHP Resque has two functions, 1) to add jobs to the queue, and 2) a job runner
that processes jobs added to the queue. 

For exmaple To add a simple job that writes a message to the magento system log
we can use the bundled Mns_Resque_Model_Job_Logmessage class.

    Mage::getModel('mnsresque/resque')->addJob('Mns_Resque_Model_Job_Logmessage',
    array('message'=>'foo');

You can pass any class name to addJob that implements a process() method.

Mage Resque provides a job runner in the shell directory, to start it simply run

    $ php shell/resque.php --daemon

