<?php

class Mns_Resque_Model_RunnerTest extends MageTest_PHPUnit_Framework_TestCase
{
    /**
     * @var Resque
     */
    protected $resque;

    /**
     * @var Mns_Resque_Model_Resque
     */
    protected $client;

    public function setUp()
    {
        parent::setUp();
        $config = Mage::getModel('mnsresque/config');
        $this->resque = new Resque();
        $this->resque->setBackend($config->getRedisBackend(), $config->getDatabase());
        $this->client = Mage::getModel('mnsresque/resque', array('resque' => $this->resque));
        $this->client->deleteAll();
    }

    public function testDaemonRunsQueuedJob()
    {
        $this->startDaemon();
        $message = 'Hello, world! - ' . microtime(true) * 1000;
        $this->client->addJob('Mns_Resque_Model_Job_Logmessage', array('message' => $message));
        $this->stopDaemon();
        $this->assertSystemLogContains($message);
    }

    protected function startDaemon()
    {
            Mage::getModel('mnsresque/runner')->start();
    }

    protected function stopDaemon($pid)
    {
        Mage::getModel('mnsresque/runner')->stop();
    }

    protected function assertSystemLogContains($message)
    {
        $log = file_get_contents(Mage::getBaseDir() . DS . 'var' . DS . 'log' . DS . 'system.log');
        $this->assertTrue(stristr($log, $message) !== false, 'Expected message not written to log file');
    }
}
