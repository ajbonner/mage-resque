<?php

/**
 * Class Mns_Resque_Model_Runner
 *
 * @method Mns_Resque_Model_Config getConfig()
 * @method Mns_Resque_Model_Runner setConfig(Mns_Resque_Model_Config $config)
 * @method string getQueue()
 * @method Mns_Resque_Model_Runner setQueue(string)
 * @method string getLogLevel()
 * @method Mns_Resque_Model_Runner setLogLevel(string $logLevel)
 */
class Mns_Resque_Model_Runner extends Mage_Core_Model_Abstract
{
    const SIGNAL_GRACEFUL     = SIGQUIT; // Wait for child to finish processing then exit
    const SIGNAL_TERMINATE    = SIGTERM; // Immediately kill child then exit
    const SIGNAL_TERMCHILDREN = SIGUSR1; // Immediately kill child but don't exit
    const SIGNAL_PAUSE        = SIGUSR2; // Pause worker, no new jobs will be processed
    const SIGNAL_RESUME       = SIGCONT; // Resume worker

    const LOG_NONE      = '';
    const LOG_NORMAL    = 'VERBOSE';
    const LOG_VERBOSE   = 'VVERBOSE';

    /**
     * @return int
     * @throws Exception
     */
    public function start()
    {
        if (! $this->getConfig()) {
            throw new Exception('Cannot start resque runner without redis config set');
        }

        $command = $this->buildShellCommand($this->getConfig(), $this->getLogLevel(), $this->getQueue());

        $this->overrideSignalHandlers();

        system($command, &$return);

        return $return;
    }

    /**
     * @param Mns_Resque_Model_Config $config
     * @param string $logLevel
     * @param string $queue
     * @return string
     */
    protected function buildShellCommand($config, $logLevel, $queue)
    {
        return sprintf('PIDFILE=%s REDIS_BACKEND=%s REDIS_BACKEND_DB=%s QUEUE=%s %s %s > %s 2>&1 &',
            Mage::getBaseDir('log') . DS . 'mage-resque.pid',
            $config->getRedisBackend(),
            $config->getDatabase(),
            $this->getQueueEnv($queue),
            $this->getLogEnv($logLevel),
            Mage::getBaseDir() . DS . 'shell' . DS . 'resque',
            Mage::getBaseDir('log') . DS . 'mage-resque.log');
    }

    /**
     * @param string $logLevel
     * @return string
     */
    protected function getLogEnv($logLevel)
    {
        $logEnv = '';

        switch ($logLevel) {
            case self::LOG_NORMAL:
                $logEnv = 'VERBOSE=1';
                break;
            case self::LOG_VERBOSE:
                $logEnv = 'VVERBOSE=1';
                break;
            default:
        }

        return $logEnv;
    }

    protected function overrideSignalHandlers()
    {
        declare(ticks = 1);
        pcntl_signal(SIGTERM, function(){});
        pcntl_signal(SIGINT,  function(){});
        pcntl_signal(SIGQUIT, function(){});
        pcntl_signal(SIGUSR1, function(){});
        pcntl_signal(SIGUSR2, function(){});
        pcntl_signal(SIGCONT, function(){});
    }

    /**
     * @param string $queue
     * @return string
     */
    protected function getQueueEnv($queue)
    {
        $queue = ($queue != '') ? $queue : '*';

        return $queue;
    }
}
