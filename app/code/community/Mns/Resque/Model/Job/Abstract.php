<?php

class Mns_Resque_Model_Job_Abstract extends Mage_Core_Model_Abstract
{
    public function setUp()
    {
        // ... Set up environment for this job
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