<?php

namespace Mithos\Slim;

class AssetMiddleware extends \Slim\Middleware {

    public function call() {
        $request = $this->app->request();
        $url = urldecode($request->getResourceUri());
        if (strpos($url, '..') !== false || strpos($url, '.') === false) {
            $this->next->call();
            return null;
        }
        $assetFile = $this->_getAssetFile($url);
        if ($assetFile === null || !file_exists($assetFile)) {
            $this->next->call();
            return null;
        }
        $response = $this->app->response();
        $pathinfo = pathinfo($assetFile);

        header("Content-type: " . MimeType::getMimeType($pathinfo['extension']));

        $since = filemtime($assetFile);
//        $this->app->lastModified($since);
//        $this->app->expires('+1 day');

        $response->body(readfile($assetFile));
    }

    protected function _getAssetFile($url) {
        $parts = array_filter(explode('/', $url), 'strlen');
        if ($parts[1] === 'template') {
            $templateName = $parts[2];
            unset($parts[1], $parts[2]);
            $fileFragment = implode(DS, $parts);
            $path = TEMPLATES_PATH . $templateName . DS . 'public' . DS;
            return $path . $fileFragment;
        } else if ($parts['1'] === 'plugin') {
            $pluginName = $parts[2];
            unset($parts[1], $parts[2]);
            $fileFragment = implode(DS, $parts);
            $path = PLUGINS_PATH . $pluginName . DS . 'public' . DS;
            return $path . $fileFragment;
        }
    }
}