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
 * Represents meta data for one translation of a Translate model:
 * Key, value with source and all overridden values and their sources
 * 
 * @package SSE_TranslationHints
 */
class SSE_TranslationHints_Model_Data_Meta
{
    const ADD_MODE_SELECT_IF_FIRST = 0;
    const ADD_MODE_OVERRIDE = 1;
    const ADD_MODE_DO_NOT_SELECT = 2;
    
    protected $_key;
    /**
     * @var SSE_TranslationHints_Model_Data_Value
     */
    protected $_value;
    /**
     * @var SSE_TranslationHints_Model_Data_Value[]
     */
    protected $_values = array();

    public function __construct($key)
    {
        $this->_key = $key;
    }
    public function addValue(SSE_TranslationHints_Model_Data_Value $value, $override)
    {
        $this->_values[] = $value;
        switch ($override) {
        	case self::ADD_MODE_OVERRIDE:
        	    $this->_value = $value;
        	    break;
        	case self::ADD_MODE_SELECT_IF_FIRST:
        	    if (is_null($this->_value)) {
        	        $this->_value = $value;
        	    }
        	    break;
        	case self::ADD_MODE_DO_NOT_SELECT:
        	    break;
        }
        return $this;
    }
    public function unsetValue()
    {
        $this->_value = null;
        return $this;
    }
    public function getKey()
    {
        return $this->_key;
    }
    public function getValue()
    {
        return $this->_value;
    }
    public function getValues()
    {
        return $this->_values;
    }
}