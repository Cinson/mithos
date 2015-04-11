<?php

namespace Mithos\Slim;

use Mithos\Account\Auth;
use Mithos\Core\Config;

class Application extends \Slim\Slim {

    public function redirect($url, $status = 302) {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            parent::redirect((Config::get('url_rewrite', true) ? '' : '/index.php') . $url, $status);
        } else {
            parent::redirect($url, $status);
        }
    }

//    public function render($template, $data = array(), $status = null) {
//        $this->view->setTemplatesDirectory($this->config('templates.path'));
//        echo $this->view->render($template, $data, $status);
//    }

    public function fetch($template, $layout = true) {
        $this->view->setTemplatesDirectory($this->config('templates.path'));
        return $this->view->fetch($template, $layout);
    }

    public function set($key, $value = null) {
        $this->view->set($key, $value);
        return $this;
    }

//    public function layout($layout) {
//        $this->view->layout($layout);
//        return $this;
//    }

    public function inAdmin() {
        return strpos($this->request()->getResourceUri(), '/admin') !== false;
    }

    public static function requireAuth() {
        $instance = self::getInstance();

        return function () use ($instance) {
            if (!Auth::loggedIn()) {
                error(__('Você deve estar logado para acessa está página.'), ['redirect' => '/']);
            }
        };
    }

}