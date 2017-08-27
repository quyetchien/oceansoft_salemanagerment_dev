<?php

class Oceansoft_SalesManagerment_Block_Adminhtml_Salesreport_Render_Salary extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        // get revenue
        $revenue =  $user_revenue = Mage::getModel('salesmanagerment/revenue')
            ->getCollection()
            ->addFieldToFilter('user_id', $row->getUserId());
        if($data_revenue = $revenue->getData()){
            $user_revenue = $data_revenue[0]['revenue'];
            if($user_revenue < $row->getPrice()){
                $user_rule = unserialize($data_revenue[0]['rule']);
                $rule_shift_1_val = 1;
                foreach($user_rule as $rule){
                    if($rule['shift'] == 1){
                        $rule_shift_1_val = $rule['value'];
                        break;
                    }
                }

                // filter
                $filter = Mage::app()->getRequest()->getParam('filter', null);
                $data   = array();
                if (is_string($filter)) {
                    $data = Mage::helper('adminhtml')->prepareFilterString($filter);
                }
                if (isset($data['month']) && isset($data['year'])) {
                    $collection = Mage::getModel('salesmanagerment/checklist')->getCollection();
                    $collection->addFieldToFilter('main_table.order_date', array(
                        'from'  => $data['year'] . '-' . $data['month'] . '-01',
                        'to'  => $data['year'] . '-' . $data['month'] . '-31',
                        'date'  => true
                    ));
                    $collection->addFieldToFilter('user', $row->getUserId());
                    $collection->setOrder('order_id', 'ASC');

                    $icsPrice = 0;
                    $totalSalary = 0;
                    $checkTmp = false;
                    foreach($collection->getData() as $note){
                        if($icsPrice < $user_revenue){
                            $icsPrice += $note['price'];
                        }elseif($icsPrice == $user_revenue || $checkTmp){
                            $totalSalary += $note['total_earn'];
                        }elseif ($icsPrice > $user_revenue){
                            $checkTmp = true;
                            $totalSalary += ($icsPrice - $user_revenue) * ($rule_shift_1_val / 100);
                            $totalSalary += $note['total_earn'];
                        }
                    }
                    return $totalSalary;
                }
                return 0;
            }else{
                return ($user_revenue - $row->getPrice()) * (-5) / 100;
            }
        }
        return 0;
    }
}