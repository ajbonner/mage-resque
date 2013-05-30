<?php

class Mns_Resque_Model_Runner extends Mage_Core_Model_Abstract
{
    const SIGNAL_GRACEFUL     = SIGQUIT; // Wait for child to finish processing then exit
    const SIGNAL_TERMINATE    = SIGTERM; // Immediately kill child then exit
    const SIGNAL_TERMCHILDREN = SIGUSR1; // Immediately kill child but don't exit
    const SIGNAL_PAUSE        = SIGUSR2; // Pause worker, no new jobs will be processed
    const SIGNAL_RESUME       = SIGCONT; // Resume worker

    /**
     * @return void
     */
    public function start()
    {
        $path = '/vendor/chrisboulton/php-resque/resque.php';
        require BP . $this->normalisePath($path, '/', DS);
    }

    /**
     * @param string $path
     * @param string $fromDirSeparator e.g. '/' on unix or '\' on win
     * @param string $toDirSeparator same as above
     * @return string
     */
    protected function normalisePath($path, $fromDirSeparator, $toDirSeparator)
    {
        if ($fromDirSeparator == $toDirSeparator) {
            return $path;
        } else {
            return implode($toDirSeparator, explode($fromDirSeparator, $path));
        }
    }
}