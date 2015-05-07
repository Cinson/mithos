<?php

namespace Mithos\Slim;

use Mithos\Core\Plugin;
use Mithos\Core\Config;
use Mithos\Util\Hash;

class View extends \Slim\View {

    const DEFAULT_LAYOUT = 'default';
    const LAYOUT_KEY = 'layout';
    const YIELD_KEY = 'content';

    private $_defaultDir = null;

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

    public function fetch($template, $data = null) {
        $app = Application::getInstance();

        if ($this->_defaultDir === null) {
            $this->_defaultDir = $this->templatesDirectory;
        }

        $layout = $this->getLayout($data);

        if (strpos($template, '.') !== false) {
            $parts = explode('.', $template);
            $plugin = $parts[0];
            $active = Plugin::isActive($plugin);
            if ($active) {
                $template = join('.', array_slice($parts, 1));
                $this->templatesDirectory = Config::get('path.plugins') . DS . $plugin . DS . 'src' . DS . ($app->inAdmin() ? 'admin' . DS : '') . 'views' . DS;
            }
        } else {
            $this->templatesDirectory = $this->_defaultDir;
        }

        $result = $this->render($template . '.php', $data);

        if (!$app->request()->isAjax() && is_string($layout)) {
            $this->templatesDirectory = $this->_defaultDir;
            $result = $this->renderLayout($layout, $result, $data);
        }
        return $result;
    }

    public function service($template, $root, $data = []) {
        $account = \Mithos\Account\Auth::getAccount();

        if ($account !== null) {
            $avaliables = Hash::nest($account->getAvaliableServices());
            $services = [];

            foreach ($avaliables as $avaliable) {
                if ($avaliable['service'] == $root) {
                    $services = $avaliable['children'];
                }
            }

            return View::display('panel/view', [
                'service' => View::fetch($template, array_merge(['layout' => false], $data)),
                'account' => \Mithos\Account\Auth::getAccount(),
                'services' => $services
            ]);
        }
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
            $app = Application::getInstance();
            $layout = $app->config(self::LAYOUT_KEY);
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