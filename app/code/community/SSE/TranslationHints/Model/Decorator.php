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
 * 
 * Example for decorated "Translated String":
 * [__Translated String__](__Modul::Text__:module:path/to/csv=Translated String Version 1|db=Translated String Version 2|__Text__:module:path/to/csv=Global Translated String)
 *
 * @package SSE_TranslationHints
 */
class SSE_TranslationHints_Model_Decorator
{
    /**
     * @var SSE_TranslationHints_Model_Translate
     */
    protected $_translate;
    /**
     * @var SSE_TranslationHints_Model_Translate_Mode
     */
    protected $_mode;

    public function __construct($translate)
    {
        $this->_translate = $translate;
    }
    public function setMode(SSE_TranslationHints_Model_Translate_Mode $mode)
    {
        $this->_mode = $mode;
    }
    public function decorateTranslation($translation, $text, $code)
    {
        $metaData = $this->_translate->getMetaData();
        $formattedMetaData = '';
        if (!isset($metaData[$code])) {
            $metaData[$code] = Mage::getModel('sse_translationhints/data_meta', $code);
        }
        $formattedMetaData .= $this->_formatMetaData($metaData[$code]);
        if ($code != $text && isset($metaData[$text])) {
            $formattedMetaData .= $this->_formatMetaData($metaData[$text]);
        }
        if ($this->_mode->getUseCache()) {
            $formattedMetaData .= '(C)';
        }
        if ($this->_mode->getIsDeveloperMode()) {
            $formattedMetaData .= '(D)';
        }
        return sprintf('[__%s__]((%s))', $translation, str_replace('%', '%%', $formattedMetaData));
    }
    protected function _formatMetaData(SSE_TranslationHints_Model_Data_Meta $metaData)
    {
        $formattedMetaData = sprintf('__(%s):', $metaData->getKey());
        foreach ($metaData->getValues() as $value) {
            /* @var $value SSE_TranslationHints_Model_Data_Value */
            $checkmark = '';
            if ($value === $metaData->getValue()) {
                $checkmark = '(x)'; // used
            } elseif ($value->getValue() === $metaData->getKey()) {
                $checkmark = '(!)'; // ignored
            }
            $formattedMetaData .= sprintf("%s%s%s = %s|",
                $checkmark,
                $value->getSourceType(),
                $value->getSourceFile() ? ': ' . $value->getSourceFile() : '',
                $value->getValue());
        }
        return $formattedMetaData;
    }
}