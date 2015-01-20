<?php
/**
 * Represents meta data for one translation of a Translate model:
 * Key, value with source and all overridden values and their sources
 */
class SSE_TranslationHints_Model_Data_Meta
{
    protected $_key;
    protected $_value;
    protected $_values = array();

    public function __construct($key)
    {
        $this->_key = $key;
    }
    public function addValue(SSE_TranslationHints_Model_Data_Value $value, $override)
    {
        $this->_values[] = $value;
        if ($override || is_null($this->_value)) {
            $this->_value = $value;
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