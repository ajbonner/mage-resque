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
        putenv('QUEUE=*');
        if ($pid = pcntl_fork()) {
            sleep(1); // let the daemon startup
            $message = 'Hello, world! - ' . microtime(true) * 1000;
            $this->client->addJob('Mns_Resque_Model_Job_Logmessage', array('message' => $message));
            posix_kill($pid, Mns_Resque_Model_Runner::SIGNAL_GRACEFUL);
            pcntl_waitpid($pid, $status);
            $this->assertSystemLogContains($message);
        } else {
            Mage::getModel('mnsresque/runner')->start();
            exit(0);
        }
    }

    protected function assertSystemLogContains($message)
    {
        $log = file_get_contents(Mage::getBaseDir() . DS . 'var' . DS . 'log' . DS . 'system.log');
        $this->assertTrue(stristr($log, $message) !== false, 'Expected message not written to log file');
    }
}