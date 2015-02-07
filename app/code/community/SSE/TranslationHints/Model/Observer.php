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
 * Observer for config validation
 *
 * @package SSE_TranslationHints
 */
class SSE_TranslationHints_Model_Observer
{
    /**
     * @var Mage_Adminhtml_Model_Session
     */
    protected $_session;
    /**
     * @var SSE_TranslationHints_Helper_Data
     */
    protected $_helper;
    
    public function __construct()
    {
        $this->_session = Mage::getSingleton('adminhtml/session');
        $this->_helper = Mage::helper('sse_translationhints');
    }
    /**
     * Show notice if translation hints and inline tranlations are both enabled
     * 
     * @param Varien_Event_Observer $observer
     */
    public function validateConfig(Varien_Event_Observer $observer)
    {
        $storeId = $observer->getStore();
        if (Mage::getStoreConfigFlag('dev/translate_inline/active', $storeId)
            && Mage::getStoreConfigFlag(SSE_TranslationHints_Model_Translate::XML_HINTS_ENABLED, $storeId)
        ) {
            $this->_session->addNotice(
            $this->_helper->__('Translation hints do not work if inline translation is active.'));
        }
    }
}
