<?php
/**
 * @package Ifuturz_Subscription
 */
class Ifuturz_Subscription_Block_Adminhtml_Subscription extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
	
    $this->_controller = 'adminhtml_subscription';
    $this->_blockGroup = 'subscription';
    $this->_headerText = Mage::helper('subscription')->__('Subscription Management');	
    parent::__construct();
	$this->_removeButton('add');
  }
}