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
 * @version    $Id: QueryTable.php 20096 2010-01-06 02:05:09Z bkarwin $
 */

/**
 * @namespace
 */
namespace Zend\Test\PHPUnit\Db\DataSet;

/**
 * @see PHPUnit_Extensions_Database_DataSet_QueryTable
 */
require_once "PHPUnit/Extensions/Database/DataSet/QueryTable.php";

/**
 * @see PHPUnit_Extensions_Database_DB_IDatabaseConnection
 */
require_once "PHPUnit/Extensions/Database/DB/IDatabaseConnection.php";

/**
 * Represent a PHPUnit Database Extension table with Queries using a Zend_Db adapter for assertion against other tables.
 *
 * @uses       PHPUnit_Extensions_Database_DataSet_QueryTable
 * @category   Zend
 * @package    Zend_Test
 * @subpackage PHPUnit
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class QueryTable extends \PHPUnit\Extensions\Database\DataSet\QueryTable
{
    /**
     * Creates a new database query table object.
     *
     * @param string $table_name
     * @param string $query
     * @param PHPUnit_Extensions_Database_DB_IDatabaseConnection $databaseConnection
     */
    public function __construct($tableName, $query, \PHPUnit\Extensions\Database\DB\IDatabaseConnection $databaseConnection)
    {
        if( !($databaseConnection instanceof \Zend\Test\PHPUnit\Db\Connection) ) {
            require_once "Zend/Test/PHPUnit/Db/Exception.php";
            throw new \Zend\Test\PHPUnit\Db\Exception("Zend_Test_PHPUnit_Db_DataSet_QueryTable only works with Zend_Test_PHPUnit_Db_Connection connections-");
        }
        parent::__construct($tableName, $query, $databaseConnection);
    }

    /**
     * Load data from the database.
     *
     * @return void
     */
    protected function loadData()
    {
        if($this->data === null) {
            $stmt = $this->databaseConnection->getConnection()->query($this->query);
            $this->data = $stmt->fetchAll(\Zend\Db\Db::FETCH_ASSOC);
        }
    }

    /**
     * Create Table Metadata
     */
    protected function createTableMetaData()
    {
        if ($this->tableMetaData === NULL)
        {
            $this->loadData();
            $keys = array();
            if(count($this->data) > 0) {
                $keys = array_keys($this->data[0]);
            }
            $this->tableMetaData = new \PHPUnit\Extensions\Database\DataSet\DefaultTableMetaData(
                $this->tableName, $keys
            );
        }
    }
}