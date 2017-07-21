<?php

class Oceansoft_SalesManagerment_Helper_Data extends Mage_Core_Helper_Abstract
{
    const USER_ROLE = 'Sale';

    public function getSaleUser(){
        $model = Mage::getModel('admin/role');
        $role = $model->getCollection()
            ->addFieldToFilter('role_name', ['eq' => self::USER_ROLE])
            ->getFirstItem();
        $output = array();
        if ($roleId = $role->getId())
        {
            $staffUsers = $model->getCollection()
                ->addFieldToFilter('parent_id', ['eq' => $roleId]);
            if ($staffUsers->getSize())
            {
                foreach ($staffUsers as $staffUser)
                {
                    if ($staffUser->getUserId())
                    {
                        $user = Mage::getModel('admin/user')->load($staffUser->getUserId());
                        $output[$user->getId()] = $user->getUsername();
                    }
                }
            }
        }
        return $output;
    }

    public function getUserNameById($user_id){
        $user = Mage::getModel('admin/user')->load($user_id);
        return $user->getUsername();
    }

    public function createPickerTime( $default = '19:00', $interval = '+1 minutes') {

        $output = '';
        $current = strtotime( '00:00' );
        $end = strtotime( '23:59' );

        while( $current <= $end ) {
            $time = date( 'H:i', $current );
            $sel = ( $time == $default ) ? ' selected' : '';

            $output .= "<option value=\"{$time}\"{$sel}>" . date( 'H:i', $current ) .'</option>';
            $current = strtotime( $interval, $current );
        }

        return $output;
    }

}