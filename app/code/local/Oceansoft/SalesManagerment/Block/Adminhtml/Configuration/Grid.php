<?php
class Oceansoft_SalesManagerment_Block_Adminhtml_Configuration_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('id');
        $this->setId('sales_coniguration_grid');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('salesmanagerment/oceansaleconfig')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $store = Mage::app()->getStore();
        // Add the columns that should appear in the grid
        $this->addColumn('id',
            array(
                'header'=> $this->__('ID'),
                'align' =>'right',
                'width' => '50px',
                'index' => 'id'
            )
        );

        $this->addColumn('user',
            array(
                'header'=> $this->__('User'),
                'renderer'  => 'salesmanagerment/adminhtml_configuration_render_user',
            )
        );

        $this->addColumn('revenue',
            array(
                'header'=> $this->__('Refund'),
                'index' => 'revenue'
            )
        );

        $this->addColumn('from',
            array(
                'header'=> $this->__('From'),
                'index' => 'from',
                'type' => 'date',
            )
        );

        $this->addColumn('to',
            array(
                'header'=> $this->__('To'),
                'index' => 'to',
                'type' => 'date',
            )
        );

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        // This is where our row data will link to
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}