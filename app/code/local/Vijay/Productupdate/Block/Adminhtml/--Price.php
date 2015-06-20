<?php
class Vijay_Productupdate_Block_Adminhtml_Price extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_productupdate';
		$this->_blockGroup = 'price';
		$this->_headerText = Mage::helper('productupdate')->__('Product Price Update Manager');
		/* $this->_addButtonLabel = Mage::helper('productupdate')->__('Add Product Update Options'); */
		parent::__construct();
	}
}
