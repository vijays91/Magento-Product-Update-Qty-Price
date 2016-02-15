<?php
class Vijay_productupdate_Adminhtml_productupdateController extends Mage_Adminhtml_Controller_Action
{
	const price = 'price';
	const qty   = 'qty';
	public function priceAction() {
		$this->loadLayout();
		$this->_title($this->__('Product Update'))->_title($this->__('Product Update'));
		$this->_setActiveMenu('vijay/items');
		
		/* $this->getLayout()->getBlock('head')->setCanLoadExtJs(true); */
		
		$breadcrumbTitle = Mage::helper('productupdate')->__('Product Update');
		$breadcrumbLabel = Mage::helper('productupdate')->__('Product Update');
		$this->_addBreadcrumb($breadcrumbLabel, $breadcrumbTitle);
		
		$this->_addContent($this->getLayout()->createBlock('productupdate/adminhtml_price_edit'));
		/* ->_addLeft($this->getLayout()->createBlock('productupdate/adminhtml_price_edit_tabs')); */
		$this->renderLayout();
	}
	
	public function qtyAction() {
		
		$this->loadLayout();
		$this->_title($this->__('Product Update'))->_title($this->__('Product Update'));		
		$this->_setActiveMenu('vijay/items');
		
		/* $this->getLayout()->getBlock('head')->setCanLoadExtJs(true); */	
		
		$breadcrumbTitle = Mage::helper('productupdate')->__('Product Update');
		$breadcrumbLabel = Mage::helper('productupdate')->__('Product Update');
		$this->_addBreadcrumb($breadcrumbLabel, $breadcrumbTitle);
		
		$this->_addContent($this->getLayout()->createBlock('productupdate/adminhtml_qty_edit'));
		/* ->_addLeft($this->getLayout()->createBlock('productupdate/adminhtml_qty_edit_tabs')); */
		$this->renderLayout();
	}
	
	public function savePriceAction() {
		ini_set('memory_limit', '2048M');
		ini_set('max_execution_time', 0);
		
		//set_time_limit(24 * 60 * 60);
		
		$allowed =  array('csv');
		$filename = $_FILES['price']['name'];
		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		if(!in_array($ext, $allowed) ) {
			Mage::getSingleton('adminhtml/session')->addError(
				Mage::helper('productupdate')->__('Check the uploaded file format.')
			);
			$this->_redirect('*/*/price');
		}
		/*- Storing Folder -*/
		$saveDirectory = "/var/product_import/price/";
		$baseDirectory = Mage::getBaseDir()."/";
		$saveDirectory = trim($saveDirectory, '/');
		$newDirectory = "";
		foreach(explode('/',$saveDirectory) as $val) {
			if(!is_dir($baseDirectory.$newDirectory.$val)){
				mkdir($baseDirectory.$newDirectory.$val, 0777);
				chmod($baseDirectory.$newDirectory.$val, 0777);
			}
			$newDirectory .= $val."/";
		}
		
		$currentTimestamp = Mage::getModel('core/date')->timestamp(time());
		$imported_date_time = date('Y-m-d_H-m-s', $currentTimestamp);
		$tmp_target_file = $saveDirectory . "/" . $imported_date_time.'_'.basename($_FILES["price"]["name"]);
		if (move_uploaded_file($_FILES["price"]["tmp_name"], $tmp_target_file)) {
			$readData = $this->csvRead($tmp_target_file, self::price);
			$productUpdate = $this->updateProduct($readData, self::price);
			if($productUpdate) {
				Mage::getSingleton('adminhtml/session')->addSuccess(
					Mage::helper('productupdate')->__('Price updated successfully')
				);
				$this->_redirect('*/*/price');
			} else {
				Mage::getSingleton('adminhtml/session')->addError(
					Mage::helper('productupdate')->__('No Price updated.')
				);
				$this->_redirect('*/*/price');
			}
		} else {
			Mage::getSingleton('adminhtml/session')->addError(
				Mage::helper('productupdate')->__('Moving file error.')
			);
			$this->_redirect('*/*/price');
		}
	}
	
