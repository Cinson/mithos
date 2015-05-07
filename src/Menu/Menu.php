<?php
    
namespace Mithos\Menu;
    
use Mithos\Util\Hash;
use Mithos\Admin\Auth;

class Menu {
    
    private static $instance = [];
    private $items = [];

    public static function getInstance($instance = 'default') {
        if (!isset(static::$instance[$instance])) {
            static::$instance[$instance] = new self();
        }
        return static::$instance[$instance];
    }
    
	public function add($item) {
        $path = explode('.', $item['id']);
        $parent = null;
        if (count($path) > 1) {
            array_pop($path);
            $parent = join('.', $path);
        }
        $item['parent_id'] = $parent;
        if (!isset($item['sequence'])) {
            $item['sequence'] = 0;
        }
        $this->items[$item['id']] = $item;
        return $this;
	}
    
    public function getMenus() {
        return $this->items;
    }
    
    public function getFormattedItems() {
        $items = Hash::sort($this->items, '{s}.sequence', 'asc');
        return Hash::nest($items);
    }
    
    public function getItems() {
        return $this->items;
    }
    
	public function render(array $items = null, $level = 0) {
		$items = $items === null ? $this->getFormattedItems() : $items;
        $menu = '';
        $class = '';
        if ($level == 1) {
            $class = 'sublist';
        } elseif ($level > 1) {
            $class = 'sublist-menu';
            $menu .= '<span class="arrow"></span>';
        }
		$menu .= '<ul class="' . $class . '">';
        $user = Auth::getUser();
        $access = explode(',', $user['access']);

		foreach ($items as $key => $item) {
			$hasChildren = !empty($item['children']);
			$class = array();
			$hasChildren ? $class[] = 'parent' : null;

            $width = isset($item['window']) && isset($item['window']['width']) ? $item['window']['width'] : 600;
            $height = isset($item['window']) && isset($item['window']['height']) ? $item['window']['height'] : 400;
            $title = isset($item['window']) && isset($item['window']['title']) ? $item['window']['title'] : $item['title'];

			$classes = ! empty($class) ? static::attributes(['class' => implode(' ', $class)]) : null;
            if ($hasChildren) {
                $menu .= '<li' . $classes . '><a title="' . $item['title'] . '" href="#">' . $item['title'] . '</a>';
            } else {
                if ($item['title'] == '-') {
                    $menu .= '<li class="divider"></li>';
                } else {
                    $menu .= '<li' . $classes . '><a data-window data-window-title="' . $title . '" data-window-width="' . $width . '" data-window-height="' . $height . '" data-name="' . $item['id'] . '" title="' . $item['title'] . '" href="' . dirname($_SERVER['PHP_SELF']) . $item['url'] . '">' . $item['title'] . '</a>';
                }
            }
			$menu .= $hasChildren ? $this->render($item['children'], $level + 1) : null;
			$menu .= '</li>';
		}
		$menu .= '</ul>';
		return $menu;
	}
    
	protected static function attributes($attrs) {
		if (empty($attrs)) {
            return '';
        }

		if (is_string($attrs)) {
			return ' ' . $attrs;
        }

		$compiled = '';
		foreach ($attrs as $key => $val) {
			$compiled .= ' ' . $key . '="' . htmlspecialchars($val) . '"';
		}

		return $compiled;
	}
}