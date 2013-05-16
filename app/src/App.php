<?php namespace Dirlister\src;

use Dirlister\lib\Renderer;

/** Application instance
 */
class App {
    /** Application configuration
     * @var Config
     */
    public $config;

    /** Init the app
     * @param Config $config
     */
    function __construct(Config $config){
        $this->config = $config;
    }

    /** Provides HTTP auth for the user
     * @param $callback
     * @param $realm
     */
    static public function httpAuth($callback, $realm = 'Restricted access') {
        $ok  = isset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])
            && $callback($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);

        if (!$ok){
            header('HTTP/1.1 401 Unauthorized');
            header('WWW-Authenticate: Basic realm="'.$realm.'"');
        }
        return $ok;
    }

    /** Authenticate the current request
     */
    function authenticate(){
        $pairs = $this->config->listing['auth']['users'];
        $auth = function($login,$passw)use($pairs){
            return isset($pairs[$login]) && $pairs[$login] == $passw;
        };
        return static::httpAuth($auth, $this->config->listing['auth']['realm']);
    }

    /** List dir
     * @param string $listPath
     */
    function actionDirListing($listPath){
        $listPath = trim($listPath, '\\/');
        $listFsPath = rtrim($this->config->files['path'], '\\/').'/'.$listPath;

        # Sanitize the input
        if (FALSE === strpos(
            realpath($listFsPath),
            realpath($this->config->files['path'])
        ))
            throw new \Exception('Invalid path: '.$listPath);

        # Utils
        $fmtFileSize = function($size, $precision = 2){
            $base = log($size) / log(1024);
            $suffixes = ' KMGTPEZY';
            return round(pow(1024, $base - floor($base)), $precision) . $suffixes[(int)floor($base)].'b';
        };

        # Load descriptions
        $metaFile = null;
        if (file_exists($this->config->files['meta']))
            $metaFile = parse_ini_file($this->config->files['meta'], true);

        # List the files
        $dirs = array();
        $files = array();
        foreach (scandir($listFsPath) as $name)
            if ($name[0] != '.') {
                $is_dir = is_dir($listFsPath.'/'.$name);

                $path = ltrim($listPath.'/'.$name, '/\\');
                $fs_path = $listFsPath.'/'.$name;
                $url = rtrim($this->config->files['web'], '\\/').'/'.$path;

                $meta = isset($metaFile[$path])? $metaFile[$path] : null;
                
                # Ignore?
                $ign = $this->config->listing['ignore'];
                if (!empty($ign['name']) && preg_match($ign['name'], $name)) continue;
                if (!empty($ign['path']) && preg_match($ign['path'], $path)) continue;
                
                # Display

                $obj = (object)array(
                    'is_dir' => $is_dir,
                    'name' => $name,
                    'path' => $path,
                    'url' => $is_dir? $name.'/' : $url,
                    'mtime' => $is_dir? null : date('Y-m-d H:i', filemtime($fs_path)),
                    'size' => $is_dir? count(scandir($fs_path))-2 : $fmtFileSize(filesize($fs_path)),
                    'comment' => isset($meta['comment'])? $meta['comment'] : '',
                );

                if (is_dir($listPath))
                    $dirs[] = $obj;
                else
                    $files[] = $obj;
            }

        # Render
//        xdebug_var_dump(array_merge($dirs, $files));die();
        $renderer = new Renderer('app/resources/views/DirListing.tpl.php');
        return $renderer->render(array(
            'path' => $listPath,
            'files' => array_merge($dirs, $files),
            'currentPath' => $listPath,
        ));
    }
}
