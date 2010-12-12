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
 * @package    Zend_Controller
 * @subpackage Zend_Controller_Action_Helper
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: AutoCompleteDojo.php 20096 2010-01-06 02:05:09Z bkarwin $
 */

/**
 * @namespace
 */
namespace Zend\Controller\Action\Helper;
use Zend\Dojo;
use Zend\Layout;

/**
 * @see Zend_Controller_Action_Helper_AutoComplete_Abstract
 */
require_once 'Zend/Controller/Action/Helper/AutoComplete/Abstract.php';

/**
 * Create and send Dojo-compatible autocompletion lists
 *
 * @uses       Zend_Controller_Action_Helper_AutoComplete_Abstract
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage Zend_Controller_Action_Helper
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class AutoCompleteDojo extends AutoComplete\AbstractAutoComplete
{
    /**
     * Validate data for autocompletion
     *
     * Stub; unused
     *
     * @param  mixed $data
     * @return boolean
     */
    public function validateData($data)
    {
        return true;
    }

    /**
     * Prepare data for autocompletion
     *
     * @param  mixed   $data
     * @param  boolean $keepLayouts
     * @return string
     */
    public function prepareAutoCompletion($data, $keepLayouts = false)
    {
        if (!$data instanceof Dojo\Data) {
            require_once 'Zend/Dojo/Data.php';
            $items = array();
            foreach ($data as $key => $value) {
                $items[] = array('label' => $value, 'name' => $value);
            }
            $data = new Dojo\Data('name', $items);
        }

        if (!$keepLayouts) {
            require_once 'Zend/Controller/Action/HelperBroker.php';
            roker\HelperBroker::getStaticHelper('viewRenderer')->setNoRender(true);

            require_once 'Zend/Layout.php';
            $layout = Layout\Layout::getMvcInstance();
            if ($layout instanceof Layout\Layout) {
                $layout->disableLayout();
            }
        }

        $response = \Zend\Controller\Front::getInstance()->getResponse();
        $response->setHeader('Content-Type', 'application/json');

        return $data->toJson();
    }
}
