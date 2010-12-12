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
 * @subpackage Actions
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: GoTo.php 20096 2010-01-06 02:05:09Z bkarwin $
 */

/**
 * @namespace
 */
namespace Zend\Pdf\Action;
use Zend\Pdf\Destination;
use Zend\Pdf\Element;

/** Internally used classes */
require_once 'Zend/Pdf/Destination.php';

require_once 'Zend/Pdf/Element/Dictionary.php';
require_once 'Zend/Pdf/Element/Name.php';


/** Zend_Pdf_Action */
require_once 'Zend/Pdf/Action.php';

/**
 * PDF 'Go to' action
 *
 * @package    Zend_Pdf
 * @subpackage Actions
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class GoTo extends Action
{
    /**
     * GoTo Action destination
     *
     * @var Zend_Pdf_Destination
     */
    protected $_destination;


    /**
     * Object constructor
     *
     * @param Zend_Pdf_Element_Dictionary $dictionary
     * @param SplObjectStorage            $processedActions  list of already processed action dictionaries, used to avoid cyclic references
     */
    public function __construct(Element\Element $dictionary, \SplObjectStorage $processedActions)
    {
        parent::__construct($dictionary, $processedActions);

        $this->_destination = Destination\Destination::load($dictionary->D);
    }

    /**
     * Create new Zend_Pdf_Action_GoTo object using specified destination
     *
     * @param Zend_Pdf_Destination|string $destination
     * @return Zend_Pdf_Action_GoTo
     */
    public static function create($destination)
    {
        if (is_string($destination)) {
            require_once 'Zend/Pdf/Destination/Named.php';
            $destination = Destination\Named::create($destination);
        }

        if (!$destination instanceof Destination\Destination) {
            require_once 'Zend/Pdf/Exception.php';
            throw new \Zend\Pdf\Exception('$destination parameter must be a Zend_Pdf_Destination object or string.');
        }

        $dictionary = new Element\Dictionary();
        $dictionary->Type = new Element\Name('Action');
        $dictionary->S    = new Element\Name('GoTo');
        $dictionary->Next = null;
        $dictionary->D    = $destination->getResource();

        return new GoTo($dictionary, new \SplObjectStorage());
    }

    /**
     * Set goto action destination
     *
     * @param Zend_Pdf_Destination|string $destination
     * @return Zend_Pdf_Action_GoTo
     */
    public function setDestination(Destination\Destination $destination)
    {
        $this->_destination = $destination;

        $this->_actionDictionary->touch();
        $this->_actionDictionary->D = $destination->getResource();

        return $this;
    }

    /**
     * Get goto action destination
     *
     * @return Zend_Pdf_Destination
     */
    public function getDestination()
    {
        return $this->_destination;
    }
}
