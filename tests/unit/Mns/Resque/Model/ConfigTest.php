<?php

class Mns_Resque_Model_ConfigTest extends MageTest_PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function testReadSettingsFromStoreConfig()
    {
        $config = Mage::getModel('mnsresque/config');
        $this->assertTrue($this->redisBackendIsSet($config));
        $this->assertTrue($this->defaultDatabaseIsSet($config));
        $this->assertTrue($this->defaultWorkerCountIsSet($config));
    }

    /**
     * @param Mns_Resque_Model_Config $inConfig
     * @return bool
     */
    protected function redisBackendIsSet($inConfig)
    {
        return 'localhost:6379' == $inConfig->getRedisBackend();
    }

    /**
     * @param Mns_Resque_Model_Config $inConfig
     * @return bool
     */
    protected function defaultDatabaseIsSet($inConfig)
    {
        return 4 == $inConfig->getDatabase();
    }

    /**
     * @param Mns_Resque_Model_Config $inConfig
     * @return bool
     */
    protected function defaultWorkerCountIsSet($inConfig)
    {
        return 1 == $inConfig->getNumWorkers();
    }
}