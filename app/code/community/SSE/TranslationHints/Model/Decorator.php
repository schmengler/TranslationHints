<?php
/**
 * 
 * Example for decorated "Translated String":
 * [__Translated String__](__Modul::Text__:module:path/to/csv=Translated String Version 1|db=Translated String Version 2|__Text__:module:path/to/csv=Global Translated String)
 *
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
        if (isset($metaData[$code])) {
            $formattedMetaData .= $this->_formatMetaData($metaData[$code]);
        }
        if ($code != $text && isset($metaData[$text])) {
            $formattedMetaData .= $this->_formatMetaData($metaData[$text]);
        }
        //FIXME (cached) information is missing or data is not being cached
        if ($this->_mode->getUseCache()) {
            $formattedMetaData .= '(cached)';
        }
        return sprintf('[__%s__]((%s))', $translation, str_replace('%', '%%', $formattedMetaData));
    }
    protected function _formatMetaData(SSE_TranslationHints_Model_Data_Meta $metaData)
    {
        $formattedMetaData = sprintf('__(%s):', $metaData->getKey());
        foreach ($metaData->getValues() as $value) {
            /* @var $value SSE_TranslationHints_Model_Data_Value */
            $formattedMetaData .= sprintf("%s%s%s=%s|",
                $value === $metaData->getValue() ? '(x) ' : '',
                $value->getSourceType(),
                $value->getSourceFile() ? ': ' . $value->getSourceFile() : '',
                $value->getValue());
        }
        return $formattedMetaData;
    }
}