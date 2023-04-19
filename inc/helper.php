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
    static function time_elapsed_string($datetime, $full = false)
    {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => __('year','atl'),
            'm' => __('month','atl'),
            'w' => __('week','atl'),
            'd' => __('day','atl'),
            'h' => __('hours','atl'),
            'i' => __('min','atl'),
            's' => __('sec','atl'),
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? '' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . __(' aog ','atl') :__('just now','atl') ;
    }
    static function get_status_label($status){
        switch ($status){
            case 'pending':
                return __('Pending','atl');
                case 'done':
                return __('Done','atl');
            default:
                return __('Unknown','atl');
        }
    }

}