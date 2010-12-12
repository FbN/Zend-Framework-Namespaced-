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
 * @subpackage Annotation
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Link.php 20096 2010-01-06 02:05:09Z bkarwin $
 */

/**
 * @namespace
 */
namespace Zend\Pdf\Annotation;
use Zend\Pdf\Element;
use Zend\Pdf;
use Zend\Pdf\Destination;
use Zend\Pdf;
use Zend\Pdf\Element;
use Zend\Pdf\Element;
use Zend\Pdf\Destination;

/** Internally used classes */
require_once 'Zend/Pdf/Element.php';
require_once 'Zend/Pdf/Element/Array.php';
require_once 'Zend/Pdf/Element/Dictionary.php';
require_once 'Zend/Pdf/Element/Name.php';
require_once 'Zend/Pdf/Element/Numeric.php';


/** Zend_Pdf_Annotation */
require_once 'Zend/Pdf/Annotation.php';

/**
 * A link annotation represents either a hypertext link to a destination elsewhere in
 * the document or an action to be performed.
 *
 * Only destinations are used now since only GoTo action can be created by user
 * in current implementation.
 *
 * @package    Zend_Pdf
 * @subpackage Annotation
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Link extends Annotation
{
    /**
     * Annotation object constructor
     *
     * @throws Zend_Pdf_Exception
     */
    public function __construct(Element\Element $annotationDictionary)
    {
        if ($annotationDictionary->getType() != Element\Element::TYPE_DICTIONARY) {
            require_once 'Zend/Pdf/Exception.php';
            throw new Pdf\Exception('Annotation dictionary resource has to be a dictionary.');
        }

        if ($annotationDictionary->Subtype === null  ||
            $annotationDictionary->Subtype->getType() != Element\Element::TYPE_NAME  ||
            $annotationDictionary->Subtype->value != 'Link') {
            require_once 'Zend/Pdf/Exception.php';
            throw new Pdf\Exception('Subtype => Link entry is requires');
        }

        parent::__construct($annotationDictionary);
    }

    /**
     * Create link annotation object
     *
     * @param float $x1
     * @param float $y1
     * @param float $x2
     * @param float $y2
     * @param Zend_Pdf_Target|string $target
     * @return Zend_Pdf_Annotation_Link
     */
    public static function create($x1, $y1, $x2, $y2, $target)
    {
        if (is_string($target)) {
            require_once 'Zend/Pdf/Destination/Named.php';
            $destination = Destination\Named::create($target);
        }
        if (!$target instanceof Pdf\Target) {
            require_once 'Zend/Pdf/Exception.php';
            throw new Pdf\Exception('$target parameter must be a Zend_Pdf_Target object or a string.');
        }

        $annotationDictionary = new Element\Dictionary();

        $annotationDictionary->Type    = new Element\Name('Annot');
        $annotationDictionary->Subtype = new Element\Name('Link');

        $rectangle = new Element\Array();
        $rectangle->items[] = new Element\Numeric($x1);
        $rectangle->items[] = new Element\Numeric($y1);
        $rectangle->items[] = new Element\Numeric($x2);
        $rectangle->items[] = new Element\Numeric($y2);
        $annotationDictionary->Rect = $rectangle;

        if ($target instanceof Destination\Destination) {
            $annotationDictionary->Dest = $target->getResource();
        } else {
            $annotationDictionary->A = $target->getResource();
        }

        return new Link($annotationDictionary);
    }

    /**
     * Set link annotation destination
     *
     * @param Zend_Pdf_Target|string $target
     * @return Zend_Pdf_Annotation_Link
     */
    public function setDestination($target)
    {
        if (is_string($target)) {
            require_once 'Zend/Pdf/Destination/Named.php';
            $destination = Destination\Named::create($target);
        }
        if (!$target instanceof Pdf\Target) {
            require_once 'Zend/Pdf/Exception.php';
            throw new Pdf\Exception('$target parameter must be a Zend_Pdf_Target object or a string.');
        }

        $this->_annotationDictionary->touch();
        $this->_annotationDictionary->Dest = $destination->getResource();
        if ($target instanceof Destination\Destination) {
            $this->_annotationDictionary->Dest = $target->getResource();
            $this->_annotationDictionary->A    = null;
        } else {
            $this->_annotationDictionary->Dest = null;
            $this->_annotationDictionary->A    = $target->getResource();
        }

        return $this;
    }

    /**
     * Get link annotation destination
     *
     * @return Zend_Pdf_Target|null
     */
    public function getDestination()
    {
        if ($this->_annotationDictionary->Dest === null  &&
            $this->_annotationDictionary->A    === null) {
            return null;
        }

        if ($this->_annotationDictionary->Dest !== null) {
            require_once 'Zend/Pdf/Destination.php';
            return Destination\Destination::load($this->_annotationDictionary->Dest);
        } else {
            require_once 'Zend/Pdf/Action.php';
            return Pdf\Action\Action::load($this->_annotationDictionary->A);
        }
    }
}
