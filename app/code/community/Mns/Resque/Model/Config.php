<?php

class Mns_Resque_Model_Config extends Mage_Core_Model_Abstract
{
    const DEFAULT_BIN_DIR = 'shell';
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
        $result = Mage::getStoreConfig('mnsresque/env/bin_dir');
        if ($result) {
            $result = static::DEFAULT_BIN_DIR;
        }

        return $result;
    }
}
