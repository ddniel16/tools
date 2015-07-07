# Tools

* fullReplace.php
 - Busca en todos los contenidos del directorio asignado en setPathProtect y cambia los parametros exactos.

* LogMaster.php
 - Sistemas de log's en php, con las opciones de registrarlos en syslog o en un archivos custom, con las diferentes prioridades. En las ejecuciones por consola, con colores identificativos para cada tipo.
 
 Ejemplo:
````
<?php

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
