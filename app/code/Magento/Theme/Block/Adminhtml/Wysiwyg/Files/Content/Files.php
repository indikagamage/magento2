<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Magento
 * @package     Magento_Theme
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Files files block
 *
 * @method
 *  \Magento\Theme\Block\Adminhtml\Wysiwyg\Files\Content\Files setStorage(\Magento\Theme\Model\Wysiwyg\Storage $storage)
 * @method \Magento\Theme\Model\Wysiwyg\Storage getStorage
 */
namespace Magento\Theme\Block\Adminhtml\Wysiwyg\Files\Content;

class Files extends \Magento\Backend\Block\Template
{
    /**
     * Files list
     *
     * @var null|array
     */
    protected $_files;

    /**
     * Get files
     *
     * @return array
     */
    public function getFiles()
    {
        if (null === $this->_files && $this->getStorage()) {
            $this->_files = $this->getStorage()->getFilesCollection();
        }

        return $this->_files;
    }

    /**
     * Get files count
     *
     * @return int
     */
    public function getFilesCount()
    {
        return count($this->getFiles());
    }
}
