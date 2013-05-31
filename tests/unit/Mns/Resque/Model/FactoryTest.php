<?php

class Mns_Resque_Model_FactoryTest extends MageTest_PHPUnit_Framework_TestCase
{
    public function testCreatesResqueModelWithDefaultConfig()
    {
        $resque = Mage::getSingleton('mnsresque/factory')->create();
        $this->assertInstanceOf('Mns_Resque_Model_Resque', $resque);
    }
}
