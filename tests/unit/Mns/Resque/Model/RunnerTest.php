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
        $pid = $this->startDaemon();
        $message = 'Hello, world! - ' . microtime(true) * 1000;
        $this->client->addJob('Mns_Resque_Model_Job_Logmessage', array('message' => $message));
        $this->stopDaemon($pid);
        $this->assertSystemLogContains($message);
    }

    protected function startDaemon()
    {
        $pid = pcntl_fork();

        if (! $pid) {
            putenv('QUEUE=*');
            putenv('COUNT=1');
            fclose(STDOUT);
            Mage::getModel('mnsresque/runner')->start();
            exit(0);
        }
        usleep(1 * 1000000);
        return $pid;
    }

    protected function stopDaemon($pid)
    {
        posix_kill($pid, Mns_Resque_Model_Runner::SIGNAL_GRACEFUL);
        pcntl_waitpid($pid, $status);
    }

    protected function assertSystemLogContains($message)
    {
        $log = file_get_contents(Mage::getBaseDir() . DS . 'var' . DS . 'log' . DS . 'system.log');
        $this->assertTrue(stristr($log, $message) !== false, 'Expected message not written to log file');
    }
}