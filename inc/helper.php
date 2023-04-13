<?php
if (!defined('ABSPATH')) {
    exit;
}
class ATL_Helper{
    static function update_or_create_option($option, $value)
    {
        if (get_option($option)) {
            update_option($option, $value);
        } else {
            add_option($option, $value);
        }
    }
}