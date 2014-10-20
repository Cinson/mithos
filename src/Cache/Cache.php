<?php

namespace Mithos\Cache;

class Cache {

    private $_path = 'cache/';
    private $_name = 'default';
    private $_extension = '.cache';

    public function __construct($name = null) {
        if ($name != null) {
            $this->setCache($name);
        }
    }

    public function setPath($path) {
        $this->_path = $path;
        return $this;
    }

    public function getPath() {
        return $this->_path;
    }


    public function setCache($name) {
        $this->_name = $name;
        return $this;
    }

    public function getCache() {
        return $this->_name;
    }

    public function setExtension($ext) {
        $this->_extension = $ext;
        return $this;
    }

    public function getExtension() {
        return $this->_extension;
    }

    public function isCached($key) {
        if ($this->loadCache() != false) {
            $cachedData = $this->loadCache();
            return isset($cachedData[$key]['data']);
        }
    }


    public function store($key, $data, $expiration = 0) {
        $storeData = array(
            'time' => time(),
            'expire' => $expiration,
            'data' => serialize($data)
        );
        $dataArray = $this->loadCache();
        if (is_array($dataArray)) {
            $dataArray[$key] = $storeData;
        } else {
            $dataArray = array($key => $storeData);
        }
        $cacheData = json_encode($dataArray);
        file_put_contents($this->getCacheDir(), $cacheData);
        return $this;
    }

    public function retrieve($key, $timestamp = false) {
        $cachedData = $this->loadCache();
        $type = !$timestamp ? 'data' : 'time';
        if (!isset($cachedData[$key][$type])) {
            return null;
        }
        return unserialize($cachedData[$key][$type]);
    }

    public function retrieveAll($meta = false) {
        if (!$meta) {
            $results = array();
            $cachedData = $this->loadCache();
            if ($cachedData) {
                foreach ($cachedData as $k => $v) {
                    $results[$k] = unserialize($v['data']);
                }
            }
            return $results;
        } else {
            return $this->loadCache();
        }
    }

    public function erase($key) {
        $cacheData = $this->loadCache();
        if (is_array($cacheData)) {
            if (isset($cacheData[$key])) {
                unset($cacheData[$key]);
                $cacheData = json_encode($cacheData);
                file_put_contents($this->getCacheDir(), $cacheData);
            } else {
                throw new CacheEraseException('Erase key: ' . $key . ' not found.');
            }
        }
        return $this;
    }

    public function eraseExpired() {
        $cacheData = $this->loadCache();
        if (is_array($cacheData)) {
            $counter = 0;
            foreach ($cacheData as $key => $entry) {
                if ($this->checkExpired($entry['time'], $entry['expire'])) {
                    unset($cacheData[$key]);
                    $counter++;
                }
            }
            if ($counter > 0) {
                $cacheData = json_encode($cacheData);
                file_put_contents($this->getCacheDir(), $cacheData);
            }
            return $counter;
        }
    }

    public function eraseAll() {
        $cacheDir = $this->getCacheDir();
        if (file_exists($cacheDir)) {
            $cacheFile = fopen($cacheDir, 'w');
            fclose($cacheFile);
        }
        return $this;
    }

    private function loadCache() {
        if (file_exists($this->getCacheDir())) {
            $file = file_get_contents($this->getCacheDir());
            return json_decode($file, true);
        } else {
            return false;
        }
    }

    public function getCacheDir() {
        if ($this->checkCacheDir()) {
            $filename = $this->getCache();
            $filename = preg_replace('/[^0-9a-z\.\_\-]/i', '', strtolower($filename));
            return $this->getPath() . $this->getHash($filename) . $this->getExtension();
        }
    }

    private function getHash($filename) {
        return sha1($filename);
    }


    private function checkExpired($timestamp, $expiration) {
        $result = false;
        if ($expiration !== 0) {
            $timeDiff = time() - $timestamp;
            $result = $timeDiff > $expiration;
        }
        return $result;
    }

    private function checkCacheDir() {
        if (!is_dir($this->getPath()) && !mkdir($this->getPath(), 0775, true)) {
            throw new \RuntimeException('Unable to create cache directory ' . $this->getPath());
        } elseif (!is_readable($this->getPath()) || !is_writable($this->getPath())) {
            if (!chmod($this->getPath(), 0775)) {
                throw new \RuntimeException($this->getPath() . ' must be readable and writeable');
            }
        }
        return true;
    }

}
