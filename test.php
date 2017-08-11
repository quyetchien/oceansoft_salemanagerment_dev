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

//            $myPrice = '143.500';
//            $created_at = 'Jul 22, 2017 5:19:26 AM';
//            $user_id = convertUserId('lily');
//            $order_id = '100017313';
//            $customer_email = 'lorcan@boom22.com';
//            $ticket_id = '17841';
//            $my_percent = 50;
//            $orderGrandTotal = $myPrice * 100/$my_percent;
//
//            $postDataGroup = array(
//                array(
//                    'saleid' => convertUserId('brian'),
//                    'salevalue' => 50,
//                ),
//            );

            $total_earn = _calculationEarnPrice($myPrice, $created_at, $user_id);
            $checkListModel
                ->setOrderId($order_id)
                ->setCustomerEmail($customer_email)
                ->setTicketId($ticket_id)
                ->setPrice($myPrice)
                ->setCreatedAt($created_at)
                ->setUser($user_id)
                ->save();
            $checklist_id = $checkListModel->getId();
            if($checklist_id){
                // import report for me
                _importSalesReport(array(
                    'user_id' => $user_id,
                    'value' => $my_percent,
                    'price' => $myPrice,
                    'total_earn' => $total_earn,
                    'order_id' => $order_id,
                    'checklist_id' => $checklist_id,
                    'created_at' => $created_at
                ));

                // import report for group
                foreach($postDataGroup as $post_group){
                    $group_price = $orderGrandTotal * $post_group['salevalue'] / 100;
                    $group_total_earn = _calculationEarnPrice($group_price, $created_at, $post_group['saleid']);
                    _importSalesReport(array(
                        'user_id' => $post_group['saleid'],
                        'value' => $post_group['salevalue'],
                        'price' => $group_price,
                        'total_earn' => $group_total_earn,
                        'order_id' => $order_id,
                        'checklist_id' => $checklist_id,
                        'created_at' => $created_at
                    ));
                }
            }
        }
    }

}catch (Exception $e){
    echo $e->getMessage();
}

function _importSalesReport($dataImport){
    if(!$dataImport){
        return false;
    }
    $salesReportModel = Mage::getModel('salesmanagerment/salesreport');
    try{
        $salesReportModel
            ->addData($dataImport)
            ->save();
    }catch (Exception $e){
        return false;
    }
    return true;
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

function _calculationEarnPrice($price, $time, $user_id){
    $time =  date("Y-m-d H:i:s",strtotime($time));
    $user_revenue = Mage::getModel('salesmanagerment/revenue')
        ->getCollection()
        ->addFieldToFilter('user_id', $user_id);
    if($user = $user_revenue->getData()){
        $user = $user[0];
        if($time >= $user['from'] && $time <= $user['to']){
            $userRule = unserialize($user['rule']);
            foreach($userRule as $rule){
                if(_checkTimeInCondition($rule['from'], $rule['to'], $time)){
                    return $price * $rule['value'] / 100;
                }
            }
        }
    }
    return 0;
}

function _checkTimeInCondition($from, $to, $current){
    $from = strtotime($from);
    $to = strtotime($to);
    $current = strtotime(date("H:i:s",strtotime($current)));
    if($to < $from){
        $to = $to + 86400;
        if($current < $from){
            $current = $current + 86400;
        }
    }
    if($from <= $current && $current <= $to){
        return true;
    }
    return false;
}



