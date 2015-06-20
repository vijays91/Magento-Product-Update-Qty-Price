<?php
class Vijay_Productupdate_Block_Adminhtml_Qty_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
		parent::__construct();               
        $this->_objectId = 'id';
        $this->_blockGroup = 'qty';
        $this->_controller = 'adminhtml_productupdate'; 
		$this->_removeButton('back');
		$this->_removeButton('delete');
		$this->_removeButton('reset');		
        $this->_updateButton('save', 'label', Mage::helper('productupdate')->__('Save Content'));		
	}
 
    public function getHeaderText() {
		return Mage::helper('productupdate')->__('Product Qty Update');
    }
	
	protected function _prepareLayout()
	{
		/* echo $this->_blockGroup . '/' . $this->_controller . '_' . $this->_mode . '_form'; */
		if ($this->_blockGroup && $this->_controller && $this->_mode) {
			$this->setChild('form', $this->getLayout()->createBlock('productupdate/adminhtml_qty_edit_form'));
		}
		return parent::_prepareLayout();
	}
}
