<?php
class Oceansoft_SalesManagerment_Block_Adminhtml_Saleschecklist_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('id');
        $this->setId('sales_checklist_grid');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('salesmanagerment/checklist')->getCollection();
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

        $this->addColumn('order_id',
            array(
                'header'=> $this->__('Order Id'),
                'index' => 'order_id',
                'width' => '100px'
            )
        );

        $this->addColumn('customer_email',
            array(
                'header'=> $this->__('Customer Email'),
                'index' => 'customer_email'
            )
        );

        $this->addColumn('ticket_id',
            array(
                'header'=> $this->__('Ticket Id'),
                'index' => 'ticket_id'
            )
        );

        $this->addColumn('price',
            array(
                'header'=> $this->__('Price'),
                'index' => 'price',
                'type' => 'price',
            )
        );

        $this->addColumn('refund',
            array(
                'header'=> $this->__('Refund'),
                'renderer'  => 'salesmanagerment/adminhtml_saleschecklist_render_refund',
            )
        );

        $this->addColumn('group',
            array(
                'header'=> $this->__('Group'),
                'renderer'  => 'salesmanagerment/adminhtml_saleschecklist_render_group',
            )
        );

        $this->addColumn('created_at',
            array(
                'header'=> $this->__('Created At'),
                'index' => 'created_at',
                'type' => 'datetime',
            )
        );

        $this->addColumn('user',
            array(
                'header'=> $this->__('User'),
                'renderer'  => 'salesmanagerment/adminhtml_saleschecklist_render_user',
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