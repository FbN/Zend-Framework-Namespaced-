<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Test
 * @subpackage PHPUnit
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: DatabaseTestCase.php 20096 2010-01-06 02:05:09Z bkarwin $
 */

/**
 * @namespace
 */
namespace Zend\Test\PHPUnit;

/**
 * @see PHPUnit_Extensions_Database_TestCase
 */
require_once "PHPUnit/Extensions/Database/TestCase.php";

/**
 * @see Zend_Test_PHPUnit_Db_Operation_Truncate
 */
require_once "Zend/Test/PHPUnit/Db/Operation/Truncate.php";

/**
 * @see Zend_Test_PHPUnit_Db_Operation_Insert
 */
require_once "Zend/Test/PHPUnit/Db/Operation/Insert.php";

/**
 * @see Zend_Test_PHPUnit_Db_DataSet_DbTableDataSet
 */
require_once "Zend/Test/PHPUnit/Db/DataSet/DbTableDataSet.php";

/**
 * @see Zend_Test_PHPUnit_Db_DataSet_DbTable
 */
require_once "Zend/Test/PHPUnit/Db/DataSet/DbTable.php";

/**
 * @see Zend_Test_PHPUnit_Db_DataSet_DbRowset
 */
require_once "Zend/Test/PHPUnit/Db/DataSet/DbRowset.php";

/**
 * Generic Testcase for Zend Framework related DbUnit Testing with PHPUnit
 *
 * @uses       PHPUnit_Extensions_Database_TestCase
 * @category   Zend
 * @package    Zend_Test
 * @subpackage PHPUnit
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class DatabaseTestCase extends \PHPUnit\Extensions\Database\TestCase
{
    /**
     * Creates a new Zend Database Connection using the given Adapter and database schema name.
     *
     * @param  Zend_Db_Adapter_Abstract $connection
     * @param  string $schema
     * @return Zend_Test_PHPUnit_Db_Connection
     */
    protected function createZendDbConnection(\Zend\Db\Adapter\AbstractAdapter $connection, $schema)
    {
        return new Db\Connection($connection, $schema);
    }

    /**
     * Convenience function to get access to the database connection.
     *
     * @return Zend_Db_Adapter_Abstract
     */
    protected function getAdapter()
    {
        return $this->getConnection()->getConnection();
    }

    /**
     * Returns the database operation executed in test setup.
     *
     * @return PHPUnit_Extensions_Database_Operation_DatabaseOperation
     */
    protected function getSetUpOperation()
    {
        return new \PHPUnit\Extensions\Database\Operation\Composite(array(
            new Db\Operation\Truncate(),
            new Db\Operation\Insert(),
        ));
    }

    /**
     * Returns the database operation executed in test cleanup.
     *
     * @return PHPUnit_Extensions_Database_Operation_DatabaseOperation
     */
    protected function getTearDownOperation()
    {
        return \PHPUnit\Extensions\Database\Operation\Factory::NONE();
    }

    /**
     * Create a dataset based on multiple Zend_Db_Table instances
     *
     * @param  array $tables
     * @return Zend_Test_PHPUnit_Db_DataSet_DbTableDataSet
     */
    protected function createDbTableDataSet(array $tables=array())
    {
        $dataSet = new Db\DataSet\DbTableDataSet();
        foreach($tables AS $table) {
            $dataSet->addTable($table);
        }
        return $dataSet;
    }

    /**
     * Create a table based on one Zend_Db_Table instance
     *
     * @param Zend_Db_Table_Abstract $table
     * @param string $where
     * @param string $order
     * @param string $count
     * @param string $offset
     * @return Zend_Test_PHPUnit_Db_DataSet_DbTable
     */
    protected function createDbTable(\Zend\Db\Table\AbstractTable $table, $where=null, $order=null, $count=null, $offset=null)
    {
        return new Db\DataSet\DbTable($table, $where, $order, $count, $offset);
    }

    /**
     * Create a data table based on a Zend_Db_Table_Rowset instance
     *
     * @param  Zend_Db_Table_Rowset_Abstract $rowset
     * @param  string
     * @return Zend_Test_PHPUnit_Db_DataSet_DbRowset
     */
    protected function createDbRowset(\Zend\Db\Table\Rowset\AbstractRowset $rowset, $tableName = null)
    {
        return new Db\DataSet\DbRowset($rowset, $tableName);
    }
}