<?php
/**
 * @package Ifuturz_Subscription
 */
class Ifuturz_Subscription_Block_Subscription extends Mage_Core_Block_Template
{
	public function _prepareLayout()
	{
		return parent::_prepareLayout();	
	}
	
	public function getSubscription()
	{
		if(!$this->hasData('subscription'))
		{
			$this->setData('subscription',Mage::registry('subscription'));
		}
		return $this->getData('subscription');
	}
	public function res($cur_category, $idCat){
      
            $cat_mod = Mage::getModel('catalog/category');
            $categoryObj  = $cat_mod->load($cur_category);//->getChildrenCategories();
      
            if($categoryObj->hasChildren()){
    ?>  <ul> <?php
    $children_categories =$categoryObj->getChildrenCategories();
                foreach($children_categories as  $v):      
                    $all_data = $v->getData();
                    $nm = $all_data['name'];?>
            <li>
                <span><?php echo $nm?></span>
            <?php if(!$v->hasChildren()):?>
                <input class="categorychild category" type="checkbox" name="category['<?=$nm?>']" value="<?php echo $v->getId();?>">                 
            <?php else:?>
                <span class="expand-child">&nbsp;</span>
            <?php endif;?>
            <?php $this->res($v->getId(),$idCat);?>
            </li>            
             <?php
                endforeach;
    ?> </ul> <?php 
        }
     }
}