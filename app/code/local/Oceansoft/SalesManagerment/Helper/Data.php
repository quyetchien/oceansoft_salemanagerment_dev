<?php

class Oceansoft_SalesManagerment_Helper_Data extends Mage_Core_Helper_Abstract
{
    const USER_ROLE = 'Sale';
    const ADMIN_ROLE = 'Administrators';

    public function getSaleUser(){
        $model = Mage::getModel('admin/role');
        $role = $model->getCollection()
            ->addFieldToFilter('role_name', self::USER_ROLE)
            ->getFirstItem();
        $output = array();
        if ($roleId = $role->getId())
        {
            $staffUsers = $model->getCollection()
                ->addFieldToFilter('parent_id', $roleId);
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

    public function checkIsAdminUser($user_id){
        $role_data = Mage::getModel('admin/user')->load($user_id)->getRole();
        $role_name = $role_data->getRoleName();
        if($role_name == self::ADMIN_ROLE){
            return true;
        }
        return false;
    }

    public function getUserNameById($user_id){
        $user = Mage::getModel('admin/user')->load($user_id);
        return $user->getUsername();
    }

    public function getUserIdByName($user_name){
        $user = Mage::getModel('admin/user')->getCollection()
            ->addFieldToFilter('username', $user_name);
        if($userData = $user->getData()){
            return $userData[0]['user_id'];
        }
        return false;
    }

}