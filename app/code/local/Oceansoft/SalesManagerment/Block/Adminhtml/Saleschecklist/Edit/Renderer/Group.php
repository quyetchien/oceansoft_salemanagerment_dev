<?php
class Oceansoft_SalesManagerment_Block_Adminhtml_Saleschecklist_Edit_Renderer_Group
    extends Mage_Adminhtml_Block_Widget
    implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * Initialize block
     */
    public function __construct()
    {
        $this->setTemplate('salesmanagerment/groups.phtml');
    }
    /**
     * Render HTML
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }
}