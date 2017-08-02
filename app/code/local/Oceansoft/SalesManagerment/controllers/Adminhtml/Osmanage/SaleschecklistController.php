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
            Mage::getSingleton('adminhtml/session')->addError('Note does not exist');
            $this->_redirect('*/*/');
        }
    }

    public function saveAction()
    {
        if ($this->getRequest()->getPost()) {
            try {
                $postData = $this->getRequest()->getPost();
                $postDataGroup = isset($postData['group']) ? $postData['group'] : false;
                unset($postData['group']);
                $checkListModel = Mage::getModel('salesmanagerment/checklist');
                $checkListCollection = $checkListModel->getCollection();
                $checklist_id = $this->getRequest()->getParam('id');
                $user_id = 0;
                $session = Mage::getSingleton('admin/session');
                if($user = $session->getUser()){
                    $user_id = $user->getUserId();
                }
                if ($checklist_id <= 0) {
                    // check order id existed
                    $checkOrderCreated = $checkListCollection
                        ->addFieldToFilter('order_id', $postData['order_id']);
                    if($checkOrderCreated->getData()){
                        Mage::getSingleton('adminhtml/session')->addError('Ticket with Order Id existed');
                        Mage::getSingleton('adminhtml/session')->setFormData($this->getRequest()->getPost());
                        $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                        return;
                    }

                    $order = Mage::getModel('sales/order')->loadByIncrementId($postData['order_id']);
                    if($order->getData()){
                        $orderGrandTotal = $order->getGrandTotal();
                        if($postData['refund'] > 0){
                            $orderGrandTotal = $orderGrandTotal * (1 - ($postData['refund'] / 100));
                        }
                        $myPercentage = $this->_getMyPercentage($postData, $postDataGroup);
                        if($myPercentage < 0){
                            Mage::getSingleton('adminhtml/session')->addError('Total percentage can not larger 100%');
                            Mage::getSingleton('adminhtml/session')->setFormData($this->getRequest()->getPost());
                            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                            return;
                        }
                        $myPrice = $orderGrandTotal * $myPercentage / 100;
                        $total_earn = $this->_calculationEarnPrice($myPrice, $postData['created_at'], $user_id);
                        $checkListModel
                            ->addData($postData)
                            ->setCustomerEmail($order->getCustomerEmail())
                            ->setPrice($myPrice)
                            ->setOrderDate($order->getCreatedAt())
                            ->setUser($user_id)
                            ->save();
                        $checklist_id = $checkListModel->getId();
                        if($checklist_id){
                            // import report for me
                            $this->_importSalesReport(array(
                                'user_id' => $user_id,
                                'value' => $myPercentage,
                                'price' => $myPrice,
                                'total_earn' => $total_earn,
                                'order_id' => $postData['order_id'],
                                'checklist_id' => $checklist_id,
                                'created_at' => $postData['created_at']
                            ));

                            // import report for group
                            if($postDataGroup && $postDataGroup['value']){
                                foreach($postDataGroup['value'] as $post_group){
                                    $group_price = $orderGrandTotal * $post_group['salevalue'] / 100;
                                    $group_total_earn = $this->_calculationEarnPrice($group_price, $postData['created_at'], $post_group['saleid']);
                                    $this->_importSalesReport(array(
                                        'user_id' => $post_group['saleid'],
                                        'value' => $post_group['salevalue'],
                                        'price' => $group_price,
                                        'total_earn' => $group_total_earn,
                                        'order_id' => $postData['order_id'],
                                        'checklist_id' => $checklist_id,
                                        'created_at' => $postData['created_at']
                                    ));
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
                    $order = Mage::getModel('sales/order')->loadByIncrementId($postData['order_id']);
                    if($order->getData()){
                        $orderGrandTotal = $order->getGrandTotal();
                        if($postData['refund'] > 0){
                            $orderGrandTotal = $orderGrandTotal * (1 - ($postData['refund'] / 100));
                        }
                        $myPercentage = $this->_getMyPercentage($postData, $postDataGroup);
                        if($myPercentage < 0){
                            Mage::getSingleton('adminhtml/session')->addError('Total percentage can not larger 100%');
                            Mage::getSingleton('adminhtml/session')->setFormData($this->getRequest()->getPost());
                            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                            return;
                        }
                        $myPrice = $orderGrandTotal * $myPercentage / 100;
                        $total_earn = $this->_calculationEarnPrice($myPrice, $postData['created_at'], $user_id);

                        // update oceansoft_sales_checklist
                        $checkListModel->load($checklist_id)->addData($postData)
                            ->setCustomerEmail($order->getCustomerEmail())
                            ->setPrice($myPrice)
                            ->setOrderDate($order->getCreatedAt())
                            ->setId($checklist_id)
                            ->save();

                        //delete report
                        $salesReportCollection = Mage::getModel('salesmanagerment/salesreport')->getCollection()
                            ->addFieldToFilter('checklist_id', $checklist_id);
                        if($salesReportCollection){
                            foreach($salesReportCollection as $report_collection){
                                $report_collection->delete();
                            }
                        }

                        $author = $checkListModel->load($checklist_id)->getUser();
                        // import report for author
                        $this->_importSalesReport(array(
                            'user_id' => $author,
                            'value' => $myPercentage,
                            'price' => $myPrice,
                            'total_earn' => $total_earn,
                            'order_id' => $postData['order_id'],
                            'checklist_id' => $checklist_id,
                            'created_at' => $postData['created_at']
                        ));

                        // import report for group
                        if($postDataGroup && $postDataGroup['value']){
                            foreach($postDataGroup['value'] as $post_group){
                                $group_price = $orderGrandTotal * $post_group['salevalue'] / 100;
                                $group_total_earn = $this->_calculationEarnPrice($group_price, $postData['created_at'], $post_group['saleid']);
                                $this->_importSalesReport(array(
                                    'user_id' => $post_group['saleid'],
                                    'value' => $post_group['salevalue'],
                                    'price' => $group_price,
                                    'total_earn' => $group_total_earn,
                                    'order_id' => $postData['order_id'],
                                    'checklist_id' => $checklist_id,
                                    'created_at' => $postData['created_at']
                                ));
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

    protected function _calculationEarnPrice($price, $time, $user_id){
        $user_revenue = Mage::getModel('salesmanagerment/revenue')
            ->getCollection()
            ->addFieldToFilter('user_id', $user_id);
        if($user = $user_revenue->getData()){
            $user = $user[0];
            if($time >= $user['from'] && $time <= $user['to']){
                $userRule = unserialize($user['rule']);
                foreach($userRule as $rule){
                    if($this->_checkTimeInCondition($rule['from'], $rule['to'], $time)){
                        return $price * $rule['value'] / 100;
                    }
                }
            }
        }
        return 0;
    }

    protected function _checkTimeInCondition($from, $to, $current){
        $from = strtotime($from);
        $to = strtotime($to);
        $current = strtotime(date("H:i:s",strtotime($current)));
        if($to < $from){
            $to = $to + 86400;
            if($current < $from){
                $current = $current + 86400;
            }
        }
        if($from <= $current && $current <= $to){
            return true;
        }
        return false;
    }

    protected function _importSalesReport($dataImport){
        if(!$dataImport){
            return false;
        }
        $salesReportModel = Mage::getModel('salesmanagerment/salesreport');
        try{
            $salesReportModel
                ->addData($dataImport)
                ->save();
        }catch (Exception $e){
            return false;
        }
        return true;
    }

    protected function _getMyPercentage($postData, $postDataGroup){
        $result = 100;
        if($postData['refund']){
            $result -= $postData['refund'];
        }
        if($postDataGroup && $postDataGroup['value']){
            foreach($postDataGroup['value'] as $post_group){
                if($post_group['salevalue']){
                    $result -= $post_group['salevalue'];
                }
            }
        }
        return $result;
    }

    /**
     * export grid items to CSV file
     */
    public function exportCsvAction()
    {
        $fileName   = 'saleschecklist.csv';
        $content    = $this->getLayout()
            ->createBlock('salesmanagerment/adminhtml_saleschecklist_grid')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid items to XML file
     */
    public function exportXmlAction()
    {
        $fileName   = 'saleschecklist.xml';
        $content    = $this->getLayout()
            ->createBlock('rewardpointsreport/adminhtml_saleschecklist_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid items to XML Excel file
     */
    public function exportExcelAction()
    {
        $fileName   = 'saleschecklist.xml';
        $content    = $this->getLayout()
            ->createBlock('rewardpointsreport/adminhtml_saleschecklist_grid')
            ->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
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