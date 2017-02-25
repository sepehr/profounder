<?php

namespace Profounder\Foundation\Support;

class Utils
{
    /**
     * Converts a price string to an equivalent integer.
     *
     * @param  string|null $price
     *
     * @return int|null
     */
    public static function preparePrice($price)
    {
        return empty($price)
            ? $price
            : intval(preg_replace('/([^0-9\\.])/i', '', $price) * 100);
    }

    /**
     * Reformats a valid date string.
     *
     * @param  string $date
     * @param  string $format
     *
     * @return string
     */
    public static function reformatDate($date, $format = 'Y-m-d H:i:s')
    {
        return date($format, strtotime($date));
    }

    /**
     * Strips HTML tags from string.
     *
     * @param  string $string
     *
     * @return string
     */
    public static function stripTags($string)
    {
        return strip_tags($string);
    }

    /**
     * Normalizes whitespace characters in a string.
     *
     * @param  string $string
     *
     * @return string
     */
    public static function normalizeWhitespace($string)
    {
        return trim(preg_replace('!\s+!', ' ', str_replace("\xC2\xA0", ' ', $string)));
    }
}
