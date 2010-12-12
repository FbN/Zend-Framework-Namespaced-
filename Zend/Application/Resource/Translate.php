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
 * @version    $Id: Translate.php 22968 2010-09-18 19:50:02Z intiilapa $
 */

/**
 * @namespace
 */
namespace Zend\Application\Resource;
use Zend\Translate;
use Zend;

/**
 * @see Zend_Application_Resource_ResourceAbstract
 */
require_once 'Zend/Application/Resource/ResourceAbstract.php';


/**
 * Resource for setting translation options
 *
 * @uses       Zend_Application_Resource_ResourceAbstract
 * @category   Zend
 * @package    Zend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Translate extends ResourceAbstract
{
    const DEFAULT_REGISTRY_KEY = 'Zend_Translate';

    /**
     * @var Zend_Translate
     */
    protected $_translate;

    /**
     * Defined by Zend_Application_Resource_Resource
     *
     * @return Zend_Translate
     */
    public function init()
    {
        return $this->getTranslate();
    }

    /**
     * Retrieve translate object
     *
     * @return Zend_Translate
     * @throws Zend_Application_Resource_Exception if registry key was used
     *          already but is no instance of Zend_Translate
     */
    public function getTranslate()
    {
        if (null === $this->_translate) {
            $options = $this->getOptions();

            if (!isset($options['content']) && !isset($options['data'])) {
                require_once 'Zend/Application/Resource/Exception.php';
                throw new Exception('No translation source data provided.');
            } else if (array_key_exists('content', $options) && array_key_exists('data', $options)) {
                require_once 'Zend/Application/Resource/Exception.php';
                throw new Exception(
                    'Conflict on translation source data: choose only one key between content and data.'
                );
            }

            if (empty($options['adapter'])) {
                $options['adapter'] = Translate\Translate::AN_ARRAY;
            }

            if (!empty($options['data'])) {
                $options['content'] = $options['data'];
                unset($options['data']);
            }

            if (isset($options['options'])) {
                foreach($options['options'] as $key => $value) {
                    $options[$key] = $value;
                }
            }

            if (!empty($options['cache']) && is_string($options['cache'])) {
                $bootstrap = $this->getBootstrap();
                if ($bootstrap instanceof end\Application\Bootstrap\ResourceBootstrapper &&
                    $bootstrap->hasPluginResource('CacheManager')
                ) {
                    $cacheManager = $bootstrap->bootstrap('CacheManager')
                        ->getResource('CacheManager');
                    if (null !== $cacheManager &&
                        $cacheManager->hasCache($options['cache'])
                    ) {
                        $options['cache'] = $cacheManager->getCache($options['cache']);
                    }
                }
            }

            $key = (isset($options['registry_key']) && !is_numeric($options['registry_key']))
                 ? $options['registry_key']
                 : self::DEFAULT_REGISTRY_KEY;
            unset($options['registry_key']);

            if(end\Registry::isRegistered($key)) {
                $translate = end\Registry::get($key);
                if(!$translate instanceof Translate\Translate) {
                    require_once 'Zend/Application/Resource/Exception.php';
                    throw new Exception($key
                                   . ' already registered in registry but is '
                                   . 'no instance of Zend_Translate');
                }

                $translate->addTranslation($options);
                $this->_translate = $translate;
            } else {
                $this->_translate = new Translate\Translate($options);
                end\Registry::set($key, $this->_translate);
            }
        }

        return $this->_translate;
    }
}
