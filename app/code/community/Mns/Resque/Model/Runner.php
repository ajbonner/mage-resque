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

    const DEFAULT_LOGFILE = 'mage-resque.log';
    const DEFAULT_PIDFILE = 'mage-resque.pid';

    /**
     * @var string
     */
    protected $logfile = self::DEFAULT_LOGFILE;

    /**
     * @var string
     */
    protected $pidfile = self::DEFAULT_PIDFILE;

    /**
     * @return int
     * @throws Mns_Resque_Model_ConfigurationException
     */
    public function start()
    {
        if (! $this->getConfig()) {
            throw new Mns_Resque_Model_ConfigurationException('Cannot start resque runner without redis config set');
        }

        if ($this->isDaemonRunning()) {
            throw new Mns_Resque_Model_DaemonAlreadyRunningException('Cannot start resque runner when daemon already running');
        }

        $return = null;
        $command = $this->buildStartShellCommand($this->getConfig(), $this->getLogLevel(), $this->getQueue());
        $this->disableOwnSignalHandlers();
        system($command, $return);

        return $return;
    }

    /**
     * @param bool $stopImmediately
     * @return int
     * @throws Mns_Resque_Model_PidNotFoundException
     */
    public function stop($stopImmediately = false)
    {
        $return = null;
        $command = $this->buildStopShellCommand();
        system($command, $return);

        if ($return === 0) {
            unlink($this->buildPidfilePath());
        }

        return $return;
    }

    /**
     * @param string $logfile
     * @return $this
     */
    public function setLogfile($logfile)
    {
        $this->logfile = $logfile;
        return $this;
    }

    /**
     * @return string
     */
    public function getLogfile()
    {
        return $this->logfile;
    }

    /**
     * @param string $pidfile
     * @return $this;
     */
    public function setPidfile($pidfile)
    {
        $this->pidfile = $pidfile;
        return $this;
    }

    /**
     * @return string
     */
    public function getPidfile()
    {
        return $this->pidfile;
    }

    /**
     * @return bool
     */
    public function isDaemonRunning()
    {
        return file_exists($this->buildPidfilePath());
    }

    /**
     * @param Mns_Resque_Model_Config $config
     * @param string $logLevel
     * @param string $queue
     * @return string
     */
    protected function buildStartShellCommand($config, $logLevel, $queue)
    {
        return sprintf('PIDFILE=%s REDIS_BACKEND=%s REDIS_BACKEND_DB=%s QUEUE=%s %s nohup %s >> %s 2>&1 &',
            $this->buildPidfilePath(),
            $config->getRedisBackend(),
            $config->getDatabase(),
            $this->getQueueEnv($queue),
            $this->getLogEnv($logLevel),
            Mage::getBaseDir() . DS . 'shell' . DS . 'resque',
            $this->buildLogfilePath());
    }

    /**
     * @param bool $stopImmediately
     * @return string
     * @throws Mns_Resque_Model_PidNotFoundException
     */
    protected function buildStopShellCommand($stopImmediately = false)
    {
        $pid = file_get_contents($this->buildPidfilePath());

        if (! $pid) {
            throw new Mns_Resque_Model_PidNotFoundException('Cannot stop resque process pidfile not found: ' . Mage::getBaseDir('log') . DS . $this->pidfile);
        }

        $signal = ($stopImmediately) ? self::SIGNAL_TERMINATE : self::SIGNAL_GRACEFUL;

        return sprintf('kill -%s %s', $signal, $pid);
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

    /**
     * Disabling this process's own signal handlers allows
     * system()'d resque process handle them instead
     */
    protected function disableOwnSignalHandlers()
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
     * @return string
     */
    protected function buildPidfilePath()
    {
        return Mage::getBaseDir('log') . DS . $this->getPidfile();
    }

    /**
     * @return string
     */
    protected function buildLogfilePath()
    {
        return Mage::getBaseDir('log') . DS . $this->getLogFile();
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
