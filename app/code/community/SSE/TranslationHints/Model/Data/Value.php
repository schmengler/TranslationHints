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
 * Represents one translation value within the translation meta data: Value and source
 * 
 * @package SSE_TranslationHints
 */
class SSE_TranslationHints_Model_Data_Value
{
    protected $_value;
    protected $_sourceType;
    protected $_sourceFile;

    public function __construct($value, $sourceType, $sourceFile)
    {
        $this->_value = $value;
        $this->_sourceType = $sourceType;
        $this->_sourceFile = $sourceFile;
    }

    public function getValue()
    {
        return $this->_value;
    }
    public function getSourceType()
    {
        return $this->_sourceType;
    }
    public function getSourceFile()
    {
        return $this->_sourceFile;
    }
}