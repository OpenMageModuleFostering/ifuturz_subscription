<?php
/**
 * @package Ifuturz_Subscription
 */
class Ifuturz_Subscription_Block_Adminhtml_Subscription_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      
	  parent::__construct();
      $this->setId('subscriptionGrid');
      $this->setDefaultSort('subscription_id');
      $this->setDefaultDir('DESC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('subscription/subscription')->getCollection();	 
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  { 
      
       $this->addColumn('subscription_id', array(
          'header'    => Mage::helper('subscription')->__('ID'),
          'align'     =>'left',
          'index'     => 'subscription_id',
      ));
	  $this->addColumn('name', array(
          'header'    => Mage::helper('subscription')->__('Name'),
          'align'     =>'left',
          'index'     => 'name',
      ));
	  
	  $this->addColumn('email', array(
          'header'    => Mage::helper('subscription')->__('Email'),
          'align'     =>'left',
          'index'     => 'email',
      ));
	  
	 /* $this->addColumn('category', array(
          'header'    => Mage::helper('subscription')->__('Category IDs'),
          'align'     =>'left',
          'index'     => 'category',		
      ));*/
	 
	   $this->addColumn('created_at', array(
          'header'    => Mage::helper('subscription')->__('Created At'),
          'align'     =>'left',
          'index'     => 'created_at',
  		 'type'      => 'date',

      ));  
	   $this->addColumn('updated_at', array(
          'header'    => Mage::helper('subscription')->__('Update At'),
          'align'     =>'left',
          'index'     => 'updated_at',
  		  'type'      => 'date',

      ));  	 	
		
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('subscription_id');
        $this->getMassactionBlock()->setFormFieldName('subscription');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('subscription')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('subscription')->__('Are you sure?')
        ));       
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}