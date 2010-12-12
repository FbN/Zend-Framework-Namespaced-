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
 * @package    Zend_Feed_Reader
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Entry.php 22300 2010-05-26 10:13:34Z padraic $
 */

/**
 * @namespace
 */
namespace Zend\Feed\Reader\Extension\Content;
use Zend\Feed\Reader;

/**
 * @see Zend_Feed_Reader
 */
require_once 'Zend/Feed/Reader.php';

/**
 * @see Zend_Feed_Reader_Entry_EntryAbstract
 */
require_once 'Zend/Feed/Reader/Extension/EntryAbstract.php';

/**
 * @category   Zend
 * @package    Zend_Feed_Reader
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Entry
    extends Reader\Extension\EntryAbstract
{

    public function getContent()
    {
        if ($this->getType() !== Reader\Reader::TYPE_RSS_10
            && $this->getType() !== Reader\Reader::TYPE_RSS_090
        ) {
            $content = $this->_xpath->evaluate('string('.$this->getXpathPrefix().'/content:encoded)');
        } else {
            $content = $this->_xpath->evaluate('string('.$this->getXpathPrefix().'/content:encoded)');
        }
        return $content;
    }

    /**
     * Register RSS Content Module namespace
     */
    protected function _registerNamespaces()
    {
        $this->_xpath->registerNamespace('content', 'http://purl.org/rss/1.0/modules/content/');
    }
}
