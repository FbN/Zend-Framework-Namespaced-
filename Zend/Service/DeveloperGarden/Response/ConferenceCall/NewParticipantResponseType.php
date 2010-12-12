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
 * @package    Zend_Service
 * @subpackage DeveloperGarden
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: NewParticipantResponseType.php 20166 2010-01-09 19:00:17Z bkarwin $
 */

/**
 * @namespace
 */
namespace Zend\Service\DeveloperGarden\Response\ConferenceCall;

/**
 * @see Zend_Service_DeveloperGarden_Response_ConferenceCall_ConferenceCallAbstract
 */
require_once 'Zend/Service/DeveloperGarden/Response/BaseType.php';

/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage DeveloperGarden
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @author     Marco Kaiser
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class NewParticipantResponseType
    extends \Zend\Service\DeveloperGarden\Response\BaseType
{
    /**
     * the participant Id
     *
     * @var string
     */
    public $participantId = null;

    /**
     * return the participant id
     *
     * @return string
     */
    public function getParticipantId()
    {
        return $this->participantId;
    }
}