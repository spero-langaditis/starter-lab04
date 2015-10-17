<?php

/**
 * Data access wrapper for "orders" table.
 *
 * @author jim
 */
class Orders extends MY_Model {

    // constructor
    function __construct() {
        parent::__construct('orders', 'num');
    }

    // add an item to an order
    function add_item($num, $code) {
        //Checks if the item is in the order then updates
        //quantity, if its not it add item and set the quantity
        //to 1
        $CI = & get_instance();
        if ($CI->orderitems->get($num, $code)) {
            $record = $CI->orderitems->get($num, $code);
            $record->quantity++;
            $CI->orderitems->update($record);
        } else {
            $record = $CI->orderitems->create();
            $record->order = $num;
            $record->item = $code;
            $record->quantity = 1;
            $CI->orderitems->add($record);
        }
    }

    // calculate the total for an order
    function total($num) {
        $CI = & get_instance();
        $items = $CI->orderitems->group($num);
        $result = 0;
        if (count($items) > 0) {
            foreach ($items as $item) {
                $menu = $CI->menu->get($item->item);
                $result += $item->quantity * $menu->price;
            }
        }
        return $result;
    }

    // retrieve the details for an order
    function details($num) {
        
    }

    // cancel an order
    function flush($num) {
        
    }

    // validate an order
    // it must have at least one item from each category
    function validate($num) {
        //Order is valid if at least there is one item from each 
        //category.
        $CI = & get_instance();
        $items = $CI->orderitems->group($num);
        $gotem = array();
        if (count($items) > 0) {
            foreach ($items as $item) {
                $menu = $CI->menu->get($item->item);
                $gotem[$menu->category] = 1;
            }
        }
        return isset($gotem['m']) && isset($gotem['d']) && isset($gotem['s']);
    }

}
