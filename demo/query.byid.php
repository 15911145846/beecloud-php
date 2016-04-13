<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>BeeCloud<?php echo $title;?>订单查询示例</title>
</head>
<body>
<table border="1" align="center" cellspacing=0>
<?php
require_once("../loader.php");

$data = array();
$appSecret = "c37d661d-7e61-49ea-96a5-68c34e83db3b";
$data["app_id"] = "c37d661d-7e61-49ea-96a5-68c34e83db3b";
$data["timestamp"] = time() * 1000;
$data["app_sign"] = md5($data["app_id"] . $data["timestamp"] . $appSecret);
$data["id"] = $_GET['id'];
$type = $_GET['type'];

if(empty($data["id"])){
    exit(json_encode(array('result_code' => 1, 'err_detail' => '请输入id')));
}
try {
    switch($type){
        case 'bill':
            $result = $api->bill($data, 'get');
            if ($result->result_code != 0 || $result->result_msg != "OK") {
                echo json_encode($result);
                exit();
            }
            $data = $result->pay;
            $str = "<tr><th>是否支付</th><th>创建时间</th><th>总价(分)</th><th>渠道类型</th><th>订单号</th><th>订单标题</th></tr>";
            if($data){
                $create_time = isset($data->create_time) && $data->create_time ? date('Y-m-d H:i:s', $data->create_time/1000) : '';
                $spay_result = $data->spay_result ? '支付' : '未支付';
                $str .= "<tr><td>$spay_result</td><td>$create_time</td><td>$data->total_fee</td><td>$data->sub_channel</td><td>$data->bill_no</td><td>$data->title</td></tr>";
            }
            echo $str;
            break;
        case 'refund':
            $result = $api->refund($data, 'get');
            if ($result->result_code != 0 || $result->result_msg != "OK") {
                echo json_encode(array('result_code' => 1, 'err_detail' => $result->err_detail));
                exit();
            }
            $data = $result->refund;
            $str = "<tr><th>退款是否成功</th><th>退款创建时间</th><th>退款号</th><th>订单金额(分)</th><th>退款金额(分)</th><th>渠道类型</th><th>订单号</th><th>退款是否完成</th><th>订单标题</th></tr>";
            if($data){
                $create_time = isset($data->create_time) && $data->create_time ? date('Y-m-d H:i:s', $data->create_time/1000) : '';
                $result = $data->result ? "成功" : "失败";
                $finish = $data->finish ? "完成" : "未完成";
                $str .= "<tr><td>$result</td><td>$create_time</td><td>$data->refund_no</td><td>$data->total_fee</td><td>$data->refund_fee</td><td>$data->sub_channel</td><td>$data->bill_no</td><td>$finish</td><td>$data->title</td></tr>";
            }
            echo $str;
            break;
    }
} catch (Exception $e) {
    die($e->getMessage());
}
?>
</table>
</body>
</html>
