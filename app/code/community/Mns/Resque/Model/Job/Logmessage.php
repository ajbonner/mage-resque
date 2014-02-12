<?php

class Mns_Resque_Model_Job_Logmessage extends Mns_Resque_Model_Job_Abstract
{
    public function perform()
    {
        if (isset($this->args['message'])) {
            Mage::log($this->args['message']);
        }
    }
}