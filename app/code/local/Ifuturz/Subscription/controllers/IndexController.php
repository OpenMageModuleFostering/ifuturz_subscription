<?php
/**
 * @package Ifuturz_Subscription
 */
class Ifuturz_Subscription_IndexController extends Mage_Core_Controller_Front_Action
{

	public function indexAction()
	{
		$this->loadLayout()->renderLayout();
	}
	
	public function postAction()
	{		
		if ($data = $this->getRequest()->getPost()) 
		{	
					
			$loadData = Mage::getModel('subscription/subscription')->getCollection()->addFieldToFilter('email',$data['email']);
			if(count($loadData)>0)
			{
				Mage::getSingleton('core/session')->addError(Mage::helper('subscription')->__('You are already subscribed for this email address. Please try with another.'));
									
				$this->_redirect('*/*/');			
			}
			else
			{
				$write = Mage::getSingleton('core/resource')->getConnection('core_write');
				$model = Mage::getModel('subscription/subscription');			
				
				$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
				try 
				{
					if ($model->getCreatedAt() == NULL)
					{
						$model->setCreatedAt(strtotime('now'));
					} 
					else
					{
						$model->setUpdatedAt(strtotime('now'));
					}
					
					$model->save();
					$subscribeId = $model->getId();
					foreach($data['category'] as $key => $value)
					{						
						$sql = "INSERT INTO `ifuturz_subscription_category` (`subscription_fk_id`,`category_id`) VALUES ('$subscribeId','$value')";
						$write->query($sql);
					}
					//start code to send a mail					
						
					$emailTemplate = Mage::getModel('core/email_template')->loadDefault('subscription_template');					
					// Set sender information			
					$senderName = Mage::getStoreConfig('trans_email/ident_general/name');
					$senderEmail = Mage::getStoreConfig('trans_email/ident_general/email');
					// Set recepient information
					$recepientEmail = $data['email'];
					$recepientName = $data['name'];	
					
					// Set variables that can be used in email template
					$emailTemplateVariables = array('customerName' => $recepientName,						
							  ); 
					/*$vars: this variables are used in transactional like <strong>Dear {{var customerName}} with email {{var customerEmail}}</strong>,<br/>*/
					
					$emailTemplate->setSenderName($senderName);
					$emailTemplate->setSenderEmail($senderEmail);
					$emailTemplate->setType('html');
					$emailTemplate->setTemplateSubject('You are subscribed for the website !!!');
					$emailTemplate->send($recepientEmail, $recepientName, $emailTemplateVariables);		
					
					//end code to send a mail
					Mage::getSingleton('core/session')->addSuccess(Mage::helper('subscription')->__('You are successfully subscribed for the website.'));
									
					$this->_redirect('*/*/');
					
				}
				catch (Exception $e) 
				{
					Mage::getSingleton('core/session')->addError($e->getMessage());
					
					$this->_redirect('*/*/');				
				}		
			}
			
		}
	}
}