<?php

interface Mns_Resque_Model_Job
{
    public function setUp();
    public function perform();
    public function tearDown();
}