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
 * @version    $Id: Insert.php 20096 2010-01-06 02:05:09Z bkarwin $
 */

/**
 * @namespace
 */
namespace Zend\Test\PHPUnit\Db\Operation;

/**
 * @see PHPUnit_Extensions_Database_Operation_IDatabaseOperation
 */
require_once "PHPUnit/Extensions/Database/Operation/IDatabaseOperation.php";

/**
 * @see PHPUnit_Extensions_Database_DB_IDatabaseConnection
 */
require_once "PHPUnit/Extensions/Database/DB/IDatabaseConnection.php";

/**
 * @see PHPUnit_Extensions_Database_DataSet_IDataSet
 */
require_once "PHPUnit/Extensions/Database/DataSet/IDataSet.php";

/**
 * @see PHPUnit_Extensions_Database_Operation_Exception
 */
require_once "PHPUnit/Extensions/Database/Operation/Exception.php";

/**
 * @see Zend_Test_PHPUnit_Db_Connection
 */
require_once "Zend/Test/PHPUnit/Db/Connection.php";

/**
 * Operation for Inserting on setup or teardown of a database tester.
 *
 * @uses       PHPUnit_Extensions_Database_Operation_IDatabaseOperation
 * @category   Zend
 * @package    Zend_Test
 * @subpackage PHPUnit
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Insert implements \PHPUnit\Extensions\Database\Operation\IDatabaseOperation
{
    /**
     * @param PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection
     * @param PHPUnit_Extensions_Database_DataSet_IDataSet $dataSet
     */
    public function execute(\PHPUnit\Extensions\Database\DB\IDatabaseConnection $connection, \PHPUnit\Extensions\Database\DataSet\IDataSet $dataSet)
    {
        if(!($connection instanceof \Zend\Test\PHPUnit\Db\Connection)) {
            require_once "Zend/Test/PHPUnit/Db/Exception.php";
            throw new \Zend\Test\PHPUnit\Db\Exception("Not a valid Zend_Test_PHPUnit_Db_Connection instance, ".get_class($connection)." given!");
        }

        $databaseDataSet = $connection->createDataSet();

        $dsIterator = $dataSet->getIterator();

        foreach($dsIterator as $table) {
            $tableName = $table->getTableMetaData()->getTableName();

            $db = $connection->getConnection();
            for($i = 0; $i < $table->getRowCount(); $i++) {
                $values = $this->buildInsertValues($table, $i);
                try {
                    $db->insert($tableName, $values);
                } catch (\Exception $e) {
                    throw new \PHPUnit\Extensions\Database\Operation\Exception("INSERT", "INSERT INTO ".$tableName." [..]", $values, $table, $e->getMessage());
                }
            }
        }
    }

    /**
     *
     * @param PHPUnit_Extensions_Database_DataSet_ITable $table
     * @param int $rowNum
     * @return array
     */
    protected function buildInsertValues(\PHPUnit\Extensions\Database\DataSet\ITable $table, $rowNum)
    {
        $values = array();
        foreach($table->getTableMetaData()->getColumns() as $columnName) {
            $values[$columnName] = $table->getValue($rowNum, $columnName);
        }
        return $values;
    }
}