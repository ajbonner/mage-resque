<?php

require 'abstract.php';

class Mns_Shell_Resque extends Mage_Shell_Abstract
{
    public function run()
    {
        if ($this->getArg('daemon')) {
            $this->startResqueDaemon();
        } else if ($this->getArg('quit')) {
            $this->stopResqueDaemon();
        } else if ($this->getArg('terminate')) {
            $this->stopResqueDaemon(true);
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

    /**
     * @return int|void
     */
    protected function startResqueDaemon()
    {
        $params = array(
            'config' => Mage::getModel('mnsresque/config'),
            'log_level' => Mns_Resque_Model_Runner::LOG_NORMAL,
            'queue' => '*');

        return Mage::getModel('mnsresque/runner', $params)->start();
    }

    /**
     * @param bool $shouldKill Stop daemon immediately without letting child processes finish their work
     */
    protected function stopResqueDaemon($shouldKill = false)
    {
        $returnStatus = Mage::getModel('mnsresque/runner')->stop($shouldKill);

        if ($returnStatus != 0) {
            $stderr = fopen('php://stderr', 'w+');
            fprintf($stderr, "Could not stop resque daemon\n");
            fclose($stderr);
        } else {
            $this->waitForDaemonToStop();
        }
    }

    protected function waitForDaemonToStop()
    {
        sleep(5);
    }

    /**
     * Retrieve Usage Help Message
     */
    public function usageHelp()
    {
        $scriptNmae = $_SERVER['argv'][0];
        return <<<USAGE
Usage:  php {$scriptName} -- [options]
  --daemon [--clean] Start Resque and fork into the background, when --clean specified stop any running resque processes.
  --quit             Gracefully stop a running resque daemon
  --terminate        Stop resque daemon immediately, do not let child workers finish their current job
  --test             Test the daemon is running by issuing a log message
  --test-sql         Test the daemon handles forked processes correctly by running an sql query
  -h                 Short alias for help
  help               This help

USAGE;
    }
}

$shell = new Mns_Shell_Resque();
$shell->run();