<?php

namespace common;

class Component {

    protected static $app;

    public function setContainer($app) {
        static::$app = $app;
    }

    protected function get($name) {
        // 优先加载配置文件中的注入
        if (isset(static::$app[$name])) {
            return static::$app->$name;
        }
        // 自动注入用户服务
        $className = 'services\\' . ucfirst($name);
        $service = new $className;
        $this->set($name, $service);
        return $service;
    }

    protected function set($name, $service) {
        static::$app[$name] = $service;
    }

}