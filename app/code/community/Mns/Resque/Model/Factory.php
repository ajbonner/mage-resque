<?php

class Mns_Resque_Model_Factory
{
    /**
     * @return Mns_Resque_Model_Resque
     */
    public static function create()
    {
        $config = Mage::getModel('mnsresque/config');
        $resqueClient = new Resque();
        $resqueClient->setBackend($config->getRedisBackend(), $config->getDatabase());
        return Mage::getModel('mnsresque/resque', array('resque' => $resqueClient));
    }
}
