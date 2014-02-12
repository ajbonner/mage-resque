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
     * @return void
     */
    public function start()
    {
        $this->configureRedisBackend($this->getConfig());
        $this->configureLog($this->getLogLevel());
        $this->configureQueue($this->getQueue());
        $path = Mage::getBaseDir('shell') . 'resque';
    }

    /**
     * @param Mns_Resque_Model_Config $config
     * @return $this
     */
    public function configureRedisBackend($config)
    {
        putenv('REDIS_BACKEND', $config->getRedisBackend());
        putenv('REDIS_BACKEND_DB', $config->getDatabase());

        return $this;
    }

    /**
     * @param string $logLevel
     * @return $this
     */
    protected function configureLog($logLevel)
    {
        switch ($logLevel) {
            case self::LOG_NORMAL:
                putenv('VERBOSE=1');
                break;
            case self::LOG_VERBOSE:
                putenv('VVERBOSE=1');
                break;
            default:
                break;
        }

        return $this;
    }

    protected function configureQueue($queue)
    {
        $queue = ($queue != '') ? $queue : '*';

        putenv("QUEUE=$queue");

        return $this;
    }
}