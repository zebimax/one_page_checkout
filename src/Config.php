<?php
/**
 * Created by PhpStorm.
 * User: Zebimax
 * Date: 12.02.15
 * Time: 19:51
 */

class Config implements ConfigInterface
{
    private $data = [];

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->data = $config;
    }

    /**
     * @param $key
     * @param null $default
     * @return null
     */
    public function get($key, $default = null)
    {
        $result = $default;
        if (isset($this->data[$key])) {
            $result = $this->data[$key];
        }
        return $result;
    }
}