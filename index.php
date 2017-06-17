<?php
namespace Kanonji\EditorConfig;

class EditorConfigFile{
    protected $configFileMap = [
        'config' => ['json', 'yaml'],
        'unity' => ['csharp', 'config'],
        'web' => ['html', 'css', 'javascript', 'config'],
        'php-web' => ['php', 'web'],
    ];
    protected $configName;

    public function __construct(ConfigName $configName){
        $this->configName = $configName;
        $this->configFileMap = new ConfigFileMap($this->configFileMap);
    }

    public function generate(){
        $content = $this->loadFile('root');

        $keys = $this->configFileMap->getFileKeys($this->configName);
        foreach($keys as $key){
            $content .= PHP_EOL;
            $content .= $this->loadFile($key);
        }
        return $content;
    }

    protected function loadFile($key){
        if(false === $content = file_get_contents("{$key}.editorconfig")){
            throw new \RuntimeException("{$key}.editorconfig is not found.");
        }
        return $content;
    }
}

class ConfigFileMap{
    protected $map;
    protected $list = [];
    protected $errors = [];

    public function __construct($configMap){
        $this->map = $configMap;
    }

    public function getFileKeys(ConfigName $configName){
        $initialKey = (string)$configName;
        if(false === $this->isValidKey($initialKey)){
            throw new \RuntimeException("`{$initialKey}` is wrong config name.");
        }

        if(false === $this->pick($initialKey)) {
            $errorsString = join($this->errors, ', ');
            throw new \RuntimeException("Key or file not exists.: {$errorsString}");
        }
        return $this->list;
    }

    protected function pick($keys){
        foreach((array)$keys as $key){
            if(false === $result = $this->isValidKey($key)) $this->errors[] = $key;
            if(is_array($result)) {
                $this->pick($result);
            }
            if(true === $result) {
                $this->list[] = $key;
            }
        }
        return empty($this->errors);
    }

    protected function isValidKey($key){
        if(empty($this->map[$key])){
            return file_exists("{$key}.editorconfig");
        }
        return $this->map[$key];
    }
}

class ConfigName{
    protected $name;

    public function __construct($name){
        $this->name = $name;
        if(false === $this->isValid()){
            throw new \RuntimeException('Invalid config name.');
        }
    }

    protected function isValid(){
        return (bool)preg_match('/^[-_a-z0-9]+$/', $this->name);
    }

    public function __toString(){
        return (string)$this->name;
    }
}

class Response{
    const OK = 200;
    const BAD_REQUEST = 400;

    protected $body;
    protected $statusCode;

    public function __construct($body, $statusCode){
        $this->body = $body;
        $this->statusCode = $statusCode;
    }

    public function output(){
        switch($this->statusCode){
            case self::BAD_REQUEST:
                $this->prepare400();
                break;
            default:
                $this->prepare200();
        }
        echo $this->body;
    }

    protected function prepare200(){
    }

    protected function prepare400(){
        header("HTTP/1.0 400 Bad Request");
    }
}

try{
    $key = filter_input(INPUT_GET, 'key');
    if(empty($key)) return;

    $configName = new ConfigName($key);
    $editorconfigFile = new EditorConfigFile($configName);
    $response = new Response($editorconfigFile->generate(), Response::OK);
} catch(\Exception $e) {
    $response = new Response($e->getMessage(), Response::BAD_REQUEST);
}
$response->output();
