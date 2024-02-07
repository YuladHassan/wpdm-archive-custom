<?php
/*
Plugin Name: WPDM - Archive Page
Description: Add archive page option with wordpress download manager
Plugin URI: https://www.wpdownloadmanager.com/download/wpdm-directory-add-on/
Author: WordPress Download Manager
Version: 4.4.7
Author URI: https://www.wpdownloadmanager.com/
Text Domain: wpdm-archive-page
Update URI: wpdm-archive-page
*/

namespace WPDM\AddOn\ArchivePage;

global $ArchivePages;

use WPDM\AddOn\ArchivePage\__\__;
use WPDM\AddOn\ArchivePage\ShortcodeGenerators\ExtendMCEButton;
use WPDM\AddOn\ArchivePage\Templates\Category\Blocks;
use WPDM\AddOn\ArchivePage\Templates\Category\Categories;
use WPDM\AddOn\ArchivePage\Templates\Compact\Compact;
use WPDM\AddOn\ArchivePage\Templates\Filter\Filter;
use WPDM\AddOn\ArchivePage\Templates\Flat\Flat;
use WPDM\AddOn\ArchivePage\Templates\Simple\Simple;
use WPDM\AddOn\ArchivePage\Templates\SimpleSearch\SimpleSearch;
use WPDM\AddOn\ArchivePage\Templates\Starter\Starter;
use WPDM\AddOn\ArchivePage\Templates\Tags\Tags;

/**
 * Plugin version
 */

define('WPDMAP_VERSION', '4.4.6');

/**
 * Text domain constant
 */
define('WPDMAP_TEXT_DOMAIN', 'wpdm-archive-page');

/**
 * Plugin dir name
 */
define("WPDMAP_DIR_NAME", basename(__DIR__));

/**
 * Plugin base dir
 */
define("WPDMAP_BASE_DIR", dirname(__FILE__) . '/');

/**
 * Plugin base url
 */
define("WPDMAP_BASE_URL", plugin_dir_url(__FILE__));

/**
 * Plugin asset url
 */
define("WPDMAP_ASSET_URL", plugin_dir_url(__FILE__).'assets/');

/**
 * Starter functions
 */
require_once "src/init.php";


class ArchivePages
{

    var $baseDir = __DIR__.'/';

    function __construct()
    {

        $this->autoLoadClasses();

        $this->actions();

        /**
         * Data manager class
         */
        new __();

        /**
         * Archive page shortcode and templates
         */
        new Starter();
        new Compact();
        new Simple();
        new Flat();
        new Filter();
        new Blocks();
        new Categories();
        new SimpleSearch();
        new Tags();

    }

    function actions()
    {
        add_action('plugins_loaded', array($this, 'loadEssentials') );

        add_action('wp_enqueue_scripts',array($this, 'enqueue'));

        add_action('wpdm_ext_shortcode', [new ExtendMCEButton(), "render"]);

        add_filter('update_plugins_wpdm-archive-page', [$this, "updatePlugin"], 10, 4);
    }

    /**
     * Class autoloader
     */
    function autoLoadClasses()
    {
        spl_autoload_register(function ($class) {

            // project-specific namespace prefix
            $prefix = 'WPDM\\AddOn\\ArchivePage\\';

            // base directory for the namespace prefix
            $base_dir = __DIR__ . '/src/';

            // does the class use the namespace prefix?
            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                // no, move to the next registered autoloader
                return;
            }

            // get the relative class name
            $relative_class = substr($class, $len);

            // replace the namespace prefix with the base directory, replace namespace
            // separators with directory separators in the relative class name, append
            // with .php
            $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

            // if the file exists, require it
            if (file_exists($file)) {
                require $file;
            }
        });
    }

    /**
     * @usage Load essentials
     */
    function loadEssentials(){

        /**
         * Load text domain
         */
        load_plugin_textdomain('wpdm-archive-page', WP_PLUGIN_URL . "/wpdm-archive-page/languages/", 'wpdm-archive-page/languages/');
    }

    /**
     * Styles and scripts required for this plugin
     */
    function enqueue(){
        global $post;

        if( is_object($post) ){
            if (
                !substr_count($post->post_content,'wpdm_archive') &&
                !substr_count($post->post_content,'wpdm_categories') &&
                !substr_count($post->post_content,'wpdm_tags') &&
                !substr_count($post->post_content,'wpdm_search_page') &&
                !substr_count($post->post_content,'wpdm_simple_search')
            )
                return;
        }
        wp_enqueue_style("wpdmap-styles", WPDMAP_ASSET_URL.'css/style.min.css');
        wp_enqueue_script("wpdmap-scripts", WPDMAP_ASSET_URL.'js/scripts.js', ['wp-i18n'], WPDMAP_VERSION);

    }

    function updatePlugin($update, $plugin_data, $plugin_file, $locales){
        $id = basename(__DIR__);
        $latest_versions = WPDM()->updater->getLatestVersions();
        $latest_version = wpdm_valueof($latest_versions, $id);
        $access_token = wpdm_access_token();
        $update = [];
        $update['id']           = $id;
        $update['slug']         = $id;
        $update['url']          = $plugin_data['PluginURI'];
        $update['tested']       = true;
        $update['version']      = $latest_version;
        $update['package']      = $access_token !== '' ? "https://www.wpdownloadmanager.com/?wpdmpp_file={$id}.zip&access_token={$access_token}" : '';
        return $update;
    }

}
//forked and pulled

if(defined('WPDM_VERSION'))
    $ArchivePages = new ArchivePages();
