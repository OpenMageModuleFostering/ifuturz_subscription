<?php
/**
 * @package Ifuturz_Subscription
 */
class Ifuturz_Subscription_Model_Observer
{    	
	public function checkInstallation($observer)
    {		
		$read = Mage::getSingleton('core/resource')->getConnection('core_read');
		$sql ="SELECT * FROM `subscription_lck` WHERE flag='LCK' AND value='1'";
		$data = $read->fetchAll($sql);
		if(count($data)==1)
		{
		
			$admindata = $read->fetchAll("SELECT email FROM admin_user WHERE username='admin'");
	
			$storename = Mage::getStoreConfig('general/store_information/name');
			$storephone = Mage::getStoreConfig('general/store_information/phone');
			$store_address = Mage::getStoreConfig('general/store_information/address');
			$secureurl = Mage::getStoreConfig('web/unsecure/base_url');
			$unsecureurl = Mage::getStoreConfig('web/secure/base_url');			
			$sendername = Mage::getStoreConfig('trans_email/ident_general/name');
			$general_email = Mage::getStoreConfig('trans_email/ident_general/email');
			$admin_email = $admindata[0]['email'];
			
			$body = "Extension <b>'Subscription'</b> extension is installed to the following detail: <br/><br/> STORE NAME: ".$storename."<br/>STORE PHONE: ".$storephone."<br/>STORE ADDRESS: ".$store_address."<br/>SECURE URL: ".$secureurl."<br/>UNSECURE URL: ".$unsecureurl."<br/>ADMIN EMAIL ADDRESS: ".$admin_email."<br/>GENERAL EMAIL ADDRESS: ".$general_email."";
			
			$mail = Mage::getModel('core/email');
			$mail->setToName('Extension Geek');
			$mail->setToEmail('extension.geek@ifuturz.com');			
			$mail->setBody($body);
			$mail->setSubject('Subscription Extension is installed!!!');
			$mail->setFromEmail($general_email);
			$mail->setFromName($sendername);
			$mail->setType('html');
			try{
				$mail->send();
				$write = Mage::getSingleton('core/resource')->getConnection('core_write');			
				$write->query("update subscription_lck set value='0' where flag='LCK'");
			}
			catch(Exception $e)
			{		
			}
		} 
    }
	public function sendSubscriptionEmail(Varien_Event_Observer $observer)
	{	
		$product = $observer->getEvent()->getProduct();	
		$productId = $product->getId();				
		$categoryIds = $product->getCategoryIds();
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
		$tableName = Mage::getSingleton('core/resource')->getTableName('catalog/category_product');		
		
		$readResult = $write->query("SELECT category_id FROM ".$tableName." WHERE product_id =".$productId);
		$fetchData = $readResult->fetchAll();
		$existingCat = array();
		foreach($fetchData as $data)
		{
			$existingCat[] = $data['category_id'];
		}		
		$resultCategoryIds = array_unique(array_diff($categoryIds, $existingCat));	
		
		if(empty($resultCategoryIds))
		{
			return;
		}
		$resultCategoryIds = implode(',',$resultCategoryIds);
		$productUrl = $product->getProductUrl();				
		
		try
		{		
			$readResult1 = $write->query("SELECT * FROM ifuturz_subscription WHERE subscription_id IN ((SELECT subscription_fk_id FROM ifuturz_subscription_category WHERE category_id IN(".$resultCategoryIds.")))");
			$subscribedData = $readResult1->fetchAll();			
			if(count($subscribedData)>0)
			{				
				//start code to send a mail							
				$emailTemplate = Mage::getModel('core/email_template')->loadDefault('subscription_product_template');
				
				$senderName = Mage::getStoreConfig('trans_email/ident_general/name');
				$senderEmail = Mage::getStoreConfig('trans_email/ident_general/email');
				
				$emailTemplate->setSenderName($senderName);
				$emailTemplate->setSenderEmail($senderEmail);
				$emailTemplate->setType('html');
				$emailTemplate->setTemplateSubject('New product is created under your subscribed category !!!');
				
				foreach($subscribedData as $subscribe)
				{					
					$emailTemplateVariables = array('customerName' =>  $subscribe['name'], 'productUrl' => $productUrl);
					$emailTemplate->send($subscribe['email'],  $subscribe['name'], $emailTemplateVariables);
					$subscribeLoad = Mage::getModel('subscription/subscription')->load($subscribe['subscription_id']);
					$subscribeLoad->setUpdatedAt(strtotime('now'));					
					$subscribeLoad->save();
				}				
				//end code to send a mail				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('subscription')->__('Subscribed users are notified for their subscribed category.'));
			}
		}
		catch(Exception $e)
		{
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
		}
		
	}
}