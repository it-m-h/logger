CREATE TABLE IF NOT EXISTS log(
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    logdate TIMESTAMP DEFAULT (datetime(CURRENT_TIMESTAMP, 'localtime')),
    PHPSESSID TEXT(255),
    ip TEXT(255),
    host TEXT(255),
    browser TEXT(255),
    uri TEXT(255),
    info BLOB,
    active INT(1) NOT NULL DEFAULT 1
);