<?php
/**
 * This file is part of SSE_TranslationHints for Magento.
 *
 * @license http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @author Fabian Schmengler <fabian@schmengler-se.de>
 * @category SSE
 * @package SSE_TranslationHints
 * @copyright Copyright (c) 2015 Schmengler Software Engineering (http://www.schmengler-se.de/)
 */

/**
 * Translation data with meta information
 * 
 * @package SSE_TranslationHints
 */
class SSE_TranslationHints_Model_Data implements Serializable, ArrayAccess
{
    protected $_mode;
    protected $_dataScope;
    /**
     * @var string[]
     */
    protected $_data = array();
    /**
     * @var SSE_TranslationHints_Model_Data_Meta[]
     */
    protected $_metaData = array();
    protected $_fromCache;

    public function __construct($data)
    {
        $this->_data = $data;
        $this->_fromCache = false;
    }
    public function unserialize($serialized)
    {
        list($this->_data, $this->_metaData) = unserialize($serialized);
        $this->_fromCache = true;
    }
    public function serialize()
    {
        return serialize([$this->_data, $this->_metaData]);
    }
    public function setMode(SSE_TranslationHints_Model_Translate_Mode $mode)
    {
        $this->_mode = $mode;
    }
    public function setDataScope(&$dataScope)
    {
        $this->_dataScope =& $dataScope;
    }
    public function getData()
    {
        return $this->_data;
    }
    public function getMetadata()
    {
        return $this->_metaData;
    }
    public function offsetExists($offset)
    {
        return isset($this->_data[$offset]);
    }
    public function offsetGet($offset)
    {
        return $this->_data[$offset];
    }
    public function offsetSet($offset, $value)
    {
        $this->logMetaData($offset, $value, true);
        $this->_data[$offset] = $value;
    }
    public function offsetUnset($offset)
    {
        $this->_metaData[$offset]->unsetValue();
        unset($this->_data[$offset]);
    }
    public function logMetaData($key, $value, $override)
    {
        if (!isset($this->_metaData[$key])) {
            $this->_metaData[$key] = new SSE_TranslationHints_Model_Data_Meta($key);
        }
        $valueObject = new SSE_TranslationHints_Model_Data_Value(
            $value, $this->_mode->getCurrentSourceType(), $this->_mode->getCurrentSourceFile()
        );
        if (strpos($key, Mage_Core_Model_Translate::SCOPE_SEPARATOR)) {
            list($scope, $keyWithoutScope) = explode(Mage_Core_Model_Translate::SCOPE_SEPARATOR, $key, 2);
            // find out if old value is being copied to scope, then copy value object
            // - see Mage_Core_Model_Translate::_addData()
            if (isset($this->_dataScope[$keyWithoutScope])
                && $this->_dataScope[$keyWithoutScope] === $scope
                && isset($this->_metaData[$keyWithoutScope])
                && null !== $this->_metaData[$keyWithoutScope]->getValue()
            ) {
                $oldValueObject = $this->_metaData[$keyWithoutScope]->getValue();
                $this->_metaData[$key]->addValue(
                    $oldValueObject, SSE_TranslationHints_Model_Data_Meta::ADD_MODE_OVERRIDE
                );
            // else log key without scope as "unused"
            } else {
                $this->logMetaDataUnused($keyWithoutScope, $value);
            }
        }
        $this->_metaData[$key]->addValue(
            $valueObject,
            $override ? SSE_TranslationHints_Model_Data_Meta::ADD_MODE_OVERRIDE
                : SSE_TranslationHints_Model_Data_Meta::ADD_MODE_SELECT_IF_FIRST
        );
        return $this;
    }
    public function logMetaDataUnused($key, $value)
    {
        if (!isset($this->_metaData[$key])) {
            $this->_metaData[$key] = new SSE_TranslationHints_Model_Data_Meta($key);
        }
        $valueObject = new SSE_TranslationHints_Model_Data_Value(
            $value, $this->_mode->getCurrentSourceType(), $this->_mode->getCurrentSourceFile()
        );
        $this->_metaData[$key]->addValue(
            $valueObject,
            SSE_TranslationHints_Model_Data_Meta::ADD_MODE_DO_NOT_SELECT
        );
        return $this;
    }
}