	public function csvRead($filename, $type) {
		$ret = array();
		$csv  = new Varien_File_Csv();
		$data = $csv->getData($filename); //path to csv
		if(count($data) > 0 ) {
			if(strtolower($data[0][0]) == "sku" && ( strtolower($data[0][1]) == $type))
			{
				/* unset($data[0]); */
				array_shift($data);
				foreach($data as $_data){
					$ret[] = array('sku' => $_data[0], $type => $_data[1] );
				}
			}
			else {
				Mage::getSingleton('adminhtml/session')->addError(
					Mage::helper('productupdate')->__('Column Mismatch.')
				);
				$this->_redirect('*/*/'.$type);
			}
		} else {
			Mage::getSingleton('adminhtml/session')->addError(
				Mage::helper('productupdate')->__('No data in CSV file.')
			);
			$this->_redirect('*/*/'.$type);

		}
		return $ret;
	}

	public function updateProduct($csv_data, $type) 
	{	
		$loop = 0;
		if(count($csv_data) > 0 ) {
			foreach($csv_data as $key => $val) {			
				$data = array($type => (integer) trim($val[$type]));
				Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);				
				$productid = Mage::getModel('catalog/product')->getIdBySku(trim($val['sku']));					
				$Product   = Mage::getModel('catalog/product')->loadByAttribute('sku', trim($val['sku']));
				if($productid){
					if($type == "price") {
						$Product->setData($type, trim($val[$type]));	
						try {
							$Product->save();
							$Product->getId();
						} catch(Exception $ex) {
							//Handle the error 
						}
					} elseif($type == "qty") {
						$stockItem =Mage::getModel('cataloginventory/stock_item')->loadByProduct($productid);
						$stockItemId = $stockItem->getId();
						$stockItem->setData('manage_stock', 1);
						$stockItem->setData('qty', (integer)trim($val[$type]));
						$stockItem->save();
					}
					
				}
				$loop++;
			}
		} 
		// else {
			// Mage::getSingleton('adminhtml/session')->addError(
				// Mage::helper('productupdate')->__('No data in CSV read file.')
			// );
			// $this->_redirect('*/*/'.$type);
		// }
		return $loop;
	}
	
	public function saveQtyAction() {
		ini_set('memory_limit', '2048M');
		ini_set('max_execution_time', 0);

		$allowed =  array('csv');
		$filename = $_FILES['qty']['name'];
		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		if(!in_array($ext, $allowed) ) {
			Mage::getSingleton('adminhtml/session')->addError(
				Mage::helper('productupdate')->__('Check the uploaded file format.')
			);
			$this->_redirect('*/*/qty');
		}
		/*- Storing Folder -*/
		$saveDirectory = "/var/product_import/qty/";
		$baseDirectory = Mage::getBaseDir()."/";
		$saveDirectory = trim($saveDirectory, '/');
		$newDirectory = "";
		foreach(explode('/',$saveDirectory) as $val) {
			if(!is_dir($baseDirectory.$newDirectory.$val)){
				mkdir($baseDirectory.$newDirectory.$val, 0777);
				chmod($baseDirectory.$newDirectory.$val, 0777);
			}
			$newDirectory .= $val."/";
		}
		
		$currentTimestamp = Mage::getModel('core/date')->timestamp(time());
		$imported_date_time = date('Y-m-d_H-m-s', $currentTimestamp);
		$tmp_target_file = $saveDirectory . "/" . $imported_date_time.'_'.basename($_FILES["qty"]["name"]);
		if (move_uploaded_file($_FILES["qty"]["tmp_name"], $tmp_target_file)) {
			$readData = $this->csvRead($tmp_target_file, self::qty);
			$productUpdate = $this->updateProduct($readData, self::qty);
			if($productUpdate) {
				Mage::getSingleton('adminhtml/session')->addSuccess(
					Mage::helper('productupdate')->__('Qty updated successfully')
				);
				$this->_redirect('*/*/qty');
			} else {
				Mage::getSingleton('adminhtml/session')->addError(
					Mage::helper('productupdate')->__('No Qty updated.')
				);
				$this->_redirect('*/*/qty');
			}
		} else {
			Mage::getSingleton('adminhtml/session')->addError(
				Mage::helper('productupdate')->__('Moving file error.')
			);
			$this->_redirect('*/*/qty');
		}
	}
}
