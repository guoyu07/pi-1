<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @link            http://code.piengine.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://piengine.org
 * @license         http://piengine.org/license.txt BSD 3-Clause License
 */

namespace Module\System\Controller\Admin;

use Pi;
use Pi\Mvc\Controller\ActionController;
use Pi\Paginator\Paginator;

class DatabaseController extends ActionController
{
    /**
     * Database tools
     *
     * @return void
     */
    public function indexAction()
    {

    }

    public function checkAction()
    {
        $sql = "SHOW TABLES";
        $results = Pi::db()->getAdapter()->query($sql, 'execute');

        $columnsError = array();

        foreach ($results->toArray() as $result){
            $tableName = array_shift($result);

            $sql = <<<SQL
SHOW FULL COLUMNS FROM `{$tableName}`;
SQL;

            $resultsColumns = Pi::db()->getAdapter()->query($sql, 'execute');

            foreach($resultsColumns->toArray() as $resultColumn){
                if($resultColumn['Collation'] && $resultColumn['Collation'] != 'utf8_general_ci'){
                    $columnsError[$tableName][$resultColumn['Field']] = $resultColumn['Collation'];
                }
            }
        }

        $this->view()->assign('columnsError', $columnsError);
    }
}
