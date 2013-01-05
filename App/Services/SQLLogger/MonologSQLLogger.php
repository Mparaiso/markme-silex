<?php

/**
 * @author M.Paraiso  <mparaiso@online.fr>
 */

namespace App\Services\SQLLogger {

    use Doctrine\DBAL\Logging\SQLLogger;
use Monolog\Logger;

    /**
     * FR : log les requêtes de la base de donnée
     */
    class MonologSQLLogger implements SQLLogger {

        /**
         * Monolog logger
         * @var Monolog\Logger $_logger 
         */
        protected $_logger;

        /**
         *
         * @var integer $_startTime
         */
        protected $_startTime;

        /**
         * 
         * @param \Monolog\Logger $logger
         */
        public function __construct(Logger $logger) {
            $this->_logger = $logger;
        }

        public function startQuery($sql, array $params = null, array $types = null) {
            $this->_logger->addInfo($sql);
            if ($params!=null)
                $this->_logger->addInfo(print_r($params, true));
            if ($types!=null)
                $this->_logger->addInfo(print_r($types, true));
            $this->_startTime = microtime();
        }

        public function stopQuery() {
            $this->_logger->addInfo("Query duration : " .$this->_startTime - microtime() . "ms");
        }

    }

}