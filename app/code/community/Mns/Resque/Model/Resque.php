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

    /**
     * @param string $job
     * @param array $params
     * @return string
     */
    public function addJob($job, $params)
    {
        $trackingToken = $this->getResque()->enqueue(
            self::DEFAULT_QUEUE, $job, $params, true);

        return $trackingToken;
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
     * @return array
     */
    public function getQueues()
    {
        return $this->getResque()->queues();
    }
}