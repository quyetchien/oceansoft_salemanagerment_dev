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
                $checkListModel = Mage::getModel('salesmanagerment/checklist');
                $checklist_id = $this->getRequest()->getParam('id');
                $user_id = 0;
                $session = Mage::getSingleton('admin/session');
                if($user = $session->getUser()){
                    $user_id = $user->getUserId();
                }
                if ($checklist_id <= 0) {
                    $order = Mage::getModel('sales/order')->loadByIncrementId($postData['order_id']);
                    if($order->getData()){
                        $orderGrandTotal = $order->getGrandTotal();
                        if($postData['refund'] > 0){
                            $orderGrandTotal = $orderGrandTotal * (1 - ($postData['refund'] / 100));
                        }
                        $price = $orderGrandTotal * $postData['percentage'] / 100;
                        $total_earn = $this->_calculationEarnPrice($price, $postData['created_at'], $user_id);
                        $checkListModel
                            ->addData($postData)
                            ->setCustomerEmail($order->getCustomerEmail())
                            ->setPrice($price)
                            ->setTotalEarn($total_earn)
                            ->setOrderDate($order->getCreatedAt())
                            ->setUser($user_id)
                            ->save();
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
                        $price = $orderGrandTotal * $postData['percentage'] / 100;
                        $total_earn = $this->_calculationEarnPrice($price, $postData['created_at'], $user_id);

                        // update oceansoft_sales_checklist
                        $checkListModel->load($checklist_id)->addData($postData)
                            ->setCustomerEmail($order->getCustomerEmail())
                            ->setPrice($price)
                            ->setTotalEarn($total_earn)
                            ->setOrderDate($order->getCreatedAt())
                            ->setId($checklist_id)
                            ->save();

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
            $userRule = unserialize($user[0]['rule']);
            echo "<pre>";
            print_r($userRule);exit;
            foreach($userRule as $rule){
                if((strtotime($time) >= strtotime($rule['from'])) && (strtotime($time) <= strtotime($rule['to']))){
                    echo $price * $rule['value'] / 100;exit;
                    return $price * $rule['value'] / 100;
                }
            }
        }
        return 0;
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