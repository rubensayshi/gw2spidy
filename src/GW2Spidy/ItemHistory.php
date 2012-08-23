<?php

namespace GW2Spidy;

use GW2Spidy\DB\ItemQuery;

use GW2Spidy\DB\Item;

use \Exception;

class ItemHistory {
    protected $history = array();

    protected static $instance;

    protected function __construct() {}

    public static function getInstance() {
        if (is_null(self::$instance)) {
            if (!isset($_SESSION['item_history'])) {
                $_SESSION['item_history'] = new self();
            }

            self::$instance = $_SESSION['item_history'];
        }

        return self::$instance;
    }

    public function addItem(Item $item) {
        if (($k = array_search($item->getDataId(), $this->history)) !== false) {
            unset($this->history[$k]);
        }

        $this->history[] = $item->getDataId();
    }

    public function getItems() {
        $ids   = array_slice(array_reverse($this->history), 0, 10);
        $items = array();

        foreach ($ids as $id) {
            $items[] = ItemQuery::create()->findPk($id);
        }

        return $items;
    }
}

?>
