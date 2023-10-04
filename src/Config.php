<?php
namespace Simcify;

class Config {

    /**
     * Cache all config files and return a combined array
     * 
     * @return  void
     */
    public static function cache() {
        $dir = str_replace("src", "config", __DIR__);
        $config = array();
        foreach(array_diff(scandir($dir), ['.', '..']) as $filename) {
            $config[str_replace('.php', '', $filename)] = include $dir."/{$filename}";
        }
        return $config;
    }
    
    /**
     * Get a configuration
     * 
     * @param   string $key
     * @return  mixed
     */
    public static function get($key) {
        $dot_keys = explode('.', $key);
        $config = container('config');
        foreach($dot_keys as $dot_key) {
            $config = $config[$dot_key];
        }
        return $config;
    }

    
    /**
     * Set a configuration at runtime
     * 
     * @param   string $key
     * @return  void
     */
    public static function set($key, $value) {
        $config = container('config');
        $dot_keys = explode('.', $key);
        $branch = $config;
        while(count($dot_keys) > 1) {
            $branch = $branch[$dot_keys[0]];
            array_splice($dot_keys, 0, 1);
        }
        $new_branch = $branch;
        $new_branch[$dot_keys[0]] = $value;
        $new_config = json_decode(str_replace(
            json_encode($branch, JSON_NUMERIC_CHECK),
            json_encode($new_branch, JSON_NUMERIC_CHECK),
            json_encode($config, JSON_NUMERIC_CHECK)
        ), true);
        container('config', $new_config);
    }
}
