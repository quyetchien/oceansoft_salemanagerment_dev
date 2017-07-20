<?php

class Oceansoft_SalesManagerment_Adminhtml_Osmanage_ConfigController extends Mage_Adminhtml_Controller_Action
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
            ->_setActiveMenu('salesmanagerment/configuration')
            ->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Ocean Sale Config'), Mage::helper('adminhtml')->__('Ocean Sale Config')
            );
        return $this;
    }

    public function editAction()
    {
        $configId = $this->getRequest()->getParam('id');
        $configModel = Mage::getModel('salesmanagerment/oceansaleconfig')->load($configId);

        if ($configModel->getId() || $configId == 0)
        {
            Mage::register('salesmanagerment_data', $configModel);
            $this->loadLayout();
            $this->_setActiveMenu('salesmanagerment/configuration');
            $this->_addBreadcrumb('Configuration Manager', 'Configuration Manager');
            $this->getLayout()->getBlock('head')
                ->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()
                ->createBlock('salesmanagerment/adminhtml_configuration_edit'))
                ->_addLeft($this->getLayout()
                    ->createBlock('salesmanagerment/adminhtml_configuration_edit_tabs')
                );
            $this->renderLayout();
        }
        else
        {
            Mage::getSingleton('adminhtml/session')->addError('Config does not exist');
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
                $configModel = Mage::getModel('salesmanagerment/oceansaleconfig');

                if( $this->getRequest()->getParam('id') <= 0 ) {
                    $configModel
                        ->addData($postData)
                        ->setId($this->getRequest()->getParam('id'))
                        ->save();

                    Mage::getSingleton('adminhtml/session')->addSuccess('successfully saved');
                    Mage::getSingleton('adminhtml/session')->setFormData(false);
                    $this->_redirect('*/*/');
                    return;
                }else{
                    $configModel->addData($postData)
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
                $configModel = Mage::getModel('salesmanagerment/oceansaleconfig');
                $configModel->setId($this->getRequest()->getParam('id'))->delete();
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
        return Mage::getSingleton('admin/session')->isAllowed('salesmanagerment/configuration');
    }
}