<?php
namespace Grabagame\BookingBundle\Service;

use Monolog\Handler\StreamHandler;

 /**
  * Logger aware class for other services to extend to get access to the logger
  *
  * @package Grabagame\BookingBundle\Service
  * @author  Chris McKinnel <chris.mckinnel@tangentlabs.co.uk>
  */
class LoggerAware {

	protected $logger;

    /**
     * @param Logger $logger   Monolog logger object
     * @param string $baseDir  Base logging directory
     * @param string $filename File name of the log file
     */    
    public function setLogger($logger, $baseUrl, $filename)
    {
        $logger->pushHandler(new StreamHandler($baseUrl.'/'.$filename.'.log', $logger::DEBUG));
        $this->logger = $logger;
    }
}
