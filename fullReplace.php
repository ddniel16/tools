<?php
/**
 * Busca en todos los contenidos del directorio asignado en setPathProtect y cambia los parametros exactos.
 * ddniel16@gmail.com
 */
class FullReplace
{
    protected $_pathProyect;
    protected $_stringReplace;
    protected $_newString;

    public function start()
    {

        echo "\n";
        $this->_checkData();
        $this->_examineFolders($this->_pathProyect);
        echo "\n";

    }

    protected function _examineFolders($folder)
    {

        $proyectDir = new DirectoryIterator($folder);

        $blackList = array(
            '.',
            '..'
        );

        foreach ($proyectDir as $item) {
            if (is_dir($item->getPathname())) {
                if (!in_array($item->getBasename(), $blackList)) {

                    $this->_examineFolders($item->getPathname());

                    if ($item->getBasename() === $this->_stringReplace) {

                        $newPath = str_replace(
                            $this->_stringReplace,
                            $this->_newString,
                            $item->getPathname()
                        );

                        rename($item->getPathname(), $newPath);
                    }


                }
            } else {

                if (is_file($item->getPathname())) {

                    $content = file_get_contents($item->getPathname());
                    if (strpos($content, $this->_stringReplace) !== false) {

                        $content = str_replace(
                            $this->_stringReplace,
                            $this->_newString,
                            $content
                        );

                        file_put_contents($item->getPathname(), $content);

                        echo ".";

                    }
                }
            }
        }

    }

    /**
     * Comprueba que todos los parametros existan y sean validos.
     * @throws \Exception
     */
    protected function _checkData()
    {

        if (empty($this->getPathProyect())) {
            throw new \Exception(
                'Path Proyect is required'
            );
        }

        if (!is_dir($this->getPathProyect())) {
            throw new \Exception(
                'Path Proyect not is folder'
            );
        }

        if (empty($this->getNewString())) {
            throw new \Exception(
                'NewString is required'
            );
        }

        if (empty($this->getStringReplace())) {
            throw new \Exception(
                'StringReplace is required'
            );
        }

    }

    public function setPathProtect($pathProyect)
    {
        $this->_pathProyect = $pathProyect;
        return $pathProyect;
    }

    public function getPathProyect()
    {
        return $this->_pathProyect;
    }

    public function setStringReplace($stringReplace)
    {
        $this->_stringReplace = $stringReplace;
        return $stringReplace;
    }

    public function getStringReplace()
    {
        return $this->_stringReplace;
    }

    public function setNewString($newString)
    {
        $this->_newString = $newString;
        return $newString;
    }

    public function getNewString()
    {
        return $this->_newString;
    }

}

$replace = new FullReplace();
$replace->setPathProtect({path});
$replace->setStringReplace({currentString});
$replace->setNewString({newString});

$replace->start();
