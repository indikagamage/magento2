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
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Magento\App\Config;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\Config\Data
     */
    protected $_model;

    /**
     * @var \Magento\App\Config\MetadataProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_metaDataProcessor;

    protected function setUp()
    {
        $this->_metaDataProcessor = $this->getMock(
            'Magento\App\Config\MetadataProcessor',
            array(),
            array(),
            '',
            false
        );
        $this->_metaDataProcessor->expects($this->any())->method('process')->will($this->returnArgument(0));
        $this->_model = new \Magento\App\Config\Data($this->_metaDataProcessor, array());
    }

    /**
     * @param string $path
     * @param mixed $value
     * @dataProvider setValueDataProvider
     */
    public function testSetValue($path, $value)
    {
        $this->_model->setValue($path, $value);
        $this->assertEquals($value, $this->_model->getValue($path));
    }

    public function setValueDataProvider()
    {
        return array(
            'simple value' => array('some/config/value', 'test'),
            'complex value' => array('some/config/value', array('level1' => array('level2' => 'test')))
        );
    }

    public function testGetData()
    {
        $model = new \Magento\App\Config\Data($this->_metaDataProcessor, array('test' => array('path' => 'value')));
        $this->assertEquals('value', $model->getValue('test/path'));
    }
}
