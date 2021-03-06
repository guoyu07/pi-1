<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @link            http://code.piengine.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://piengine.org
 * @license         http://piengine.org/license.txt BSD 3-Clause License
 */

namespace Module\Page\Route;

use Pi;
use Pi\Mvc\Router\Http\Standard;

/**
 * Route for pages
 *
 *  1. ID: /url/page/123
 *  2. Slug: /url/page/my-slug
 *  3. Name: /url/page/myname
 */
class Page extends Standard
{
    /**
     * {@inheritDoc}
     */
    public function parse($path)
    {
        $module = $this->defaults['module'];

        $name = '';
        if ($path) {
            if (false !== ($pos = strpos($path, $this->paramDelimiter))) {
                list($name, $path) = explode($this->paramDelimiter, $path, 2);
            } else {
                $name = $path;
                $path = '';
            }
        }
        $params  = $path
            ? explode($this->paramDelimiter, trim($path, $this->paramDelimiter))
            : [];
        $params  = parent::parseParams($params);
        $matches = array_merge($this->defaults, $params);

        // Set id
        if (is_numeric($name)) {
            $matches['id'] = (int)$name;
            $name          = '';
            // Set name
        } else {
            $name = $this->decode($name);
        }

        // Set action
        $action   = '';
        $pageList = Pi::registry('page', $module)->read();
        if (!empty($matches['id'])) {
            if (isset($pageList[$matches['id']])) {
                $action = $pageList[$matches['id']]['name'];
            }
        } else {
            $pName = empty($matches['name']) ? $name : $matches['name'];
            $pSlug = empty($matches['slug']) ? $name : $matches['slug'];

            $filter         = new \Pi\Filter\Slug;
            $filtered_pName = $filter($pName);
            $filtered_pSlug = $filter($pSlug);

            if ($filtered_pName || $filtered_pSlug) {
                foreach ($pageList as $id => $page) {
                    if ($filtered_pName && $filtered_pName == $page['name']) {
                        $action        = $pName;
                        $matches['id'] = $id;
                        break;
                    }
                    if ($filtered_pSlug && $filtered_pSlug == $page['slug']) {
                        $action        = $pSlug;
                        $matches['id'] = $id;
                        break;
                    }
                }
            }
        }
        if ($action) {
            $matches['action'] = $action;
        }

        return $matches;
    }

    /**
     * {@inheritDoc}
     */
    public function assemble(array $params = [], array $options = [])
    {
        //$this->prefix = $this->defaults['module'];

        $mergedParams = array_merge($this->defaults, $params);
        $url          = '';
        if (!empty($mergedParams['slug'])) {
            $url = $this->encode($mergedParams['slug']);
        } elseif (!empty($mergedParams['name'])) {
            $url = $this->encode($mergedParams['name']);
        } elseif (!empty($mergedParams['id'])) {
            $url = (int)$mergedParams['id'];
        }
        if (empty($url)) {
            return $this->prefix;
        }
        $params       = [];
        $prefix       = $this->prefix;
        $this->prefix .= $this->paramDelimiter . $url;
        $url          = parent::assemble($params, $options);
        $this->prefix = $prefix;
        $url          = rtrim($url, '/');

        return $url;
    }
}
