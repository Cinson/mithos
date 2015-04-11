<?php

namespace Mithos\Slim;

class ViewBlock {

    const APPEND = 'append';
    const PREPEND = 'prepend';
    protected $blocks = [];
    protected $active = [];
    protected $discardActiveBufferOnEnd = false;

    public function start($name) {
        if (in_array($name, $this->active)) {
            throw new Exception(sprintf("A view block with the name '%s' is already/still open.", $name));
        }
        $this->active[] = $name;
        ob_start();
    }

    public function end() {
        if ($this->discardActiveBufferOnEnd) {
            $this->discardActiveBufferOnEnd = false;
            ob_end_clean();
            return;
        }
        if (!empty($this->active)) {
            $active = end($this->active);
            $content = ob_get_clean();
            $this->blocks[$active] = $content;
            array_pop($this->active);
        }
    }

    public function concat($name, $value, $mode = ViewBlock::APPEND) {
        if (!isset($this->blocks[$name])) {
            $this->blocks[$name] = '';
        }
        if ($mode === ViewBlock::PREPEND) {
            $this->blocks[$name] = $value . $this->blocks[$name];
        } else {
            $this->blocks[$name] .= $value;
        }
    }

    public function set($name, $value) {
        $this->blocks[$name] = (string)$value;
    }

    public function get($name, $default = '') {
        if (!isset($this->blocks[$name])) {
            return $default;
        }
        return $this->blocks[$name];
    }

    public function exists($name) {
        return isset($this->blocks[$name]);
    }

    public function keys() {
        return array_keys($this->blocks);
    }

    public function active() {
        return end($this->active);
    }

    public function unclosed() {
        return $this->active;
    }
}
