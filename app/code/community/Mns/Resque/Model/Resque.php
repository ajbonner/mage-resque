<?php

/**
 * Class Mns_Resque_Model_Resque
 *
 * @method Resque getResque()
 * @method Mns_Resque_Model_Resque setResque(Resque $queue)
 */
class Mns_Resque_Model_Resque extends Mage_Core_Model_Abstract
{
    const DEFAULT_QUEUE = 'default';

    const JOB_STATUS_WAITING = Resque_Job_Status::STATUS_WAITING;

    /**
     * @param string $jobClassName
     * @param array $params
     * @return string tracking token issued by backend
     */
    public function addJob($jobClassName, $params)
    {
        $trackingToken = $this->getResque()->enqueue(
            self::DEFAULT_QUEUE, $jobClassName, $params, true);

        return $trackingToken;
    }

    /**
     * @param string $trackingToken
     * @return mixed
     */
    public function status($trackingToken)
    {
        $status = new Resque_Job_Status($trackingToken);

        return $status->get();
    }

    /**
     * @return Resque_Job
     */
    public function popNextJob()
    {
        return Resque::reserve(self::DEFAULT_QUEUE);
    }

    /**
     * @param string $ofQueue
     * @return int
     */
    public function size($ofQueue)
    {
        return $this->getResque()->size($ofQueue);
    }

    /**
     * @return $this
     */
    public function deleteAll()
    {
        $this->getResque()->redis()->flushdb();
    }

    /**
     * @return array collection containing names of available queues
     */
    public function getQueues()
    {
        return $this->getResque()->queues();
    }
}