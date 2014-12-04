<?php

namespace Mithos\Slim;

class View extends \Slim\View {
    private $layout = 'layouts/default';

    public function layout($layout) {
        $this->layout = 'layouts/' . $layout;
        return $this;
    }

    public function set($key, $value = null) {
        parent::set($key, $value);
        return $this;
    }

    public function display($template, $data = array()) {
        echo $this->fetch($template, $data);
    }

    public function fetch($template, $data = array()) {
        return $this->render($template, $data);
    }

    public function render($template, $data = array()) {
        $template = $template . '.php';
        if ($this->layout) {
            $dir = $this->getTemplatesDirectory();
            if (substr($template, 0, 1) == '/') {
                $parts = explode('/', $template);
                $plugin = $parts[1];
                $template = join('/', array_slice($parts, 2));

                $this->setTemplatesDirectory(PLUGINS_PATH . DS . $plugin . DS . 'views' . DS);
            }
            if (Application::getInstance()->request()->isAjax() || !$this->layout) {
                return parent::render($template);
            } else {
                $content = parent::render($template);

                $this->data->set('content', $content);

                $template = $this->layout . '.php';
                $this->layout = null;
                $this->setTemplatesDirectory($dir);
                return parent::render($template);
            }
        } else {
            return parent::render($template);
        }
    }
}