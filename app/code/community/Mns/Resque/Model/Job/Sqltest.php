<?php

class Mns_Resque_Model_Job_Sqltest extends Mns_Resque_Model_Job_Abstract
{
    public function perform()
    {
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->setPageSize(10);

        Mage::log('Collection size: ' . $collection->count());
    }
}