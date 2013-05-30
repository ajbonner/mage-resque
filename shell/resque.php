<?php

require 'abstract.php';

class Mns_Shell_Resque extends Mage_Shell_Abstract
{
    public function run()
    {
        if ($this->getArg('daemon')) {
            Mage::getModel('mnsresque/runner')->start();
        } else {
            echo $this->usageHelp();
        }
    }

    /**
     * Retrieve Usage Help Message
     */
    public function usageHelp()
    {
        return <<<USAGE
Usage:  php resque.php -- [options]
  --daemon Start Resque and fork into the background
  -h                    Short alias for help
  help                  This help

USAGE;
    }
}

$shell = new Mns_Shell_Resque();
$shell->run();