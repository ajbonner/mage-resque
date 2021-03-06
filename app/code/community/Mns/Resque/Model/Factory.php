<?php

class Mns_Resque_Model_Factory
{
    /**
     * @return Mns_Resque_Model_Resque
     */
    public function create()
    {
        $resqueClient = new Resque();
        $this->configureResque(Mage::getModel('mnsresque/config'));
        return Mage::getModel('mnsresque/resque', array('resque' => $resqueClient));
    }

    /**
     * @param Mns_Resque_Model_Config $config
     * @return $this
     */
    public function configureResque($config)
    {
        Resque::setBackend($config->getRedisBackend(), $config->getDatabase());
        return $this;
    }
}
