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
 * @package    Zend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Application\Resource;

/**
 * @category   Zend
 * @package    Zend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Useragent extends ResourceAbstract 
{
    /**
     * @var Zend_Http_UserAgent
     */
	protected $_userAgent;
	
    /**
     * Intialize resource
     * 
     * @return Zend_Http_UserAgent
     */
    public function init() 
    {
		$userAgent = $this->getUserAgent();

        // Optionally seed the UserAgent view helper
        $bootstrap = $this->getBootstrap();
        if ($bootstrap->hasResource('view') || $bootstrap->hasPluginResource('view')) {
            $bootstrap->bootstrap('view');
            $view = $bootstrap->getResource('view');
            if (null !== $view) {
                $view->userAgent($userAgent);
            }
        }

        return $userAgent;
	}
	
    /**
     * Get UserAgent instance
     * 
     * @return Zend_Http_UserAgent
     */
    public function getUserAgent() 
    {
		if (null === $this->_userAgent) {
			$options = $this->getOptions();
			$this->_userAgent = new \Zend\Http\UserAgent\UserAgent($options);
		}
		
		return $this->_userAgent;
	}
}
