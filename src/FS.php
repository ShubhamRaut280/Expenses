<?php
namespace Simcify;

use Exception;

class FS {

    /**
     * The default working directory
     * 
     * @var string
     */
    protected static $dir;

    /**
     * The constructor
     */
    public function __construct() {
        // Nothing
    }

    /**
     * Returns the basename of a file
     * 
     * @param   string  $file_path
     * @return  string
     */
    public static function basename($file_path) {
        return basename( static::path($file_path) );
    }

    /**
     * Copy a file/directory on a new locaion on the disk
     * 
     * @param   string  $source_path
     * @param   string  $destination_path
     * @return  boolean
     */
    public static function copy($source_path, $destination_path) {
        if ( static::exists($source_path) ) {
            return copy(
                static::path($source_path),
                static::path($destination_path)
            );
        } else {
            false;
        }
    }

    /**
     * Delete a file
     * 
     * @param   string  $file_path
     * @return  boolean
     */
    public static function delete($file_path) {
        if ( static::exists($file_path) ) {
            unlink( static::path($file_path) );
            return true;
        } else {
            return false;
        }
    }

    /**
     * Assign $dir if unassigned
     * 
     * @param   string  $dir_path
     * @return  string
     */
    public static function dir($dir_path = '/') {
        if(empty(static::$dir)) {
            $default_disk = config('filesystem.default');
            static::$dir = config("filesystem.disks.{$default_disk}");
        }
        return str_replace('/', DIRECTORY_SEPARATOR, static::$dir . $dir_path);
    }

    /**
     * Change the working directory
     * 
     * @param   string  $disk_name
     * @return  \Simcyfy\FS
     */
    public static function disk($disk_name) {
        static::$dir = config("filesystem.disks.{$disk_name}");
        return new static();
    }

    /**
     * Download a file
     * 
     * @param   string  $file_path
     * @param   string  $file_name
     * @return  void
     */
    public static function download($file_path, $file_name = '') {
        $full_file_path = static::path($file_path);
        if ( empty($file_name) ) {
            $file_name = static::basename($file_path);
        }
        $file_size = static::size($file_path);
        response()->headers([
            'Cache-Control: must-revalidate',
            'Content-Description: File Transfer',
            "Content-Disposition: attachment; filename=\"{$file_name}\"",
            "Content-Length: {$file_size}",
            'Content-Type: application/octet-stream',
            'Pragma: public',
            'Expires: 0'
        ]);
        readfile(static::path($file_path));
    }

    /**
     * Check if a file/directory exists
     * 
     * @param   string  $file_path
     * @return  boolean
     */
    public static function exists($file_path) {
        return file_exists( static::path($file_path) );
    }

    /**
     * List all files in a directory
     * 
     * @param   string  $dir_path
     * @return  array
     */
    public static function ls($dir_path = '/') {
        return array_diff( scandir( static::dir($dir_path) ), ['.', '..'] );
    }

    /**
     * Move a file/directory to a different locaion on the disk
     * 
     * @param   string  $source_path
     * @param   string  $destination_path
     * @return  boolean
     */
    public static function move($source_path, $destination_path) {
        return rename(
            static::path($source_path),
            static::path($destination_path)
        );
    }

    /**
     * Rename a file/directory
     * 
     * @param   string  $source_path
     * @param   string  $destination_path
     * @return  boolean
     */
    public static function rename($source_path, $destination_path) {
        if ( static::exists($source_path) ) {
            return static::move($source_path, $destination_path);
        } else {
            false;
        }
    }

    /**
     * Generate a full path to a file
     * 
     * @param   string  $disk_name
     * @return  string
     */
    public static function path($file_path) {
        return static::dir() . str_replace('/', DIRECTORY_SEPARATOR, $file_path);
    }

    /**
     * Get a file's size
     * 
     * @param   string  $file_path
     * @return  mixed
     */
    public static function size($file_path) {
        if (static::exists($file_path)) {
            return filesize( static::path($file_path) );
        } else {
            return null;
        }
    }

    /**
     * Write a file to disk
     * 
     * @param   string  $file_path
     * @return  string
     */
    public static function save($file_path, $data) {
        try {
            $file = fopen( static::path($file_path), 'w+' );
            fwrite($file, $data);
            fclose($file);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

}
