<?php
namespace Kanonji\EditorConfig;

class EditorConfigFile{
    protected $map = [
        'config' => ['json', 'yaml'],
        'unity' => ['csharp', 'config'],
        'web' => ['html', 'css', 'javascript', 'config'],
        'php-web' => ['php', 'web'],
    ];
    protected $initialKey;
    protected $list = [];
    protected $errors = [];

    public function __construct($key){
        $this->initialKey = $key;
    }

    public function generate(){
        $content = $this->loadFile('root');
        if(false === $this->pick($this->initialKey)){
            throw new \RuntimeException();
        }
        foreach($this->list as $key){
            $content .= PHP_EOL;
            $content .= $this->loadFile($key);
        }
        return $content;
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

    protected function loadFile($key){
        if(false === $content = file_get_contents("{$key}.editorconfig")){
            throw new \RuntimeException();
        }
        return $content;
    }
}

$key = filter_input(INPUT_GET, 'key');
if(empty($key)) return;

$isMatched = preg_match('/^[-_a-z0-9]+$/', $key);
if(empty($isMatched)) {
    throw new \RuntimeException();
}

$editorconfig = (new EditorConfigFile($key))->generate();
echo $editorconfig;
