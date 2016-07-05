<?php

require realpath(dirname(__FILE__) . '/../../../../shell/') . '/abstract.php';

class Mns_Shell_Resque extends Mage_Shell_Abstract
{
    public function run()
    {
        if ($this->getArg('daemon')) {
            exit($this->startResqueDaemon());
        } else if ($this->getArg('quit')) {
            exit($this->stopResqueDaemon());
        } else if ($this->getArg('terminate')) {
            exit($this->stopResqueDaemon(true));
        } else if ($this->getArg('test')) {
            $this->addJob('Mns_Resque_Model_Job_Logmessage', array('message' => 'Resque Test ' . time()));
            exit(0);
        } else if ($this->getArg('test-sql')) {
            $this->addJob('Mns_Resque_Model_Job_Sqltest');
            exit(0);
        } else {
            echo $this->usageHelp();
            exit(0);
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
     * @return int resque process exit status code
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

        return $returnStatus;
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
        $scriptName = $_SERVER['argv'][0];

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
