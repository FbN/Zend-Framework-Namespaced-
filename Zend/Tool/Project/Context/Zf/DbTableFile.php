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
 * @package    Zend_Tool
 * @subpackage Framework
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: DbTableFile.php 20851 2010-02-02 21:45:51Z ralph $
 */

/**
 * @namespace
 */
namespace Zend\Tool\Project\Context\Zf;
use Zend\CodeGenerator\Php\Property;

/**
 * This class is the front most class for utilizing Zend_Tool_Project
 *
 * A profile is a hierarchical set of resources that keep track of
 * items within a specific project.
 *
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class DbTableFile extends AbstractClassFile
{

    protected $_dbTableName = null;
    
    protected $_actualTableName = null;
    
    /**
     * getName()
     *
     * @return string
     */
    public function getName()
    {
        return 'DbTableFile';
    }

    /**
     * init()
     *
     */
    public function init()
    {
        $this->_dbTableName = $this->_resource->getAttribute('dbTableName');
        $this->_actualTableName = $this->_resource->getAttribute('actualTableName');
        $this->_filesystemName = ucfirst($this->_dbTableName) . '.php';
        parent::init();
    }
    
    public function getPersistentAttributes()
    {
        return array('dbTableName' => $this->_dbTableName);
    }

    public function getContents()
    {
        $className = $this->getFullClassName($this->_dbTableName, 'Model_DbTable');
        
        $codeGenFile = new \Zend\CodeGenerator\Php\File(array(
            'fileName' => $this->getPath(),
            'classes' => array(
                new \Zend\CodeGenerator\Php\Class(array(
                    'name' => $className,
                    'extendedClass' => 'Zend_Db_Table_Abstract',
                    'properties' => array(
                        new Property\Property(array(
                            'name' => '_name',
                            'visibility' => Property\Property::VISIBILITY_PROTECTED,
                            'defaultValue' => $this->_actualTableName
                            ))
                        ),
                
                    ))
                )
            ));
        return $codeGenFile->generate();
    }
    
}
