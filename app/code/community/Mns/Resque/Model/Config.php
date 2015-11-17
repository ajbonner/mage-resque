<?php

class Mns_Resque_Model_Config extends Mage_Core_Model_Abstract
{
    /**
     * @return string
     */
    public function getRedisBackend()
    {
        return Mage::getStoreConfig('mnsresque/redis/backend');
    }

    /**
     * @return string
     */
    public function getDatabase()
    {
        return Mage::getStoreConfig('mnsresque/redis/database');
    }

    /**
     * @return string
     */
    public function getBinDir()
    {
        return Mage::getStoreConfig('mnsresque/env/bin_dir');
    }

    /**
     * @return int
     */
    public function getNumWorkers()
    {
        return Mage::getStoreConfig('mnsresque/env/num_workers');
    }

}
