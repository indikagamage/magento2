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
 * @package     Magento_Backend
 * @subpackage  unit_tests
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Magento\Backend\Model\Config\Structure\Element\Dependency;

class MapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Field prefix
     */
    const FIELD_PREFIX = 'prefix_';

    /**
     * Value in store
     */
    const VALUE_IN_STORE = 'value in store';

    /**#@+
     * Field ids
     */
    const FIELD_ID1 = 'field id 1';

    const FIELD_ID2 = 'field id 2';

    /**#@-*/

    /**
     * Store code
     */
    const STORE_CODE = 'some store code';

    /**
     * @var \Magento\Backend\Model\Config\Structure\Element\Dependency\Mapper
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configStructureMock;

    /**
     * Test data
     *
     * @var array
     */
    protected $_testData;

    /**
     * Mock of dependency field factory
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fieldFactoryMock;

    protected function setUp()
    {
        $this->_testData = array(
            'field_x' => array('id' => self::FIELD_ID1),
            'field_y' => array('id' => self::FIELD_ID2)
        );

        $this->_configStructureMock = $this->getMockBuilder(
            'Magento\Backend\Model\Config\Structure'
        )->setMethods(
            array('getElement')
        )->disableOriginalConstructor()->getMock();
        $this->_fieldFactoryMock = $this->getMockBuilder(
            'Magento\Backend\Model\Config\Structure\Element\Dependency\FieldFactory'
        )->setMethods(
            array('create')
        )->disableOriginalConstructor()->getMock();
        $this->_scopeConfigMock = $this->getMockBuilder(
            '\Magento\App\Config\ScopeConfigInterface'
        )->disableOriginalConstructor()->getMock();
        $this->_model = new \Magento\Backend\Model\Config\Structure\Element\Dependency\Mapper(
            $this->_configStructureMock,
            $this->_fieldFactoryMock,
            $this->_scopeConfigMock
        );
    }

    protected function tearDown()
    {
        unset($this->_model);
        unset($this->_configStructureMock);
        unset($this->_fieldFactoryMock);
        unset($this->_testData);
    }

    /**
     * @param bool $isValueSatisfy
     * @dataProvider getDependenciesDataProvider
     */
    public function testGetDependenciesWhenDependentIsInvisible($isValueSatisfy)
    {
        $expected = array();
        $rowData = array_values($this->_testData);
        for ($i = 0; $i < count($this->_testData); ++$i) {
            $data = $rowData[$i];
            $dependentPath = 'some path ' . $i;
            $field = $this->_getField(
                false,
                $dependentPath,
                'Magento_Backend_Model_Config_Structure_Element_Field_' . (string)$isValueSatisfy . $i
            );
            $this->_configStructureMock->expects(
                $this->at($i)
            )->method(
                'getElement'
            )->with(
                $data['id']
            )->will(
                $this->returnValue($field)
            );
            $dependencyField = $this->_getDependencyField(
                $isValueSatisfy,
                false,
                $data['id'],
                'Magento_Backend_Model_Config_Structure_Element_Dependency_Field_' . (string)$isValueSatisfy . $i
            );
            $this->_fieldFactoryMock->expects(
                $this->at($i)
            )->method(
                'create'
            )->with(
                array('fieldData' => $data, 'fieldPrefix' => self::FIELD_PREFIX)
            )->will(
                $this->returnValue($dependencyField)
            );
            $this->_scopeConfigMock->expects(
                $this->at($i)
            )->method(
                'getValue'
            )->with(
                $dependentPath,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                self::STORE_CODE
            )->will(
                $this->returnValue(self::VALUE_IN_STORE)
            );
            if (!$isValueSatisfy) {
                $expected[$data['id']] = $dependencyField;
            }
        }
        $actual = $this->_model->getDependencies($this->_testData, self::STORE_CODE, self::FIELD_PREFIX);
        $this->assertEquals($expected, $actual);
    }

    public function getDependenciesDataProvider()
    {
        return array(array(true), array(false));
    }

    public function testGetDependenciesIsVisible()
    {
        $expected = array();
        $rowData = array_values($this->_testData);
        for ($i = 0; $i < count($this->_testData); ++$i) {
            $data = $rowData[$i];
            $field = $this->_getField(
                true,
                'some path',
                'Magento_Backend_Model_Config_Structure_Element_Field_visible_' . $i
            );
            $this->_configStructureMock->expects(
                $this->at($i)
            )->method(
                'getElement'
            )->with(
                $data['id']
            )->will(
                $this->returnValue($field)
            );
            $dependencyField = $this->_getDependencyField(
                (bool)$i,
                true,
                $data['id'],
                'Magento_Backend_Model_Config_Structure_Element_Dependency_Field_visible_' . $i
            );
            $this->_fieldFactoryMock->expects(
                $this->at($i)
            )->method(
                'create'
            )->with(
                array('fieldData' => $data, 'fieldPrefix' => self::FIELD_PREFIX)
            )->will(
                $this->returnValue($dependencyField)
            );
            $expected[$data['id']] = $dependencyField;
        }
        $actual = $this->_model->getDependencies($this->_testData, self::STORE_CODE, self::FIELD_PREFIX);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Get dependency field mock
     *
     * @param bool $isValueSatisfy
     * @param bool $isFieldVisible
     * @param string $fieldId
     * @param string $mockClassName
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getDependencyField($isValueSatisfy, $isFieldVisible, $fieldId, $mockClassName)
    {
        $field = $this->getMockBuilder(
            'Magento\Backend\Model\Config\Structure\Element\Dependency\Field'
        )->setMethods(
            array('isValueSatisfy', 'getId')
        )->setMockClassName(
            $mockClassName
        )->disableOriginalConstructor()->getMock();
        if ($isFieldVisible) {
            $field->expects($isFieldVisible ? $this->never() : $this->once())->method('isValueSatisfy');
        } else {
            $field->expects(
                $this->once()
            )->method(
                'isValueSatisfy'
            )->with(
                self::VALUE_IN_STORE
            )->will(
                $this->returnValue($isValueSatisfy)
            );
        }
        $field->expects(
            $isFieldVisible || !$isValueSatisfy ? $this->once() : $this->never()
        )->method(
            'getId'
        )->will(
            $this->returnValue($fieldId)
        );
        return $field;
    }

    /**
     * Get field mock
     *
     * @param bool $isVisible
     * @param string $path
     * @param string $mockClassName
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getField($isVisible, $path, $mockClassName)
    {
        $field = $this->getMockBuilder(
            'Magento\Backend\Model\Config\Structure\Element\Field'
        )->setMethods(
            array('isVisible', 'getPath')
        )->setMockClassName(
            $mockClassName
        )->disableOriginalConstructor()->getMock();
        $field->expects($this->once())->method('isVisible')->will($this->returnValue($isVisible));
        if ($isVisible) {
            $field->expects($this->never())->method('getPath');
        } else {
            $field->expects(
                $isVisible ? $this->never() : $this->once()
            )->method(
                'getPath'
            )->with(
                self::FIELD_PREFIX
            )->will(
                $this->returnValue($path)
            );
        }
        return $field;
    }
}
