<?php
 class Omx_Hooks_Block_Admin_Search_Results extends Mage_Adminhtml_Block_Abstract { public function _construct(){ $this->_blockGroup = 'hooks'; $this->_mode = 'search results'; $this->_controller = 'admin'; $this->_headerText = Mage::helper('hooks')->__('Search Results: ' ); } protected function _toHtml () { $string = '<h1>Results</h1>'; $results = $this->getData('omxEntries'); if(count($results)){ $string .= "<ul>"; foreach ($results as $result) { $link = $this->getUrl('*/*/view', array( 'id' => $result->getOmxId() )); $string .= "<li><a href='". $link ."'>". $result->getOmxId() ." | " . $result->getTimestamp(). "</a></li>"; } $string .= "</ul>"; return $string; } } }