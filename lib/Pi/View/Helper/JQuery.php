<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @link            http://code.piengine.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://piengine.org
 * @license         http://piengine.org/license.txt BSD 3-Clause License
 * @package         View
 */

namespace Pi\View\Helper;

use Pi;

/**
 * Helper for loading jQuery files
 *
 * Usage inside a phtml template
 *
 * ```
 *  // Load basic jQuery file
 *  $this->jQuery();
 *
 *  // Load specific file
 *  $this->jQuery('some.js');
 *
 *  // Load specific file with attributes
 *  $this->jQuery('some.js',
 *      array('conditional' => '...', 'position' => 'prepend'));
 *
 *  // Load a list of files
 *  $this->jQuery(array(
 *      'some.css',
 *      'some.js',
 *  ));
 *
 *  // Load a list of files with corresponding attributes
 *  $this->jQuery(array(
 *      'some.css' => array('media' => '...', 'conditional' => '...'),
 *      'some.js',
 *  ));
 * ```
 *
 * @author Taiwen Jiang <taiwenjiang@tsinghua.org.cn>
 */
class JQuery extends AssetCanonize
{
    /** @var string Root dir of jQuery */
    const DIR_ROOT = 'vendor/jquery';

    /** @var bool jQuery basic file is loaded */
    protected static $rootLoaded;

    /**
     * Load jQuery files
     *
     * @param   null|string|array $files
     * @param   array $attributes
     * @param   bool|null $appendVersion
     *
     * @return  $this
     */
    public function __invoke(
        $files = null,
        $attributes = [],
        $appendVersion = null
    )
    {
        $files = $this->canonize($files, $attributes);
        if (empty(static::$rootLoaded)) {
            if (isset($files['jquery.min.js'])) {
                $baseAttrs = $files['jquery.min.js'];
            } else {
                $baseAttrs = $this->canonizeFile('jquery.min.js');
            }
            if (!is_array($baseAttrs)) {
                $baseAttrs = [
                    'file' => $baseAttrs,
                ];
            }
            /*
            if (!isset($baseAttrs['defer'])) {
                $baseAttrs['defer'] = false;
            }
            */
            $files              = ['jquery.min.js' => $baseAttrs] + $files;
            static::$rootLoaded = true;
        }

        foreach ($files as $file => $attrs) {
            $file     = static::DIR_ROOT . '/' . $file;
            $url      = Pi::service('asset')->getStaticUrl($file, $appendVersion);
            $position = isset($file['position'])
                ? $file['position'] : 'append';
            if ('css' == $attrs['ext']) {
                $attrs['href'] = $url;
                if ('prepend' == $position) {
                    $this->view->headLink()->prependStylesheet($attrs);
                } else {
                    $this->view->headLink()->appendStylesheet($attrs);
                }
            } else {
                if ('prepend' == $position) {
                    $this->view->headScript()
                        ->prependFile($url, 'text/javascript', $attrs);
                } else {
                    $this->view->headScript()
                        ->appendFile($url, 'text/javascript', $attrs);
                }
            }
        }

        return $this;
    }
}
