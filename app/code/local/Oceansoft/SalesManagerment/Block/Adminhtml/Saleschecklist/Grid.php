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
        $user_id = 0;
        $session = Mage::getSingleton('admin/session');
        if($user = $session->getUser()){
            $user_id = $user->getUserId();
        }
        if(Mage::helper('salesmanagerment')->checkIsAdminUser($user_id)){
            $collection = Mage::getModel('salesmanagerment/checklist')
                ->getCollection();
        }else{
            $saleReportCollection = Mage::getModel('salesmanagerment/salesreport')
                ->getCollection()
                ->addFieldToFilter('user_id', $user_id);
            $checklistId = array();
            if($reportData = $saleReportCollection->getData()){
                foreach($reportData as $report_data){
                    if($report_data['checklist_id']){
                        $checklistId[] = $report_data['checklist_id'];
                    }
                }
            }
            $checklistId = array_unique($checklistId);
            $collection = Mage::getModel('salesmanagerment/checklist')
                ->getCollection()
                ->addFieldToFilter('id', array('in' => $checklistId));
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
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
                'type' => 'number',
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

        $this->addExportType('*/*/exportCsv', Mage::helper('salesmanagerment')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('salesmanagerment')->__('XML'));
        $this->addExportType('*/*/exportExcel', Mage::helper('adminhtml')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    public function styleDate( $value,$row,$column,$isExport )
    {
        $locale = Mage::app()->getLocale();
        $date = $locale->date( $value, $locale->getDateFormat(), $locale->getLocaleCode(), false )->toString( $locale->getDateFormat() ) ;
        return $date;
    }

    public function getRowUrl($row)
    {
        // This is where our row data will link to
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}