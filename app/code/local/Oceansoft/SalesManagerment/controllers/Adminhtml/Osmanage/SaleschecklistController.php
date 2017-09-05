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
                if(!isset($postData['refund'])){
                    $postData['refund'] = 0;
                }
                if(!isset($postData['refund_reason'])){
                    $postData['refund_reason'] = '';
                }
                $checkListModel = Mage::getModel('salesmanagerment/checklist');
                $checkListCollection = $checkListModel->getCollection();
                $checklist_id = $this->getRequest()->getParam('id');
                $user_id = 0;
                $session = Mage::getSingleton('admin/session');
                if($user = $session->getUser()){
                    $user_id = $user->getUserId();
                }
                if ($checklist_id <= 0) {
                    // check percentage
                    $maxPercentage = 100;
                    $refund = 0;
                    $checkNote = $checkListCollection
                        ->addFieldToFilter('order_id', $postData['order_id']);
                    if($noteData = $checkNote->getData()){
                        foreach($noteData as $note){
                            if($note['user'] == $user_id){
                                $this->_errorResult('You have one note with same order id');
                                return;
                            }
                            if($note['refund'] > 0){
                                $refund = $note['refund'];
                            }
                            if($note['sale_percentage'] > 0){
                                $maxPercentage -= $note['sale_percentage'];
                            }
                        }
                    }
                    if($refund > 0){
                        $postData['refund'] = $refund;
                    }
                    if($postData['sale_percentage'] > $maxPercentage){
                        $this->_errorResult('You can only fill form with max percentage: ' . $maxPercentage . '%');
                        return;
                    }
                    // end check percentage

                    $order = Mage::getModel('sales/order')->loadByIncrementId($postData['order_id']);
                    if($order->getData()){
                        $postData['order_date'] = $order->getCreatedAt();
                        $orderGrandTotal = $order->getGrandTotal();
                        if($postData['refund'] > 0){
                            $orderGrandTotal = $orderGrandTotal * (1 - ($postData['refund'] / 100));
                        }
                        $myPrice = $orderGrandTotal * $postData['sale_percentage'] / 100;
                        $total_earn = $this->_calculationEarnPrice($myPrice, $postData['order_date'], $postData['shift'], $user_id);
                        $checkListModel
                            ->addData($postData)
                            ->setCustomerEmail($order->getCustomerEmail())
                            ->setPrice($myPrice)
                            ->setUser($user_id)
                            ->setTotalEarn($total_earn)
                            ->save();

                        Mage::getSingleton('adminhtml/session')->addSuccess('successfully saved');
                        Mage::getSingleton('adminhtml/session')->setFormData(false);
                        $this->_redirect('*/*/');
                        return;
                    }else{
                        $this->_errorResult('Order not existed');
                        return;
                    }
                }else{
                    $order = Mage::getModel('sales/order')->loadByIncrementId($postData['order_id']);
                    if($order->getData()){
                        $postData['order_date'] = $order->getCreatedAt();
                        $orderGrandTotal = $order->getGrandTotal();
                        if($postData['refund'] > 0){
                            $orderGrandTotal = $orderGrandTotal * (1 - ($postData['refund'] / 100));
                        }
                        $maxPercentage = 100;
                        $checkNote = $checkListCollection
                            ->addFieldToFilter('order_id', $postData['order_id'])
                            ->addFieldToFilter('id', array('neq' => $checklist_id));

                        // Update All Order (refund, price, total_earn)
                        if($postData['refund']){
                            foreach($checkNote as $note){
                                $salePrice = $orderGrandTotal * $note->getSalePercentage() / 100;
                                $sale_total_earn = $this->_calculationEarnPrice($salePrice, $note->getOrderDate(), $note->getShift(), $note->getUser());
                                $note->setRefund($postData['refund'])
                                    ->setPrice($salePrice)
                                    ->setTotalEarn($sale_total_earn)
                                    ->save();
                            }
                        }
                        // check percentage
                        if($noteData = $checkNote->getData()){
                            foreach($noteData as $note){
                                if($note['sale_percentage'] > 0){
                                    $maxPercentage -= $note['sale_percentage'];
                                }
                            }
                        }
                        if($postData['sale_percentage'] > $maxPercentage){
                            $this->_errorResult('You can only fill form with max percentage: ' . $maxPercentage . '%');
                            return;
                        }
                        // end check percentage

                        //get my price and my total earn
                        $noteUser = Mage::getModel('salesmanagerment/checklist')->load($checklist_id)->getUser();
                        $myPrice = $orderGrandTotal * $postData['sale_percentage'] / 100;
                        $total_earn = $this->_calculationEarnPrice($myPrice, $postData['order_date'], $postData['shift'], $noteUser);

                        // update oceansoft_sales_checklist
                        $checkListModel->load($checklist_id)->addData($postData)
                            ->setCustomerEmail($order->getCustomerEmail())
                            ->setPrice($myPrice)
                            ->setTotalEarn($total_earn)
                            ->setId($checklist_id)
                            ->save();

                        Mage::getSingleton('adminhtml/session')->addSuccess('successfully saved');
                        Mage::getSingleton('adminhtml/session')->setFormData(false);
                        $this->_redirect('*/*/');
                        return;

                    }else{
                        $this->_errorResult('Order not existed');
                        return;
                    }
                }
            } catch (Exception $e) {
                $this->_errorResult($e->getMessage());
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

    /**
     *  My Custom Function
     */

    protected function _errorResult($msg){
        Mage::getSingleton('adminhtml/session')->addError($msg);
        Mage::getSingleton('adminhtml/session')->setFormData($this->getRequest()->getPost());
        $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
        return;
    }

    protected function _calculationEarnPrice($price, $order_date, $shift, $user_id){
        $user_revenue = Mage::getModel('salesmanagerment/revenue')
            ->getCollection()
            ->addFieldToFilter('user_id', $user_id);
        if($user = $user_revenue->getData()){
            $user = $user[0];
            if($order_date >= $user['from'] && $order_date <= $user['to']){
                $userRule = unserialize($user['rule']);
                foreach($userRule as $rule){
                    if($shift == $rule['shift']){
                        return $price * $rule['value'] / 100;
                    }
                }
            }
        }
        return 0;
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