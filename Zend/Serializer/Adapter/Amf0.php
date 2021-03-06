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
 * @package    Zend_Serializer
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Amf0.php 20574 2010-01-24 17:39:14Z mabe $
 */

/**
 * @namespace
 */
namespace Zend\Serializer\Adapter;
use Zend\Serializer;

/** @see Zend_Serializer_Adapter_AdapterAbstract */
require_once 'Zend/Serializer/Adapter/AdapterAbstract.php';

/** @see Zend_Amf_Parse_OutputStream */
require_once 'Zend/Amf/Parse/OutputStream.php';

/** @see Zend_Amf_Parse_Amf0_Serializer */
require_once 'Zend/Amf/Parse/Amf0/Serializer.php';

/** @see Zend_Amf_Parse_InputStream */
require_once 'Zend/Amf/Parse/InputStream.php';

/** @see Zend_Amf_Parse_Amf0_Deserializer */
require_once 'Zend/Amf/Parse/Amf0/Deserializer.php';

/**
 * @category   Zend
 * @package    Zend_Serializer
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Amf0 extends AdapterAbstract
{
    /**
     * Serialize a PHP value to AMF0 format
     * 
     * @param  mixed $value 
     * @param  array $opts 
     * @return string
     * @throws Zend_Serializer_Exception
     */
    public function serialize($value, array $opts = array())
    {
        try  {
            $stream     = new \Zend\Amf\Parse\OutputStream();
            $serializer = new \Zend\Amf\Parse\Amf0\Serializer($stream);
            $serializer->writeTypeMarker($value);
            return $stream->getStream();
        } catch (\Exception $e) {
            require_once 'Zend/Serializer/Exception.php';
            throw new Serializer\Exception('Serialization failed by previous error', 0, $e);
        }
    }

    /**
     * Unserialize an AMF0 value to PHP
     * 
     * @param  mixed $value 
     * @param  array $opts 
     * @return void
     * @throws Zend_Serializer_Exception
     */
    public function unserialize($value, array $opts = array())
    {
        try {
            $stream       = new \Zend\Amf\Parse\InputStream($value);
            $deserializer = new \Zend\Amf\Parse\Amf0\Deserializer($stream);
            return $deserializer->readTypeMarker();
        } catch (\Exception $e) {
            require_once 'Zend/Serializer/Exception.php';
            throw new Serializer\Exception('Unserialization failed by previous error', 0, $e);
        }
    }

}
