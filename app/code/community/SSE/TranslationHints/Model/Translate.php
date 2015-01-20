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
 * Translate Model, rewritten to add translation hints to translated strings
 * 
 * @package SSE_TranslationHints
 * @property SSE_TranslationHints_Model_Data $_data
 */
class SSE_TranslationHints_Model_Translate extends Mage_Core_Model_Translate
{
    const XML_HINTS_ENABLED = 'dev/debug/translation_hints';
    /**
     * @var SSE_TranslationHints_Model_Translate_Mode
     */
    protected $_mode;
    /**
     * @var SSE_TranslationHints_Helper_Decorator
     */
    protected $_decorator;
    
    public function __construct()
    {
        $this->_mode = Mage::getModel('sse_translationhints/translate_mode');
        $this->_decorator = Mage::getModel('sse_translationhints/decorator', $this);
        $this->_decorator->setMode($this->_mode);
    }
    /**
     * @return SSE_TranslationHints_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('sse_translationhints');
    }
    /**
     * @return bool
     */
    public function getTranslationHintsEnabled()
    {
        //TODO make it impossible together with _translateInline
        return $this->_helper()->isModuleOutputEnabled() && Mage::getStoreConfigFlag(self::XML_HINTS_ENABLED);
    }
    /**
     * (non-PHPdoc) Overridden to set mode
     * @see Mage_Core_Model_Translate::_loadDbTranslation()
     */
    protected function _loadDbTranslation($forceReload=false)
    {
        $this->_mode->setCurrentSourceType(SSE_TranslationHints_Model_Translate_Mode::SOURCE_DB);
        $this->_mode->setCurrentSourceFile(null);
        return parent::_loadDbTranslation($forceReload);
    }
    /**
     * (non-PHPdoc) Overridden to set mode
     * @see Mage_Core_Model_Translate::_loadModuleTranslation()
     */
    protected function _loadModuleTranslation($moduleName, $files, $forceReload=false)
    {
        $this->_mode->setCurrentSourceType(SSE_TranslationHints_Model_Translate_Mode::SOURCE_MODULE);
        foreach ($files as $file) {
            $file = $this->_getModuleFilePath($moduleName, $file);
            $this->_mode->setCurrentSourceFile(substr($file, strlen(Mage::getBaseDir('locale'))));
            $this->_addData($this->_getFileData($file), $moduleName, $forceReload);
        }
        return $this;
    }
    /**
     * (non-PHPdoc) Overridden to set mode
     * @see Mage_Core_Model_Translate::_loadThemeTranslation()
     */
    protected function _loadThemeTranslation($forceReload=false)
    {
        $this->_mode->setCurrentSourceType(SSE_TranslationHints_Model_Translate_Mode::SOURCE_THEME);
        $this->_mode->setCurrentSourceFile(substr(Mage::getDesign()->getLocaleFileName('translate.csv'), strlen(Mage::getBaseDir('design'))));
        return parent::_loadThemeTranslation($forceReload);
    }

    /**
     * (non-PHPdoc) Overridden to force reload if translation hints enabled and cache does not contain meta data
     * @see Mage_Core_Model_Translate::_loadCache()
     */
    protected function _loadCache()
    {
        $result = parent::_loadCache();
        if ($this->getTranslationHintsEnabled() && !($result instanceof SSE_TranslationHints_Model_Data)) {
            return false;
        }
        if ($result !== false) {
            $this->_mode->setUseCache(true);
        }
        return $result;
    }
    /**
     * (non-PHPdoc) Overridden to convert data to object with meta information
     * @see Mage_Core_Model_Translate::_addData()
     */
    protected function _addData($data, $scope, $forceReload=false)
    {
        if ($this->getTranslationHintsEnabled() && is_array($this->_data)) {
            $this->_data = Mage::getModel('sse_translationhints/data', $this->_data);
            $this->_data->setDataScope($this->_dataScope);
            $this->_data->setMode($this->_mode);
        }
        /*
         * If $key equals $value, Magento does not save the translations.
         * We add the source to the metadata anyway:
         */
        if ($this->getTranslationHintsEnabled()) {
            foreach ($data as $key => $value) {
                if ($key === $value) {
                    $this->_data->logMetaData($key, $value, false);
                }
            }
        }
        // kurz rekapituliert: wenn übersetzung nur einmal definiert ist, gilt sie überall (key ohne scope)
        //  wenn sie mehrmals in unterschiedlichem scope definiert ist, gilt sie jeweils nur in diesem scope (key mit scope)
        //  wenn dabei der developer mode ausgeschaltet ist, gilt außerdem die erste fassung überall
        return parent::_addData($data, $scope, $forceReload);
    }
    public function getData()
    {
        if ($this->_data instanceof SSE_TranslationHints_Model_Data) {
            return $this->_data->getData();
        }
        return parent::getData();
    }
    public function getMetaData()
    {
        if ($this->_data instanceof SSE_TranslationHints_Model_Data) {
            return $this->_data->getMetadata();
        }
        return array();
    }
    protected function _getTranslatedString($text, $code)
    {
        $result = parent::_getTranslatedString($text, $code);
        if ($this->getTranslationHintsEnabled()) {
            return $this->_decorator->decorateTranslation($result, $text, $code);
        }
        return $result;
    }
}