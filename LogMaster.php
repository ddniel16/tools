<?php

/**
 * Sistemas de log's en php, con las opciones de registrarlos en syslog o en un archivos custom,
 * con las diferentes prioridades y en el caso de los por consola, con colores identificativos para cada
 * tipo.
 *
 * @author ddniel16 <ddniel16@gmail.com>
 */
class LogsManager
{

    protected $_options;

    protected $_default = "\033[0m";

    private $_fontColors = array(
        'default' => 39,
        'white' => 97,
        'black' => 30,
        'red' => 31,
        'green' => 32,
        'yellow' => 33,
        'blue' => 34,
        'purple' => 35,
        'cyan' => 36,
        'lightRed' => 91,
        'lightGreen' => 92,
        'lightYellow' => 93,
        'lightBlue' => 94,
        'lightPurple' => 95,
        'lightCyan' => 96
    );

    private $_backgroundColors = array(
        'default' => 49,
        'white' => 107,
        'black' => 40,
        'red' => 41,
        'green' => 42,
        'yellow' => 43,
        'blue' => 44,
        'purple' => 45,
        'cyan' => 46,
        'lightRed' => 101,
        'lightGreen' => 102,
        'lightYellow' => 103,
        'lightBlue' => 104,
        'lightPurple' => 105,
        'lightCyan' => 106
    );

    public function __construct ($options = array())
    {

        $default = array(
            'syslog' => false,
            'fileActive' => true,
            'fileOpts' => array(
                'logDir' => sys_get_temp_dir(),
                'name' => 'custom',
                'ext' => '.log',
                'dateFormat' => 'd-m-Y H:i:s P',
                'maxLogs' => 10,
                'maxSize' => 123123154,
            )
        );

        $this->_options = array_merge($default, $options);

    }

    public function debug($log, $priority = LOG_DEBUG)
    {

        if ($this->_options['syslog']) {
            $this->_syslog($log, $priority);
        }

        if ($this->_options['fileActive']) {
            $this->_customFile($log, $priority);
        }

        return "\033[39m" . print_r($log, true) . $this->_default . PHP_EOL;

    }

    public function info($log, $priority = LOG_INFO)
    {

        if ($this->_options['syslog']) {
            $this->_syslog($log, $priority);
        }

        if ($this->_options['fileActive']) {
            $this->_customFile($log, $priority);
        }

        return "\033[96m" . print_r($log, true) . $this->_default . PHP_EOL;

    }

    public function warning($log, $priority = LOG_WARNING)
    {

        if ($this->_options['syslog']) {
            $this->_syslog($log, $priority);
        }

        if ($this->_options['fileActive']) {
            $this->_customFile($log, $priority);
        }

        return "\033[93m" . print_r($log, true) . $this->_default . PHP_EOL;

    }

    public function success($log, $priority = LOG_DEBUG)
    {

        if ($this->_options['syslog']) {
            $this->_syslog($log, $priority);
        }

        if ($this->_options['fileActive']) {
            $this->_customFile($log, $priority);
        }

        return "\033[92m" . print_r($log, true) . $this->_default . PHP_EOL;

    }

    public function error($log, $priority = LOG_ERR)
    {

        if ($this->_options['syslog']) {
            $this->_syslog($log, $priority);
        }

        if ($this->_options['fileActive']) {
            $this->_customFile($log, $priority);
        }

        return "\033[91m" . print_r($log, true) . $this->_default . PHP_EOL;

    }

    public function fatal()
    {
        //error_log("Problema serio, nos hemos quedado sin FOOs!", 1, "lol@example.com");
    }

    public function custom($log, $fontColor = 39, $backgroundColor = 49)
    {

        $color = $this->_checkFontColor($fontColor);
        $background = $this->_checkBackgroundColor($backgroundColor);

        if ($this->_options['syslog']) {
            $this->_syslog($log, LOG_ERR);
        }

        if ($this->_options['fileActive']) {
            $this->_customFile($log);
        }

        $back = "\033[" . $background . "m";
        $font = "\033[" . $color . "m";
        $content = print_r($log, true);

        return $back . $font . $content . $this->_default . PHP_EOL;

    }

    protected function _checkFontColor($fontColor)
    {

        if (gettype($fontColor) === 'integer') {

            $values = array_values($this->_fontColors);

            if (array_search($fontColor, $values) !== false) {
                return $fontColor;
            }

        } elseif (gettype($fontColor) === 'string') {

            if (array_key_exists($fontColor, $this->_fontColors)) {
                return $this->_fontColors[$fontColor];
            }

        }

        return 39;

    }

    protected function _checkBackgroundColor($backgroundColor)
    {

        if (gettype($backgroundColor) === 'integer') {

            $values = array_values($this->_backgroundColors);

            if (array_search($backgroundColor, $values) !== false) {
                return $backgroundColor;
            }

        } elseif (gettype($backgroundColor) === 'string') {

            if (array_key_exists($backgroundColor, $this->_backgroundColors)) {
                return $this->_backgroundColors[$backgroundColor];
            }

        }

        return 49;

    }

    /**
     *
     * @param String $message
     */
    protected function _customFile($message, $priority = LOG_DEBUG)
    {

        $logOpts = $this->_options['fileOpts'];

        $logMaxSize = $logOpts['maxSize'];
        if (!is_numeric($logMaxSize)) {
            $logMaxSize = 104857600;
        }

        $logName = $logOpts['name']  . '.' . $logOpts['ext'];
        $logFile = $logOpts['logDir'] . '/' . $logName;

        if (!file_exists($logFile)) {
            $log = fopen($logFile, 'w');
            fclose($log);
        }

        $fileSize = filesize($logFile);

        if ($fileSize > $logMaxSize) {

            $pathInfo = pathinfo($logFile);
            $path = realpath($pathInfo['dirname']);
            $newName = $pathInfo['filename'] . '2.' . $pathInfo['extension'];

            rename($logFile, $path . '/' . $newName);

            $log = fopen($logFile, 'w');
            fclose($log);

        }

        $date = date($logOpts['dateFormat']);

        file_put_contents($logFile, LOG_DEBUG . $date . ': ' . print_r($message, true) . PHP_EOL, FILE_APPEND | LOCK_EX);

    }

    /**
     *
     * @param String $message
     * @param Constante $priority
     */
    protected function _syslog($message, $priority = LOG_DEBUG)
    {

        $syslogTag = $this->_options['syslog'];

        if ($syslogTag) {
            openlog($syslogTag, LOG_NDELAY | LOG_PID, LOG_LOCAL0);
        }

        syslog($priority, print_r($message, true));

    }

    protected function _fileSizeConvert ($bytes)
    {

        $bytes = floatval($bytes);
        $arBytes = array(
            0 => array(
                'unit' => 'TB',
                'value' => pow(1024, 4)
            ),
            1 => array(
                'unit' => 'GB',
                'value' => pow(1024, 3)
            ),
            2 => array(
                'unit' => 'MB',
                'value' => pow(1024, 2)
            ),
            3 => array(
                'unit' => 'KB',
                'value' => 1024
            ),
            4 => array(
                'unit' => 'B',
                'value' => 1
            )
        );

        foreach ($arBytes as $arItem) {
            if ($bytes >= $arItem['value']) {
                $result = $bytes / $arItem['value'];
                $result = str_replace('.', ',', strval(round($result, 2))) . ' ' . $arItem['unit'];
                break;
            }
        }

        return $result;

    }

}