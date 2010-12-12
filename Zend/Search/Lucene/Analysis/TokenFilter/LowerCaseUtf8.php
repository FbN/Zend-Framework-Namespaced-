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
 * @package    Zend_Search_Lucene
 * @subpackage Analysis
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: LowerCaseUtf8.php 20096 2010-01-06 02:05:09Z bkarwin $
 */

/**
 * @namespace
 */
namespace Zend\Search\Lucene\Analysis\TokenFilter;
use Zend\Search\Lucene\Analysis;

/** Zend_Search_Lucene_Analysis_TokenFilter */
require_once 'Zend/Search/Lucene/Analysis/TokenFilter.php';


/**
 * Lower case Token filter.
 *
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Analysis
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

class LowerCaseUtf8 extends TokenFilter
{
    /**
     * Object constructor
     */
    public function __construct()
    {
        if (!function_exists('mb_strtolower')) {
            // mbstring extension is disabled
            require_once 'Zend/Search/Lucene/Exception.php';
            throw new \Zend\Search\Lucene\Exception('Utf8 compatible lower case filter needs mbstring extension to be enabled.');
        }
    }

    /**
     * Normalize Token or remove it (if null is returned)
     *
     * @param Zend_Search_Lucene_Analysis_Token $srcToken
     * @return Zend_Search_Lucene_Analysis_Token
     */
    public function normalize(Analysis\Token $srcToken)
    {
        $newToken = new Analysis\Token(
                                     mb_strtolower($srcToken->getTermText(), 'UTF-8'),
                                     $srcToken->getStartOffset(),
                                     $srcToken->getEndOffset());

        $newToken->setPositionIncrement($srcToken->getPositionIncrement());

        return $newToken;
    }
}

