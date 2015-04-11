<?php

namespace Mithos\Slim;

class View extends \Slim\View {

    const DEFAULT_LAYOUT = 'default';
    const LAYOUT_KEY = 'layout';
    const YIELD_KEY = 'content';

    private $defaultDir = null;
    private $blocks = null;
    private $app = null;

    public function __construct() {
        parent::__construct();
        $this->blocks = new ViewBlock();
        $this->app = Application::getInstance();
    }

    public function remove($key) {
        $this->data->remove($key);
    }

    public function add($key, $value = []) {
        $data = $this->data->get($key);
        if ($data == null) {
            $data = [];
        }
        $this->data->set($key, array_merge($data, $value));
    }

    public function block() {
        return $this->block;
    }

    public function fetch($template, $data = null) {
        if ($this->defaultDir === null) {
            $this->defaultDir = $this->templatesDirectory;
        }

        $layout = $this->getLayout($data);

        if (substr($template, 0, 1) == '/') {
            $parts = explode('/', $template);
            $plugin = $parts[1];
            $template = join('/', array_slice($parts, 2));
            $this->templatesDirectory = $this->app->config('plugins.path') . DS . $plugin . DS . 'src' . DS . ($this->app->inAdmin() ? 'admin' . DS : '') . 'views' . DS;
        } else {
            $this->templatesDirectory = $this->defaultDir;
        }

        $result = $this->render($template . '.php', $data);

        if (!Application::getInstance()->request()->isAjax() && is_string($layout)) {
            $this->templatesDirectory = $this->defaultDir;
            $result = $this->renderLayout($layout, $result, $data);
        }
        return $result;
    }

    public function getLayout($data = null) {
        $layout = null;

        if (is_array($data) && array_key_exists(self::LAYOUT_KEY, $data)) {
            $layout = $data[self::LAYOUT_KEY];
            unset($data[self::LAYOUT_KEY]);
        }

        if ($this->has(self::LAYOUT_KEY)) {
            $layout = $this->get(self::LAYOUT_KEY);
            $this->remove(self::LAYOUT_KEY);
        }

        if (is_null($layout)) {
            $layout = $this->app->config(self::LAYOUT_KEY);
        }

        if (is_null($layout)) {
            $layout = self::DEFAULT_LAYOUT;
        }

        if ($layout == null) {
            return $layout;
        } else {
            return 'layouts/' . $layout . '.php';
        }
    }

    protected function renderLayout($layout, $yield, $data = null) {
        if (!is_array($data)) {
            $data = [];
        }
        $data[self::YIELD_KEY] = $yield;
        $currentTemplate = $this->templatesDirectory;
        $result = $this->render($layout, $data);
        $this->templatesDirectory = $currentTemplate;
        return $result;
    }
}