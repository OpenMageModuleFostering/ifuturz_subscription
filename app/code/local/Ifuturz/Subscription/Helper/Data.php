<?php
/**
 * @package Ifuturz_Subscription
 */
class Ifuturz_Subscription_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getSubscribeUrl()
	{
		return $this->_getUrl('subscription/index/index');
	}
}