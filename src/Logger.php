<?php
declare(strict_types=1);

namespace logger;

class Logger
{
    private $config = null;
    public function __construct(array $config = null)
    {
        if ($config != null) {
            $this->config = $config;
        } else {
            $this->config = include('config.php');
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
    public function getData($limit = 100)
    {
        $db = new \SQLite3($this->config['DB']);
        $sql = "SELECT * FROM ".$this->config['TABLE']." ORDER BY id DESC LIMIT :limit";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':limit', $limit, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $data = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $data[] = $row;
        }
        $db->close();
        return $data;
    }
        
    /**
     * getDataBy
     *
     * @param  string $column Name of column
     * @return array
     */
    public function getDataBy($column)
    {
        $db = new \SQLite3($this->config['DB']);
        $sql = "SELECT * FROM ".$this->config['TABLE']." ORDER BY logdate DESC";
        $stmt = $db->prepare($sql);
        $result = $stmt->execute();
        $data = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $data[] = $row;
        }
        $db->close();
        $group = array();
        foreach ($data as $value) {
            $group[$value[$column]][] = $value;
        }

        return $group;
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