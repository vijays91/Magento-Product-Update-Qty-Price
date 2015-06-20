<?php
class Vijay_Productupdate_Block_Adminhtml_Price_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{	  
    public function __construct() {
        parent::__construct();
    }
		
    protected function _prepareForm()
    {
		$form = new Varien_Data_Form(array(
			'id' => 'edit_form',
			'action' => $this->getUrl('*/*/savePrice'),
			'method' => 'post',
			'enctype'=> "multipart/form-data",
			)
		); 
        $form->setUseContainer(true);
        $this->setForm($form);		
		
        $fieldset = $form->addFieldset('productupdate_form',
					array('legend'=>Mage::helper('productupdate')->__('Product Price Update'))
				);
		$fieldset->addField('price', 'file', array(
			'name'          =>'price',
			'label'         => Mage::helper('productupdate')->__('Product Price Update'),
			'value'         => '',
			'required'		=> true,
			'tabindex' 		=> 1,
		));
		
        return parent::_prepareForm();
    }
}
