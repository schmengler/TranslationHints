<?php
class SSE_TranslationHints_Model_Translate_Mode
{
    const SOURCE_DB = 'db';
    const SOURCE_MODULE = 'module';
    const SOURCE_THEME = 'theme';
    
    /**
     * @var string Source of currently added translation data
     */
    protected $_currentSourceType;
    /**
     * @var string File of currently added translation data (if mode is not MODE_DB)
     */
    protected $_currentSourceFile;
    /**
     * @var bool
     */
    protected $_useCache = false;

    public function setCurrentSourceType($mode)
    {
        $this->_currentSourceType = $mode;
        return $this;
    }
    public function setCurrentSourceFile($file)
    {
        $this->_currentSourceFile = $file;
        return $this;
    }
    public function setUseCache($useCache)
    {
        $this->_useCache = $useCache;
    }
    public function getCurrentSourceType()
    {
        return $this->_currentSourceType;
    }
    public function getCurrentSourceFile()
    {
        return $this->_currentSourceFile;
    }
    public function getUseCache()
    {
        return $this->_useCache;
    }
}