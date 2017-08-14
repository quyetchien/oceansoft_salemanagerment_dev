<pre>
<?php
define('MAGENTO_ROOT', getcwd());
$compilerConfig = MAGENTO_ROOT . '/includes/config.php';
if (file_exists($compilerConfig)) {
    include $compilerConfig;
}
$mageFilename = MAGENTO_ROOT . '/app/Mage.php';
require MAGENTO_ROOT . '/app/bootstrap.php';
require_once $mageFilename;
ini_set('display_errors', 1);
Mage::app();


$string = 'Aug 1, 2017 5:10:45 PM';
echo date("H:i:s",strtotime($string));
// MY CODE

try{
    $resource = Mage::getSingleton('core/resource');
    $readConnection = $resource->getConnection('core_read');
    $writeConnection = $resource->getConnection('core_write');

    $readConnection = $resource->getConnection('core_read');
    $query = "SELECT * FROM WTF WHERE order_id";
    $results = $readConnection->fetchAll($query);
    if($results){
        foreach($results as $note){
            $checkListModel = Mage::getModel('salesmanagerment/checklist');

            $shift = _checkTimeInCondition($note['created_at']);
            $data = array(
                'order_id' => $note['order_id'],
                'customer_email' => $note['customer_email'],
                'ticket_id' => $note['ticket_id'],
                'price' => $note['price'],
                'sale_percentage' => $note['sale_percentage'],
                'note' => $note['note'],
                'refund' => 0,
                'shift' => $shift,
                'order_date' => $note['created_at'],
                'user' => convertUserId($note['user']),
                'total_earn' => _calculationEarnPrice($note['price'], $note['created_at'], $shift, convertUserId($note['user']))
            );
            $checkListModel
                ->addData($data)
                ->save();

        }
    }

}catch (Exception $e){
    echo $e->getMessage();
}

function _calculationEarnPrice($price, $order_date, $shift, $user_id){
    $user_revenue = Mage::getModel('salesmanagerment/revenue')
        ->getCollection()
        ->addFieldToFilter('user_id', $user_id);
    if($user = $user_revenue->getData()){
        $user = $user[0];
        $userRule = unserialize($user['rule']);
        foreach($userRule as $rule){
            if($shift == $rule['shift']){
                return $price * $rule['value'] / 100;
            }
        }
    }
    return 0;
}

function convertUserId($user_name){
    switch ($user_name){
        case "sofia" : return 7;
        case "brian" : return 8;
        case "lily" : return 9;
        case "julia" : return 10;
        case  "carol" : return 11;
        default : return false;
    }
}

function _checkTimeInCondition($current){
    $time_current = date("H:i:s",strtotime($current));
    if($time_current >= '08:00:00' && $time_current <= '17:45:00'){
        return 1;
    }
    if($time_current >= '17:46:00' && $time_current <= '22:30:00'){
        return 2;
    }
    return 3;
}



