<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @link            http://code.piengine.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://piengine.org
 * @license         http://piengine.org/license.txt BSD 3-Clause License
 */

namespace Module\User\Controller\Front;

use Pi;
use Pi\Mvc\Controller\ActionController;

/**
 * Activity controller
 *
 * @author Liu Chuang <liuchuang@eefocus.com>
 */
class ActivityController extends ActionController
{
    /**
     * Display activity
     *
     * @return array|void
     */
    public function indexAction()
    {
        // Check front disable
        if ($this->config('disable_front')) {
            return $this->jumpToDenied(__('View information is disable'));
        }

        $name     = _get('name');
        $uid      = _get('uid');
        $ownerUid = Pi::user()->getId();
        $limit    = Pi::config('list_limit', 'user');
        $page     = _get('page', 'int') ?: 1;
        if (!$uid && !$ownerUid) {
            $this->jump(
                [
                    'controller' => 'profile',
                    'action'     => 'index',
                ],
                __('User was not found.'),
                'error'
            );
        }

        // Check is owner
        if (!$uid) {
            $uid  = $ownerUid;
            $view = false;
        } else {
            $view = true;

        }
        if (!$name) {
            $this->jump(
                [
                    'controller' => 'profile',
                    'action'     => 'index',
                ],
                __('An error occurred.'),
                'error'
            );
        }
        // Get activity list for nav display
        $activityList = Pi::api('activity', 'user')->getList($uid);

        // Get current activity data
        $data = Pi::api('activity', 'user')->get($uid, $name, $limit, $page);

        // Get user base info
        $user         = Pi::api('user', 'user')->get(
            $uid,
            ['name', 'country', 'city', 'time_activated'],
            true,
            true
        );
        $user['name'] = isset($user['name']) ? $user['name'] : null;
        $this->view()->assign([
            'list' => $activityList,
            'name' => $name,
            'data' => $data,
            'uid'  => $uid,
            'user' => $user,
            'view' => $view,
        ]);
    }

    /**
     * Test for activity more link contents
     */
    public function moreAction()
    {
        $this->view()->setTemplate('activity-more');
    }
}
