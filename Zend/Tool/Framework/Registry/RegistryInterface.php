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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Interface.php 20096 2010-01-06 02:05:09Z bkarwin $
 */

/**
 * @namespace
 */
namespace Zend\Tool\Framework\Registry;

/**
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface RegistryInterface
{


    /**
     * setClient()
     *
     * @param Zend_Tool_Framework_Client_Abstract $client
     * @return Zend_Tool_Framework_Registry
     */
    public function setClient(\Zend\Tool\Framework\Client\AbstractClient $client);

    /**
     * getClient() return the client in the registry
     *
     * @return Zend_Tool_Framework_Client_Abstract
     */
    public function getClient();

    /**
     * setLoader()
     *
     * @param Zend_Tool_Framework_Loader_Abstract $loader
     * @return Zend_Tool_Framework_Registry
     */
    public function setLoader(\Zend\Tool\Framework\Loader\LoaderInterface $loader);

    /**
     * getLoader()
     *
     * @return Zend_Tool_Framework_Loader_Abstract
     */
    public function getLoader();

    /**
     * setActionRepository()
     *
     * @param Zend_Tool_Framework_Action_Repository $actionRepository
     * @return Zend_Tool_Framework_Registry
     */
    public function setActionRepository(\Zend\Tool\Framework\Action\Repository $actionRepository);

    /**
     * getActionRepository()
     *
     * @return Zend_Tool_Framework_Action_Repository
     */
    public function getActionRepository();

    /**
     * setProviderRepository()
     *
     * @param Zend_Tool_Framework_Provider_Repository $providerRepository
     * @return Zend_Tool_Framework_Registry
     */
    public function setProviderRepository(\Zend\Tool\Framework\Provider\Repository $providerRepository);

    /**
     * getProviderRepository()
     *
     * @return Zend_Tool_Framework_Provider_Repository
     */
    public function getProviderRepository();

    /**
     * setManifestRepository()
     *
     * @param Zend_Tool_Framework_Manifest_Repository $manifestRepository
     * @return Zend_Tool_Framework_Registry
     */
    public function setManifestRepository(\Zend\Tool\Framework\Manifest\Repository $manifestRepository);

    /**
     * getManifestRepository()
     *
     * @return Zend_Tool_Framework_Manifest_Repository
     */
    public function getManifestRepository();

    /**
     * setRequest()
     *
     * @param Zend_Tool_Framework_Client_Request $request
     * @return Zend_Tool_Framework_Registry
     */
    public function setRequest(\Zend\Tool\Framework\Client\Request $request);

    /**
     * getRequest()
     *
     * @return Zend_Tool_Framework_Client_Request
     */
    public function getRequest();

    /**
     * setResponse()
     *
     * @param Zend_Tool_Framework_Client_Response $response
     * @return Zend_Tool_Framework_Registry
     */
    public function setResponse(\Zend\Tool\Framework\Client\Response\Response $response);

    /**
     * getResponse()
     *
     * @return Zend_Tool_Framework_Client_Response
     */
    public function getResponse();

}
