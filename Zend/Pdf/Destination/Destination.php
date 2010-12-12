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
 * @package    Zend_Pdf
 * @subpackage Destination
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Destination.php 20096 2010-01-06 02:05:09Z bkarwin $
 */

/**
 * @namespace
 */
namespace Zend\Pdf\Destination;
use Zend\Pdf\Element;
use Zend\Pdf;

/** Internally used classes */
require_once 'Zend/Pdf/Element.php';


/** Zend_Pdf_Target */
require_once 'Zend/Pdf/Target.php';


/**
 * Abstract PDF destination representation class
 *
 * @package    Zend_Pdf
 * @subpackage Destination
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Destination extends Pdf\Target
{
    /**
     * Load Destination object from a specified resource
     *
     * @internal
     * @param $destinationArray
     * @return Zend_Pdf_Destination
     */
    public static function load(Element\Element $resource)
    {
        require_once 'Zend/Pdf/Element.php';
        if ($resource->getType() == Element\Element::TYPE_NAME  ||  $resource->getType() == Element\Element::TYPE_STRING) {
            require_once 'Zend/Pdf/Destination/Named.php';
            return new Named($resource);
        }

        if ($resource->getType() != Element\Element::TYPE_ARRAY) {
            require_once 'Zend/Pdf/Exception.php';
            throw new Pdf\Exception('An explicit destination must be a direct or an indirect array object.');
        }
        if (count($resource->items) < 2) {
            require_once 'Zend/Pdf/Exception.php';
            throw new Pdf\Exception('An explicit destination array must contain at least two elements.');
        }

        switch ($resource->items[1]->value) {
            case 'XYZ':
                require_once 'Zend/Pdf/Destination/Zoom.php';
                return new Zoom($resource);
                break;

            case 'Fit':
                require_once 'Zend/Pdf/Destination/Fit.php';
                return new Fit($resource);
                break;

            case 'FitH':
                require_once 'Zend/Pdf/Destination/FitHorizontally.php';
                return new FitHorizontally($resource);
                break;

            case 'FitV':
                require_once 'Zend/Pdf/Destination/FitVertically.php';
                return new FitVertically($resource);
                break;

            case 'FitR':
                require_once 'Zend/Pdf/Destination/FitRectangle.php';
                return new FitRectangle($resource);
                break;

            case 'FitB':
                require_once 'Zend/Pdf/Destination/FitBoundingBox.php';
                return new FitBoundingBox($resource);
                break;

            case 'FitBH':
                require_once 'Zend/Pdf/Destination/FitBoundingBoxHorizontally.php';
                return new FitBoundingBoxHorizontally($resource);
                break;

            case 'FitBV':
                require_once 'Zend/Pdf/Destination/FitBoundingBoxVertically.php';
                return new FitBoundingBoxVertically($resource);
                break;

            default:
                require_once 'Zend/Pdf/Destination/Unknown.php';
                return new Unknown($resource);
                break;
        }
    }
}
