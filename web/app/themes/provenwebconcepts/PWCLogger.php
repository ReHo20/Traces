<?php

    /**
     * Created by PhpStorm.
     * User: Vincent
     * Date: 21-3-2018
     * Time: 09:36:55
     */
    class PWCLogger {

        const LEVEL_DEBUG = 'DEBUG';
        const LEVEL_INFO = 'INFO';
        CONST LEVEL_WARNING = 'WARNING';
        CONST LEVEL_ERROR = 'ERROR';

        /**
         * @return string
         */
        private static function getFullPath() {
            $logDir = sprintf("%s/logs/%s/", __DIR__, date('Y'));
            if (!is_dir($logDir)) {
                mkdir($logDir, 0777, true);
            }
            $logFileName = sprintf("%s.log", date('m'));

            return $logDir . $logFileName;
        }

        /**
         * @param string $file
         * @param string $line
         */
        private static function appendToFile($file, $line) {
            $data = (file_exists($file)) ? file_get_contents($file) . $line : $line;
            $data .= PHP_EOL;
            file_put_contents($file, $data);
        }

        /**
         * @param string $message
         * @param string $level
         */
        public static function log($message, $level = self::LEVEL_INFO) {
            $logFile = self::getFullPath();
            $logMessage = sprintf("%s - WP_ENV: %s - %s: %s", date('d-m-Y H:i:s'), WP_ENV, $level, $message);

            self::appendToFile($logFile, $logMessage);
        }

    }