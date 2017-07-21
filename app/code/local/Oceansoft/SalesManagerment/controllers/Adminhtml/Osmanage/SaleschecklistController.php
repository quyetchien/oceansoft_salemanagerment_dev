<?php

class Oceansoft_SalesManagerment_Adminhtml_Osmanage_SaleschecklistController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction(){
        $this->_initAction()
            ->renderLayout();
    }

    /**
     * Initialize action
     *
     * Here, we set the breadcrumbs and the active menu
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('salesmanagerment/saleschecklist')
            ->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Sales Checklist'), Mage::helper('adminhtml')->__('Sales Checklist')
            );
        return $this;
    }

    public function editAction()
    {
        $checkListId = $this->getRequest()->getParam('id');
        $checkListModel = Mage::getModel('salesmanagerment/checklist')->load($checkListId);
        if ($checkListModel->getId() || $checkListId == 0)
        {
            Mage::register('salesmanagerment_data', $checkListModel);
            $this->loadLayout();
            $this->_setActiveMenu('salesmanagerment/saleschecklist');
            $this->_addBreadcrumb('Sales Checklist Manager', 'Sales Checklist Manager');
            $this->getLayout()->getBlock('head')
                ->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()
                ->createBlock('salesmanagerment/adminhtml_saleschecklist_edit'))
                ->_addLeft($this->getLayout()
                    ->createBlock('salesmanagerment/adminhtml_saleschecklist_edit_tabs')
                );
            $this->renderLayout();
        }
        else
        {
            Mage::getSingleton('adminhtml/session')->addError('Email does not exist');
            $this->_redirect('*/*/');
        }
    }

    public function saveAction()
    {
        if ($this->getRequest()->getPost()) {
            try {
                $postData = $this->getRequest()->getPost();
                $checkListModel = Mage::getModel('salesmanagerment/checklist');
                $checklist_id = $this->getRequest()->getParam('id');
                $checklistData = $postData;
                $checklistGroup = array();
                if(isset($postData['group']['value'])){
                    $checklistGroup = $postData['group']['value'];
                }
                unset($checklistData['group']);
                $user_id = 0;
                $session = Mage::getSingleton('admin/session');
                if($user = $session->getUser()){
                    $user_id = $user->getUserId();
                }
                if ($checklist_id <= 0) {
                    $order = Mage::getModel('sales/order')->loadByIncrementId($checklistData['order_id']);
                    if($order->getData()){
                        $orderGrandTotal = $order->getGrandTotal();
                        if($checklistData['refund'] > 0){
                            $orderGrandTotal = $orderGrandTotal * (1 - ($checklistData['refund'] / 100));
                        }
                        $dataExt = $this->_getDataByOrder($order, $checklistGroup, $orderGrandTotal);
                        $checkListModel
                            ->addData($checklistData)
                            ->setCustomerEmail($dataExt['customer_email'])
                            ->setPrice($dataExt['price'])
                            ->setOrderDate($dataExt['order_date'])
                            ->setUser($user_id)
                            ->save();
                        $checklistIdIpt = $checkListModel->getId();

                        // insert oceansoft_sales_report
                        $this->_importSalesReportTable($user_id, $dataExt['price'], $checklistIdIpt, $checklistData['created_at']);

                        // insert oceansoft_sales_checklist_group
                        if($checklistIdIpt){
                            if ($checklistGroup) {
                                foreach ($checklistGroup as $data_group) {
                                    $groupModel = Mage::getModel('salesmanagerment/checklistgroup');
                                    $groupModel->setChecklistId($checklistIdIpt);
                                    $groupModel->setUserId($data_group['saleid']);
                                    $groupModel->setValue($data_group['salevalue']);
                                    $groupModel->save();
                                    $user_group_price = $orderGrandTotal * $data_group['salevalue'] / 100;
                                    $this->_importSalesReportTable($data_group['saleid'], $user_group_price, $checklistIdIpt, $checklistData['created_at']);
                                }
                            }
                        }
                        Mage::getSingleton('adminhtml/session')->addSuccess('successfully saved');
                        Mage::getSingleton('adminhtml/session')->setFormData(false);
                        $this->_redirect('*/*/');
                        return;
                    }else{
                        Mage::getSingleton('adminhtml/session')->addError('Order not existed');
                        Mage::getSingleton('adminhtml/session')->setFormData($this->getRequest()->getPost());
                        $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                        return;
                    }
                }else{
                    $order = Mage::getModel('sales/order')->loadByIncrementId($checklistData['order_id']);
                    if($order->getData()){
                        $orderGrandTotal = $order->getGrandTotal();
                        if($checklistData['refund'] > 0){
                            $orderGrandTotal = $orderGrandTotal * (1 - ($checklistData['refund'] / 100));
                        }
                        $dataExt = $this->_getDataByOrder($order, $checklistGroup, $orderGrandTotal);

                        // delete oceansoft_sales_report, oceansoft_sales_checklist_group
                        $reportCollection = Mage::getModel('salesmanagerment/salesreport')->getCollection()
                            ->addFieldToFilter('checklist_id', $checklist_id);
                        foreach ($reportCollection as $report_collection) {
                            $report_collection->delete();
                        }
                        $groupCollection = Mage::getModel('salesmanagerment/checklistgroup')->getCollection()
                            ->addFieldToFilter('checklist_id', $checklist_id);
                        foreach ($groupCollection as $group_collection) {
                            $group_collection->delete();
                        }

                        // update oceansoft_sales_checklist
                        $checkListModel->load($checklist_id)->addData($checklistData)
                            ->setCustomerEmail($dataExt['customer_email'])
                            ->setPrice($dataExt['price'])
                            ->setOrderDate($dataExt['order_date'])
                            ->setId($checklist_id)
                            ->save();

                        $this->_importSalesReportTable($user_id, $dataExt['price'], $checklist_id, $checklistData['created_at']);

                        // update oceansoft_sales_checklist_group
                        if ($checklistGroup) {
                            foreach ($checklistGroup as $data_group) {
                                $groupModel = Mage::getModel('salesmanagerment/checklistgroup');
                                $groupModel->setChecklistId($checklist_id);
                                $groupModel->setUserId($data_group['saleid']);
                                $groupModel->setValue($data_group['salevalue']);
                                $groupModel->save();
                                $user_group_price = $orderGrandTotal * $data_group['salevalue'] / 100;
                                $this->_importSalesReportTable($data_group['saleid'], $user_group_price, $checklist_id, $checklistData['created_at']);
                            }
                        }

                        Mage::getSingleton('adminhtml/session')->addSuccess('successfully saved');
                        Mage::getSingleton('adminhtml/session')->setFormData(false);
                        $this->_redirect('*/*/');
                        return;
                    }else{
                        Mage::getSingleton('adminhtml/session')->addError('Order not existed');
                        Mage::getSingleton('adminhtml/session')->setFormData($this->getRequest()->getPost());
                        $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                        return;
                    }
                }
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($this->getRequest()->getPost());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }

            $this->_redirect('*/*/');
        }
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function deleteAction()
    {
        if($this->getRequest()->getParam('id') > 0)
        {
            try
            {
                $checkListModel = Mage::getModel('salesmanagerment/checklist');
                $checkListModel->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess('successfully deleted');
                $this->_redirect('*/*/');
            }
            catch (Exception $e)
            {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }


    protected function _getDataByOrder($order, $checklistGroup, $orderGrandTotal){
        $customer_email = $order->getCustomerEmail();
        $myPercent = 100;
        if($checklistGroup){
            foreach($checklistGroup as $group){
                if($group['salevalue']){
                    $myPercent -= $group['salevalue'];
                }
            }
        }
        if($myPercent > 0){
            $myPrice = $orderGrandTotal * $myPercent / 100;
        }else{
            $myPrice = 0;
        }
        return array(
            'customer_email' => $customer_email,
            'price' => $myPrice,
            'order_date' => $order->getCreatedAt()
        );
    }

    protected function _importSalesReportTable($user_id, $price, $checklist_id, $created_at){
        $reportModel = Mage::getModel('salesmanagerment/salesreport');
        $reportModel->setUserId($user_id);
        $reportModel->setPrice($price);
        $reportModel->setChecklistId($checklist_id);
        $reportModel->setCreatedAt($created_at);
        $reportModel->save();
    }

    /**
     * Check currently called action by permissions for current user
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('salesmanagerment/saleschecklist');
    }
}