<?php
if (!class_exists('SQLite3')) {
    die('You don\'t have sqlite3 installed. Please configure your server with sqlite3.');
}

class InspectorLogger extends SQLite3
{
    const FILENAME = 'data/inspectorLogger.db';

    public function __construct()
    {
        parent::__construct(self::FILENAME);

        try {
            $this->open(self::FILENAME);
        } catch (Exception $e) {
            die('Unable to create database. Please check permissions on "<b>hackability/inspector/data</b>"');
        }
    }

    public function init()
    {
        $this->createTable();
    }

    public function createTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS inspection_log (
          ID INTEGER PRIMARY KEY AUTOINCREMENT,
          object_name TEXT,
          user_agent TEXT,
          html TEXT,
          ip VARCHAR(50),
          date_of_request DATETIME
        )";

        $result = $this->exec($sql);

        if (!$result) {
            die('Unable to create table.');
        }
    }

    public function updateData($objName, $html)
    {
        $objName = (string)$objName;
        $html = (string)$html;

        if (!strlen($objName) || !strlen($html)) {
            die('Object name or html not supplied');
        }

        $sql = "UPDATE inspection_log SET object_name = :objectName, user_agent = :userAgent, html = :html, date_of_request = DATETIME('now','localtime') WHERE ip = :ip";
        $prepareStatement = $this->prepare($sql);
        $prepareStatement->bindParam(':objectName', $objName);
        $prepareStatement->bindParam(':userAgent', $_SERVER['HTTP_USER_AGENT']);
        $prepareStatement->bindParam(':html', $html);
        $prepareStatement->bindParam(':ip', $_SERVER['REMOTE_ADDR']);
        $result = $prepareStatement->execute();

        if (!$result) {
            die('Unable to store data');
        }
    }

    public function insertData($objName, $html)
    {
        $objName = (string)$objName;
        $html = (string)$html;

        if (!strlen($objName) || !strlen($html)) {
            die('Object name or html not supplied');
        }

        $sql = "INSERT INTO inspection_log (object_name, user_agent, html, ip, date_of_request) VALUES(:objectName, :userAgent, :html, :ip, DATETIME('now','localtime'))";
        $prepareStatement = $this->prepare($sql);
        $prepareStatement->bindParam(':objectName', $objName);
        $prepareStatement->bindParam(':userAgent', $_SERVER['HTTP_USER_AGENT']);
        $prepareStatement->bindParam(':html', $html);
        $prepareStatement->bindParam(':ip', $_SERVER['REMOTE_ADDR']);
        $result = $prepareStatement->execute();

        if (!$result) {
            die('Unable to store data');
        }
    }
}
