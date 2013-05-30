<?php

class Mns_Resque_Model_Job_Logmessage extends Mns_Resque_Model_Job_Abstract
{
    public function perform()
    {
        Mage::log($this->args['message']);
    }
}