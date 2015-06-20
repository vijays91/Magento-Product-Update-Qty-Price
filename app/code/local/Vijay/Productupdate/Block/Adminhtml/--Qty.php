<?php
class Vijay_Productupdate_Block_Adminhtml_Qty extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_productupdate';
		$this->_blockGroup = 'Qty';
		$this->_headerText = Mage::helper('productupdate')->__('Product Qty Update Manager');
		/* $this->_addButtonLabel = Mage::helper('productupdate')->__('Add Product Update Options'); */
		parent::__construct();
	}
}
