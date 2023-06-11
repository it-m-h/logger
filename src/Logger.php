<?php
declare(strict_types=1);

namespace logger;

class Logger
{
    private $config = null;
    public function __construct(array $config = null)
    {
        $this->config = include 'config.php';
        if ($config != null) {
            $this->config = $config;
        }
        if (!file_exists($this->config['DB'])) {
            $this->createDB();
        }
    }
    public function write($info)
    {
        $db = new \SQLite3($this->config['DB']);
        $sql = "INSERT INTO ".$this->config['TABLE']." (PHPSESSID, ip, host, browser, uri, info) VALUES (:PHPSESSID, :ip, :host, :browser, :uri, :info)";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':PHPSESSID', session_id(), SQLITE3_TEXT);
        $stmt->bindValue(':ip', $_SERVER['REMOTE_ADDR'], SQLITE3_TEXT);
        $stmt->bindValue(':host', gethostbyaddr($_SERVER['REMOTE_ADDR']), SQLITE3_TEXT);
        $stmt->bindValue(':browser', $_SERVER['HTTP_USER_AGENT'], SQLITE3_TEXT);
        $stmt->bindValue(':uri', $_SERVER['REQUEST_URI'], SQLITE3_TEXT);
        $stmt->bindValue(':info', $info, SQLITE3_TEXT);
        $stmt->execute();
        $db->close();
    }
    private function createDB()
    {
        $db = new \SQLite3($this->config['DB']);
        $sqlFile =  dirname(dirname(__DIR__)).'/logger/data/logging.sql';
        $sql = file_get_contents($sqlFile);
        $db->exec($sql);
        $db->close();
    }
}