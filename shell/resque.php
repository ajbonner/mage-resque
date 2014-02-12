<?php

require 'abstract.php';

class Mns_Shell_Resque extends Mage_Shell_Abstract
{
    public function run()
    {
        if ($this->getArg('daemon')) {
            $this->startResqueDaemon();
        } else if ($this->getArg('test')) {
            $this->addJob('Mns_Resque_Model_Job_Logmessage', array('message' => 'Resque Test ' . time()));
        } else if ($this->getArg('test-sql')) {
            $this->addJob('Mns_Resque_Model_Job_Sqltest');
        } else {
            echo $this->usageHelp();
        }
    }

    /**
     * @param string $jobClassName
     * @param mixed
     * @return string
     */
    protected function addJob($jobClassName, $params=array())
    {
        $resque = Mage::getModel('mnsresque/factory')->create();
        return $resque->addJob($jobClassName, $params);
    }

    protected function startResqueDaemon()
    {
        $params = array(
            'config' => Mage::getModel('mnsresque/config'),
            'log_level' => Mns_Resque_Model_Runner::LOG_NORMAL,
            'queue' => '*');

        Mage::getModel('mnsresque/runner', $params)->start();
    }

    /**
     * Retrieve Usage Help Message
     */
    public function usageHelp()
    {
        return <<<USAGE
Usage:  php resque.php -- [options]
  --daemon   Start Resque and fork into the background
  --test     Test the daemon is running by issuing a log message
  --test-sql Test the daemon handles forked processes correctly by running an sql query
  -h                    Short alias for help
  help                  This help

USAGE;
    }
}

$shell = new Mns_Shell_Resque();
$shell->run();