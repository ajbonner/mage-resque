<?php

class Mns_Resque_Model_ResqueTest extends MageTest_PHPUnit_Framework_TestCase
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

    public function testStoresJob()
    {
        $trackingToken = $this->addJobToQueue('A_Test_Job', array('foo'=>'bar'));
        $this->assertTrue($this->jobStatusIs(Resque_Job_Status::STATUS_WAITING, $trackingToken));
    }

    public function testCanDeleteAllJobs()
    {
        $this->addJobToQueue('A_Test_Job1', array('foo'=>'bar'));
        $this->addJobToQueue('A_Test_Job2', array('foo'=>'bar'));
        $this->client->deleteAll();
        $this->assertEquals(0, $this->client->size(Mns_Resque_Model_Resque::DEFAULT_QUEUE));
    }

    public function testCanGetListOfQueues()
    {
        $this->addJobToQueue('A_Test_Job', array());
        $queues = $this->client->getQueues();
        $this->assertContains(Mns_Resque_Model_Resque::DEFAULT_QUEUE, $queues);
    }

    protected function addJobToQueue($job, $withParams)
    {
        return $this->client->addJob($job, $withParams);
    }

    protected function jobStatusIs($expectedStatus, $trackingToken)
    {
        $status = new Resque_Job_Status($trackingToken);
        return $status->get() == $expectedStatus;
    }
}