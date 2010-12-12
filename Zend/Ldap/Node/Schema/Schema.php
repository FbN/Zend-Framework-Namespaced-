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
 * @package    Zend_Ldap
 * @subpackage Schema
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Schema.php 20096 2010-01-06 02:05:09Z bkarwin $
 */

/**
 * @namespace
 */
namespace Zend\Ldap\Node\Schema;
use Zend\Ldap;
use Zend\Ldap\Node\RootDse;
use Zend\Ldap;

/**
 * @see Zend_Ldap_Node_Abstract
 */
require_once 'Zend/Ldap/Node/Abstract.php';

/**
 * Zend_Ldap_Node_Schema provides a simple data-container for the Schema node.
 *
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage Schema
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Schema extends Ldap\Node\AbstractNode
{
    const OBJECTCLASS_TYPE_UNKNOWN    = 0;
    const OBJECTCLASS_TYPE_STRUCTURAL = 1;
    const OBJECTCLASS_TYPE_ABSTRACT   = 3;
    const OBJECTCLASS_TYPE_AUXILIARY  = 4;

    /**
     * Factory method to create the Schema node.
     *
     * @param  Zend_Ldap $ldap
     * @return Zend_Ldap_Node_Schema
     * @throws Zend_Ldap_Exception
     */
    public static function create(Ldap\Ldap $ldap)
    {
        $dn = $ldap->getRootDse()->getSchemaDn();
        $data = $ldap->getEntry($dn, array('*', '+'), true);
        switch ($ldap->getRootDse()->getServerType()) {
            case RootDse\RootDse::SERVER_TYPE_ACTIVEDIRECTORY:
                /**
                 * @see Zend_Ldap_Node_Schema_ActiveDirectory
                 */
                require_once 'Zend/Ldap/Node/Schema/ActiveDirectory.php';
                return new ActiveDirectory($dn, $data, $ldap);
            case RootDse\RootDse::SERVER_TYPE_OPENLDAP:
                /**
                 * @see Zend_Ldap_Node_RootDse_ActiveDirectory
                 */
                require_once 'Zend/Ldap/Node/Schema/OpenLdap.php';
                return new OpenLdap($dn, $data, $ldap);
            case RootDse\RootDse::SERVER_TYPE_EDIRECTORY:
            default:
                return new \self($dn, $data, $ldap);
        }
    }

    /**
     * Constructor.
     *
     * Constructor is protected to enforce the use of factory methods.
     *
     * @param  Zend_Ldap_Dn $dn
     * @param  array        $data
     * @param  Zend_Ldap    $ldap
     */
    protected function __construct(Ldap\Dn $dn, array $data, Ldap\Ldap $ldap)
    {
        parent::__construct($dn, $data, true);
        $this->_parseSchema($dn, $ldap);
    }

    /**
     * Parses the schema
     *
     * @param  Zend_Ldap_Dn $dn
     * @param  Zend_Ldap    $ldap
     * @return Zend_Ldap_Node_Schema Provides a fluid interface
     */
    protected function _parseSchema(Ldap\Dn $dn, Ldap\Ldap $ldap)
    {
        return $this;
    }

    /**
     * Gets the attribute Types
     *
     * @return array
     */
    public function getAttributeTypes()
    {
        return array();
    }

    /**
     * Gets the object classes
     *
     * @return array
     */
    public function getObjectClasses()
    {
        return array();
    }
}