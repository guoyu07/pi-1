<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @link            http://code.piengine.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://piengine.org
 * @license         http://piengine.org/license.txt BSD 3-Clause License
 */

namespace Module\User\Api;

use Pi;
use Pi\Application\Api\AbstractApi;

/**
 * User activity APIs
 *
 * @author Taiwen Jiang <taiwenjiang@tsinghua.org.cn>
 */
class Activity extends AbstractApi
{
    /**
     * @{inheritDoc}
     */
    protected $module = 'user';

    /**
     * Get activity list sorted by display order
     *
     * @return array
     */
    public function getList($uid)
    {
        $result = [];
        $list   = Pi::registry('activity', 'user')->read();
        foreach ($list as $name => $activity) {

            $callback = $activity['callback'];
            if (!preg_match('|^http[s]?://|i', $callback)) {
                $module = isset($meta['module']) ? $meta['module'] : null;
                $reader = new $callback($module);
                if ($reader instanceof AbstractActivityCallback) {
                    $count = $reader->getCount($uid);
                }
            }

            $result[$name] = [
                'title' => $activity['title'],
                'icon'  => $activity['icon'],
                'count' => $count,
            ];
        }

        return $result;
    }

    /**
     * Get meta data and message list of an activity
     *
     * Log array: time, message
     *
     * @param int $uid
     * @param string $name
     * @param int $limit
     * @param int $offset
     *
     * @return array
     */
    public function get($uid, $name, $limit, $offset = 0)
    {
        $content = '';
        $link    = '';
        $items   = [];
        $data    = [];

        $meta     = Pi::registry('activity', 'user')->read($name);
        $callback = $meta['callback'];
        if (preg_match('|^http[s]?://|i', $callback)) {
            $data = Pi::service('remote')->get($callback, [
                'module' => $meta['module'],
                'uid'    => $uid,
                'limit'  => $limit,
                'offset' => $offset,
            ]);
        } else if ($callback != null) {
            $reader = new $callback($meta['module']);
            if ($reader instanceof AbstractActivityCallback) {
                $data = $reader->get($uid, $limit, $offset, $name);
            }
        }
        if (is_string($data)) {
            $content = $data;
        } elseif (empty($meta['template'])) {
            foreach ($data['items'] as $item) {
                if (is_string($item)) {
                    $items[] = [
                        'time'    => null,
                        'message' => $item,
                    ];
                } else {
                    $items[] = [
                        'time'    => isset($item['time']) ? $item['time'] : null,
                        'message' => $item['message'],
                    ];
                }
            }
            $link = isset($data['link']) ? $data['link'] : '';
        } else {
            // Render template()
            $template = [
                'module' => $meta['module'] ?: $this->module,
                'file'   => $meta['template'],
            ];
            $content  = Pi::service('view')->render($template, $data);
        }

        $result = [
            'title'       => $meta['title'],
            'description' => $meta['description'],
            'module'      => $meta['module'],
            'icon'        => $meta['icon'],
            'link'        => $link,
            'items'       => $items,
            'content'     => $content,
        ];

        return $result;
    }
}
