<?php
/**
 * @package Ifuturz_Subscription
 */
class Ifuturz_Subscription_Model_Subscription extends Mage_Core_Model_Abstract
{
	public function _construct()
	{
		parent::_construct();
		$this->_init('subscription/subscription');
	}
   
}