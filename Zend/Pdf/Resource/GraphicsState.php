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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Image.php 20096 2010-01-06 02:05:09Z bkarwin $
 */

/**
 * @namespace
 */
namespace Zend\Pdf\Resource;
use Zend\Pdf\Element;
use Zend\Pdf;
use Zend\Pdf\Element;

/** Internally used classes */
require_once 'Zend/Pdf/Element/Object.php';
require_once 'Zend/Pdf/Element/Dictionary.php';
require_once 'Zend/Pdf/Element/Name.php';
require_once 'Zend/Pdf/Element/Numeric.php';


/** Zend_Pdf_Resource */
require_once 'Zend/Pdf/Resource.php';


/**
 * Graphics State.
 *
 * While some parameters in the graphics state can be set with individual operators,
 * as shown in Table 4.7, others cannot. The latter can only be set with the generic
 * graphics state operator gs (PDF 1.2).
 *
 * @package    Zend_Pdf
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class GraphicsState extends Resource
{
    /**
     * Object constructor.
     *
     * @param Zend_Pdf_Element_Object $extGStateObject
     * @throws Zend_Pdf_Exception
     */
    public function __construct(Element\Object\Object $extGStateObject = null)
    {
        if ($extGStateObject == null) {
            // Create new Graphics State object
            require_once 'Zend/Pdf/ElementFactory.php';
            $factory = ElementFactory\ElementFactory::createFactory(1);

            $gsDictionary = new Element\Dictionary();
            $gsDictionary->Type = new Element\Name('ExtGState');

            $extGStateObject = $factory->newObject($gsDictionary);
        }

        if ($extGStateObject->getType() != Element\Element::TYPE_DICTIONARY) {
            require_once 'Zend/Pdf/Exception.php';
            throw new Pdf\Exception('Graphics state PDF object must be a dictionary');
        }

        parent::__construct($gsDictionary);
    }

    /**
     * Set the transparancy
     *
     * $alpha == 0  - transparent
     * $alpha == 1  - opaque
     *
     * Transparency modes, supported by PDF:
     * Normal (default), Multiply, Screen, Overlay, Darken, Lighten, ColorDodge, ColorBurn, HardLight,
     * SoftLight, Difference, Exclusion
     *
     * @param float $alpha
     * @param string $mode
     * @throws Zend_Pdf_Exception
     * @return Zend_Pdf_Canvas_Interface
     */
    public function setAlpha($alpha, $mode = 'Normal')
    {
        if (!in_array($mode, array('Normal', 'Multiply', 'Screen', 'Overlay', 'Darken', 'Lighten', 'ColorDodge',
                                   'ColorBurn', 'HardLight', 'SoftLight', 'Difference', 'Exclusion'))) {
            require_once 'Zend/Pdf/Exception.php';
            throw new Pdf\Exception('Unsupported transparency mode.');
        }
        if (!is_numeric($alpha)  ||  $alpha < 0  ||  $alpha > 1) {
            require_once 'Zend/Pdf/Exception.php';
            throw new Pdf\Exception('Alpha value must be numeric between 0 (transparent) and 1 (opaque).');
        }

        $this->_resource->BM   = new Element\Name($mode);
        $this->_resource->CA   = new Element\Numeric($alpha);
        $this->_resource->ca   = new Element\Numeric($alpha);
    }


    /** @todo add other Graphics State features support */
}

