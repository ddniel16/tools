# Tools

* fullReplace.php
 - Busca en todos los contenidos del directorio asignado en setPathProtect y cambia los parametros exactos.

````php

$replace = new FullReplace();
$replace->setPathProtect('/path/');
$replace->setStringReplace('currentString');
$replace->setNewString('newString');
$replace->start();

````

* LogMaster.php
 - Sistema de log's en php, con las opciones de registrarlos en syslog o en un archivos custom, con las diferentes prioridades. En las ejecuciones por consola, con colores identificativos para cada tipo.

````php

$opt = array(
    'logFile' => '/tmp/logMaster.log',
    'syslog' => true,
    'syslogTag' => '[app]'
);

$logs = new LogsManager($opt);

echo $logs->info('info');
echo $logs->error('error');
echo $logs->warning('warning');
echo $logs->success('success');
echo $logs->debug('debug');

echo $logs->custom(array('custom'), 'blue', 'default');

````

* mysqlDump.php
 - Mini script para crear dumps de mysql, ya sea de toda una base de datos, varias tablas o una en concreto.
````php
$config = array(
    'host' => 'localhost',
    'user' => 'root',
    'pass' => '1234',
    'dbname' => 'Testing'
);

$dump = new MysqlDump($config);

$dump->dumpAllTables();
$dump->dumpTables(array('Authors', 'Banners'));
$dump->dumpTable('Authors');
````