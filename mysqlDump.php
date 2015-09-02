<?php

class MysqlDump
{

    protected $_host;
    protected $_user;
    protected $_pass;
    protected $_dbname;

    public function __construct($config = array())
    {

        $defaultConfig = array(
            'host' => 'localhost',
            'user' => 'root',
            'pass' => '1234',
            'dbname' => ''
        );

        if (empty($config)) {
            $config = $defaultConfig;
        } else {
            $config = array_replace($defaultConfig, $config);
        }

        $this->setHost($config['host']);
        $this->setUser($config['user']);
        $this->setPass($config['pass']);
        $this->setDbName($config['dbname']);

    }

    public function dumpTable($table)
    {

        $link = $this->_connect();

        mysql_select_db(
            $this->getDbName(),
            $link
        );

        $sqlData = '';
        $sqlData .= $this->_checkTable($table);

        $name = $this->getDbName() . '.' . $table;

        $this->_writeSql($sqlData, $name);

    }

    public function dumpTables($tables = array())
    {

        $link = $this->_connect();

        mysql_select_db(
            $this->getDbName(),
            $link
        );

        $sqlData = '';
        foreach ($tables as $table) {
            $sqlData .= $this->_checkTable($table);
        }

        $name = $this->getDbName() . '.' . implode('.', $tables);

        $this->_writeSql($sqlData, $name);

    }

    public function dumpAllTables()
    {

        $link = $this->_connect();

        mysql_select_db(
            $this->getDbName(),
            $link
        );

        $tables = array();
        $result = mysql_query('SHOW TABLES');

        if ($result === false) {
            die('Error!!');
        }

        while ($row = mysql_fetch_row($result)) {
            $tables[] = $row[0];
        }

        $sqlData = '';
        foreach ($tables as $table) {
            $sqlData .= $this->_checkTable($table);
        }

        $this->_writeSql($sqlData, $this->getDbName());

    }

    protected function _connect()
    {

        $link = mysql_connect(
            $this->getHost(),
            $this->getUser(),
            $this->getPass()
        );

        return $link;

    }

    protected function _checkTable($table)
    {

        $sqlData = '';

        $result = mysql_query('SELECT * FROM ' . $table);
        $numFields = mysql_num_fields($result);

        $sqlData.= 'DROP TABLE IF EXISTS `' . $table . '`;';

        $createTable = mysql_fetch_row(
            mysql_query('SHOW CREATE TABLE ' . $table)
        );
        $sqlData.= "\n\n" . $createTable[1] . ";\n\n";

        if ($numFields > 0) {
            $sqlData.= "\nLOCK TABLES `" . $table . "` WRITE;\n\n";
        }

        for ($i = 0; $i < $numFields; $i++) {
            while ($row = mysql_fetch_row($result)) {

                $sqlData.= 'INSERT INTO `' . $table . '` VALUES(';

                for ($j=0; $j<$numFields; $j++) {
                    $row[$j] = addslashes($row[$j]);
                    $row[$j] = ereg_replace("\n", "\\n", $row[$j]);

                    if (isset($row[$j])) {
                        $sqlData.= '"'.$row[$j].'"' ;
                    } else {
                        $sqlData.= '""';
                    }

                    if ($j<($numFields-1)) {
                        $sqlData.= ',';
                    }
                }

                $sqlData.= ");\n";

            }
        }

        $sqlData.= "\n";

        if ($numFields > 0) {
            $sqlData.= "UNLOCK TABLES;\n";
        }

        $sqlData.= "\n\n\n";

        return $sqlData;

    }

    protected function _writeSql($sqlData, $name)
    {

        $tagTime = date('d-m-Y') . '-' .  time();
        $handle = fopen('db-backup-' . $name . '-' . $tagTime . '.sql', 'w+');
        fwrite($handle, $sqlData);
        fclose($handle);

    }

    public function setHost($host)
    {
        $this->_host = $host;
    }

    public function getHost()
    {
        return $this->_host;
    }

    public function setUser($user)
    {
        $this->_user = $user;
    }

    public function getUser()
    {
        return $this->_user;
    }

    public function setPass($pass)
    {
        $this->_pass = $pass;
    }

    public function getPass()
    {
        return $this->_pass;
    }

    public function setDbName($dbName)
    {
        $this->_dbName = $dbName;
    }

    public function getDbName()
    {
        return $this->_dbName;
    }

}