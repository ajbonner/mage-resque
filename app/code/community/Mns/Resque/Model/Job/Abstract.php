<?php

class Mns_Resque_Model_Job_Abstract extends Mage_Core_Model_Abstract
    implements Mns_Resque_Model_Job
{
    /**
     * @var array
     */
    public $args = array();

    /**
     * @var Resque_Job
     */
    public $job;

    /**
     * @var string The name of the queue that this job belongs to.
     */
    public $queue;

    /**
     * Bootstrap a Magento environment for the job
     *
     * By default bootstrap the entire store but for admin tasks you could
     * selectively bootstrap for efficiency
     */
    public function setUp()
    {
        Mage::init();
    }

    public function perform()
    {
        // .. Run job
    }

    public function tearDown()
    {
        // ... Remove environment for this job
    }
}