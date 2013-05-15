<?php namespace Dirlister\src;

class Config extends \Dirlister\lib\Config {
    /** Files config
     * ['path'] - path to files
     * ['meta'] - the metadata file
     * @var array
     */
    public $files;

    /** Directory listing settings
     * ['auth'] - Password-protection for the directory listing. Login:password pairs
     * @var array
     */
    public $listing;
}
