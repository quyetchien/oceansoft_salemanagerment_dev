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
                Mage::helper('adminhtml')->__('Sales Check List'), Mage::helper('adminhtml')->__('Sales Check List')
            );
        return $this;
    }

    public function editAction()
    {
        $checkListId = $this->getRequest()->getParam('checklist_id');
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
                $checklist_id = $this->getRequest()->getParam('checklist_id');
                if ($checklist_id <= 0) {
//                    $emailModel->setIsRunning(0);
//                    $emailModel->setLastRewardId(0);
//                    $emailModel->setCreatedTime( Mage::getSingleton('core/date')->gmtDate() );
//                    $emailModel
//                        ->addData($postData)
//                        ->setUpdateTime( Mage::getSingleton('core/date')->gmtDate())
//                        ->setId($this->getRequest()->getParam('id'))
//                        ->save();
//
//                    Mage::getSingleton('adminhtml/session')->addSuccess('successfully saved');
//                    Mage::getSingleton('adminhtml/session')->setFormData(false);
//                    $this->_redirect('*/*/');
//                    return;
                } else {
                    // update oceansoft_sales_checklist_group
                    $groupCollection = Mage::getModel('salesmanagerment/checklistgroup')->getCollection()
                        ->addFieldToFilter('checklist_id', $checklist_id);
                    foreach ($groupCollection as $group_collection) {
                        $group_collection->delete();
                    }
                    if ($postData['group'] && isset($postData['group']['value'])) {
                        foreach ($postData['group']['value'] as $data_group) {
                            $groupModel = Mage::getModel('salesmanagerment/checklistgroup');
                            $groupModel->setChecklistId($checklist_id);
                            $groupModel->setSaleName($data_group['salename']);
                            $groupModel->setValue($data_group['salevalue']);
                            $groupModel->save();
                        }
                    }
                    // update oceansoft_sales_checklist
                    $checklistData = $postData;
                    unset($checklistData['group']);
                    $checkListModel->addData($checklistData)
                        ->setChecklistId($checklist_id)
                        ->save();
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