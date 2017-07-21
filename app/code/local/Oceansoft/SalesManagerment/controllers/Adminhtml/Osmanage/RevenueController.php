<?php

class Oceansoft_SalesManagerment_Adminhtml_Osmanage_RevenueController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
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
            ->_setActiveMenu('salesmanagerment/revenue')
            ->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Ocean Sale Revenue'), Mage::helper('adminhtml')->__('Ocean Sale Revenue')
            );
        return $this;
    }

    public function editAction()
    {
        $configId = $this->getRequest()->getParam('id');
        $configModel = Mage::getModel('salesmanagerment/revenue')->load($configId);

        if ($configModel->getId() || $configId == 0)
        {
            Mage::register('salesmanagerment_data', $configModel);
            $this->loadLayout();
            $this->_setActiveMenu('salesmanagerment/revenue');
            $this->_addBreadcrumb('Revenue', 'Revenue');
            $this->getLayout()->getBlock('head')
                ->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()
                ->createBlock('salesmanagerment/adminhtml_revenue_edit'))
                ->_addLeft($this->getLayout()
                    ->createBlock('salesmanagerment/adminhtml_revenue_edit_tabs')
                );
            $this->renderLayout();
        }
        else
        {
            Mage::getSingleton('adminhtml/session')->addError('Revenue does not exist');
            $this->_redirect('*/*/');
        }
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function saveAction()
    {
        if ($this->getRequest()->getPost())
        {
            try {
                $postData = $this->getRequest()->getPost();
                $revenueModel = Mage::getModel('salesmanagerment/revenue');
                $rule = array();
                if($postData['rule'] && $postData['rule']['value']){
                    foreach($postData['rule']['value'] as $rule_value){
                        $rule[] = array(
                            'from' => $rule_value['rulefrom'],
                            'to' => $rule_value['ruleto'],
                            'value' => $rule_value['rulevalue']
                        );
                    }
                }
                $rule = serialize($rule);
                $revenueData = $postData;
                unset($revenueData['rule']);
                if( $this->getRequest()->getParam('id') <= 0 ) {
                    $revenueModel
                        ->addData($revenueData)
                        ->setRule($rule)
                        ->setId($this->getRequest()->getParam('id'))
                        ->save();

                    Mage::getSingleton('adminhtml/session')->addSuccess('successfully saved');
                    Mage::getSingleton('adminhtml/session')->setFormData(false);
                    $this->_redirect('*/*/');
                    return;
                }else{
                    $revenueModel->addData($revenueData)
                        ->setRule($rule)
                        ->setId($this->getRequest()->getParam('id'))
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

    public function deleteAction()
    {
        if($this->getRequest()->getParam('id') > 0)
        {
            try
            {
                $revenueModel = Mage::getModel('salesmanagerment/revenue');
                $revenueModel->setId($this->getRequest()->getParam('id'))->delete();
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
     * Check currently called action by permissions for current user
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('salesmanagerment/revenue');
    }
}