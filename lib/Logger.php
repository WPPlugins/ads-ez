<?php

if (!class_exists('Logger')) {

  class Logger {

    /**
     * file - the log file to write to
     *
     * @var string
     * */
    private $file, $tag, $logLevel;
    private $levels = array('DEBUG', 'INFO', 'WARNING', 'ERROR');

    /**
     * Constructor
     * @param String logfile - [optional] Absolute file name/path. Default is a random temp file.
     * @return void
     * */
    function __construct($file = "", $tag = "") {
      if (empty($file)) {
        $file = tempnam(sys_get_temp_dir(), 'log');
      }
      $this->file = $file;
      if (!file_exists($file)) { //Attempt to create log file
        touch($file);
      }

      //Make sure we'ge got permissions
      if (!is_writable($file)) {
        //Cant write to file,
        throw new Exception("Logger Error: Can't write to log", 1);
      }
      $this->tag = $tag;
      $this->logLevel = 0;
    }

    function __destruct() {
      unlink($this->file);
    }

    /**
     * setTag - set a common tag
     * @param String tag - Log Tag
     * */
    function setTag($tag) {
      $this->tag = $tag;
    }

    /**
     * setLevel - Log Warning
     * @param String tag - Log Tag
     * @param String message - message to spit out
     * @author
     * */
    function setLevel($status) {
      $level = array_search($status, $this->levels);
      $this->logLevel = $level;
    }

    /**
     * d - Log Debug
     * @param String tag - Log Tag
     * @param String message - message to spit out
     * @return void
     * */
    public function debug($message) {
      $this->writeToLog("DEBUG", $message);
    }

    /**
     * e - Log Error
     * @param String tag - Log Tag
     * @param String message - message to spit out
     * @author
     * */
    public function error($message) {
      $this->writeToLog("ERROR", $message);
    }

    /**
     * w - Log Warning
     * @param String tag - Log Tag
     * @param String message - message to spit out
     * @author
     * */
    public function warn($message) {
      $this->writeToLog("WARNING", $message);
    }

    /**
     * i - Log Info
     * @param String message - message to spit out
     * @return void
     * */
    public function info($message) {
      $this->writeToLog("INFO", $message);
    }

    /**
     * writeToLog - writes out timestamped message to the log file as
     * defined by the $file class variable.
     *
     * @param String status - "INFO"/"DEBUG"/"ERROR" e.t.c.
     * @param String message - The message you want to output.
     * @return void
     * */
    private function writeToLog($status, $message) {
      $level = array_search($status, $this->levels);
      if ($level < $this->logLevel) {
        return;
      }
      $date = date('[Y-m-d H:i:s]');
      $msg = "$date: [$this->tag][$status] - $message" . PHP_EOL;
      file_put_contents($this->file, $msg, FILE_APPEND);
    }

    private function grep($status) {
      return implode("", preg_grep("/$status/", file($this->file)));
    }

    public function get() {
      return file_get_contents($this->file);
    }

    public function getError() {
      return $this->grep('ERROR');
    }

    public function getInfo() {
      return $this->grep('INFO');
    }

    public function getWarn() {
      return $this->grep('WARNING');
    }

    public function getDebug() {
      return $this->grep('DEBUG');
    }

  }

}