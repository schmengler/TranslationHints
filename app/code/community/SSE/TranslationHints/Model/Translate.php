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
 */
class SSE_TranslationHints_Model_Translate extends Mage_Core_Model_Translate
{
    const XML_HINTS_ENABLED = 'dev/debug/translation_hints';

    const MODE_DB = 'db';
    const MODE_MODULE = 'module';
    const MODE_THEME = 'theme';
    
    /**
     * @var string Mode, defines source of currently added translation data
     */
    protected $_currentMode;
    /**
     * @var string File of currently added translation data (if mode is not MODE_DB)
     */
    protected $_currentFile;

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
    public function isEnabled()
    {
        return $this->_helper()->isModuleOutputEnabled() && Mage::getStoreConfigFlag(self::XML_HINTS_ENABLED);
    }
    /**
     * (non-PHPdoc) Overridden to set mode
     * @see Mage_Core_Model_Translate::_loadDbTranslation()
     */
    protected function _loadDbTranslation($forceReload=false)
    {
        $this->_currentMode = self::MODE_DB;
        $this->_currentFile = null;
        return parent::_loadDbTranslation($forceReload);
    }
    /**
     * (non-PHPdoc) Overridden to set mode
     * @see Mage_Core_Model_Translate::_loadModuleTranslation()
     */
    protected function _loadModuleTranslation($moduleName, $files, $forceReload=false)
    {
        $this->_currentMode = self::MODE_MODULE;
        foreach ($files as $file) {
            $file = $this->_getModuleFilePath($moduleName, $file);
            $this->_currentFile = substr($file, strlen(Mage::getBaseDir('locale')));
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
        $this->_currentMode = self::MODE_THEME;
        $this->_currentFile = substr(Mage::getDesign()->getLocaleFileName('translate.csv'), strlen(Mage::getBaseDir('design')));
        return parent::_loadThemeTranslation($forceReload);
    }
    /**
     * (non-PHPdoc) Overridden to set mode
     * @see Mage_Core_Model_Translate::_loadCache()
     */
    protected function _loadCache()
    {
        $cache = parent::_loadCache();
        if ($cache !== false) {
            $cache = array_map([$this, '_decorateCacheHint'], $cache);
        }
        return $cache;
    }

    /**
     * (non-PHPdoc) Overridden to add _decorateTranslationHint()
     * 
     * @see Mage_Core_Model_Translate::_addData()
     */
    protected function _addData($data, $scope, $forceReload=false)
    {
        //TODO collect information about fallbacks and overrides
        foreach ($data as $key => $value) {
            if ($key === $value) {
                continue;
            }
            $key    = $this->_prepareDataString($key);
            $value  = $this->_decorateTranslationHint($this->_prepareDataString($value));
            if ($scope && isset($this->_dataScope[$key]) && !$forceReload ) {
                /**
                 * Checking previos value
                 */
                $scopeKey = $this->_dataScope[$key] . self::SCOPE_SEPARATOR . $key;
                if (!isset($this->_data[$scopeKey])) {
                    if (isset($this->_data[$key])) {
                        $this->_data[$scopeKey] = $this->_data[$key];
                        /**
                         * Not allow use translation not related to module
                         */
                        if (Mage::getIsDeveloperMode()) {
                            unset($this->_data[$key]);
                        }
                    }
                }
                $scopeKey = $scope . self::SCOPE_SEPARATOR . $key;
                $this->_data[$scopeKey] = $value;
            }
            else {
                $this->_data[$key]     = $value;
                $this->_dataScope[$key]= $scope;
            }
        }
        return $this;
    }
    /**
     * Add translation hint to string if hints enabled
     * 
     * @param string $string
     * @return string
     */
    protected function _decorateTranslationHint($string)
    {
        if (!$this->isEnabled()) {
            return $string;
        }
        return sprintf('[__%s__](%s%s)', $string,
                $this->_currentMode,
                $this->_currentFile ? ':' . $this->_currentFile : '');
    }
    /**
     * Add cache info to string if hints enabled
     * 
     * @param string $string
     * @return string
     */
    protected function _decorateCacheHint($string)
    {
        if (!$this->isEnabled()) {
            return $string;
        }
        return $string . '(cached)';
    }
}