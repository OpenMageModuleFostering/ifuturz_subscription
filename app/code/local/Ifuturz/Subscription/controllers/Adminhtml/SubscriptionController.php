<?php
/**
 * @package Ifuturz_Subscription
 */
class Ifuturz_Subscription_Adminhtml_SubscriptionController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction()
	{
		$this->loadLayout()
			->_setActiveMenu('subscription')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Subscription Message Management'), Mage::helper('adminhtml')->__('Subscriptionation Message Management'));
		
		return $this;
	}
	
	public function indexAction() 
	{
		$this->_initAction()
			->renderLayout();
	}
	
	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('subscription/subscription')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('subscription_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('subscription');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Special Offer Message Management'), Mage::helper('adminhtml')->__('Special Offer Message Management'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Rule News'), Mage::helper('adminhtml')->__('Rule News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('subscription/adminhtml_subscription_edit'))
				->_addLeft($this->getLayout()->createBlock('subscription/adminhtml_subscription_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('subscription')->__('Message does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	
	public function newAction() 
	{
		$this->_forward('edit');
	}
	
	public function saveAction() 
	{
		
		if ($data = $this->getRequest()->getPost()) 
		{				
			$id = $this->getRequest()->getParam('id');
			if($id)
			{
				$modaldata = Mage::getModel('subscription/subscription')->load($id);	
				$prev_status = $modaldata->getSendMail();			
			}
	  		
			$model = Mage::getModel('subscription/subscription');
			
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try 
			{
				if ($model->getCreatedAt()== NULL)
				{
					$model->setCreatedAt(now());
				} 
				else
				{					
					$model->setUpdatedAt(now());
				}
				$model->save();
				
				if($data['send_mail']=='yes' && $prev_status=='no')
				{
					//start code to send a mail					
					
					$emailTemplate = Mage::getModel('core/email_template')->loadDefault('queans_template');	
					$productload = Mage::getModel('catalog/product')->load($model->getProductId());					
			   
					// Set sender information			
					$senderName = Mage::getStoreConfig('trans_email/ident_general/name');
					$senderEmail = Mage::getStoreConfig('trans_email/ident_general/email');
					// Set recepient information
					$recepientEmail = $model->getEmail();
					$recepientName = $model->getName();	
					
					// Set variables that can be used in email template
					$emailTemplateVariables = array('customerName' => $recepientName,						 
							  'url' => $productload->getProductUrl(),
							  'productname' => $productload->getName()
							  ); 
					/*$vars: this variables are used in transactional like <strong>Dear {{var customerName}} with email {{var customerEmail}}</strong>,<br/>*/
					
					$emailTemplate->setSenderName($senderName);
					$emailTemplate->setSenderEmail($senderEmail);
					$emailTemplate->setType('html');
					$emailTemplate->setTemplateSubject('Your Question is Answered !!!');
					$emailTemplate->send($recepientEmail, $recepientName, $emailTemplateVariables);		
					
					//end code to send a mail
				}
				
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('subscription')->__('Answer was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) 
				{
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            }
			catch (Exception $e) 
			{
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('subscription')->__('Unable to find Question to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('subscription/subscription');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Question was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $subscriptionIds = $this->getRequest()->getParam('subscription');
        if(!is_array($subscriptionIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('subscription')->__('Please select Message(s)'));
        } else {
            try {
                foreach ($subscriptionIds as $subscriptionId) {
                    $subscription = Mage::getModel('subscription/subscription')->load($subscriptionId);
                    $subscription->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($subscriptionIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $subscriptionIds = $this->getRequest()->getParam('subscription');
        if(!is_array($subscriptionIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select Question(s)'));
        } else {
            try {
                foreach ($subscriptionIds as $subscriptionId) {
                    $subscription = Mage::getSingleton('subscription/subscription')
                        ->load($subscriptionId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($subscriptionIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
}