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
 * @package    Zend_Gdata
 * @subpackage Books
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: BooksCategory.php 20096 2010-01-06 02:05:09Z bkarwin $
 */

/**
 * @namespace
 */
namespace Zend\Gdata\Books\Extension;

/**
 * @see Zend_Gdata_App_Extension_Category
 */
require_once 'Zend/Gdata/App/Extension/Category.php';

/**
 * Describes a books category
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Books
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class BooksCategory extends
    \Zend\Gdata\App\Extension\Category
{

    /**
     * Constructor for Zend_Gdata_Books_Extension_BooksCategory which
     * Describes a books category
     *
     * @param string|null $term An identifier representing the category within
     *        the categorization scheme.
     * @param string|null $scheme A string containing a URI identifying the
     *        categorization scheme.
     * @param string|null $label A human-readable label for display in
     *        end-user applications.
     */
    public function __construct($term = null, $scheme = null, $label = null)
    {
        $this->registerAllNamespaces(\Zend\Gdata\Books\Books::$namespaces);
        parent::__construct($term, $scheme, $label);
    }

}
