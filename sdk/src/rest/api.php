<?php
/*
 * php version >= 5.3
 *
 */
namespace beecloud;

namespace beecloud\rest;

const UNEXPECTED_RESULT = "非预期的返回结果:";
const NEED_PARAM = "需要必填字段:";
const NEED_VALID_PARAM = "字段值不合法:";
const NEED_WX_JSAPI_OPENID = "微信公众号支付(WX_JSAPI) 需要openid字段";
const NEED_RETURN_URL = "当channel参数为 ALI_WEB 或 ALI_QRCODE 或 UN_WEB 或JD_WAP 或 JD_WEB时 return_url为必填";
const NEED_IDENTITY_ID = "当channel参数为 YEE_WAP时 identity_id为必填";
const BILL_TIMEOUT_ERROR = "当channel参数为 JD* 不支持bill_timeout";
const NEED_QR_PAY_MODE = '当channel参数为 ALI_QRCODE时 qr_pay_mode为必填';
const NEED_CARDNO = '当channel参数为 YEE_NOBANKCARD时 cardno为必填';
const NEED_CARDPWD = '当channel参数为 YEE_NOBANKCARD时 cardpwd为必填';
const NEED_FRQID = '当channel参数为 YEE_NOBANKCARD时 frqid为必填';


class api {
    const URI_BILL = "/2/rest/bill";  			//支付
    const URI_REFUND = "/2/rest/refund";		//退款 预退款批量审核 退款订单查询(制定id)
    const URI_BILLS = "/2/rest/bills";			//订单查询
    const URI_BILLS_COUNT = "/2/rest/bills/count";		//订单总数查询
    const URI_REFUNDS = "/2/rest/refunds";			//退款查询
    const URI_REFUNDS_COUNT = "/2/rest/refunds/count"; //退款总数查询
    const URI_REFUND_STATUS = "/2/rest/refund/status"; //退款状态更新
    
    const URI_TRANSFERS = "/2/rest/transfers"; //批量打款 - 支付宝
    const URI_TRANSFER = "/2/rest/transfer";  //单笔打款 - 支付宝/微信
    const URI_BC_TRANSFER = "/2/rest/bc_transfer"; //代付 - 银行卡
    
    const URI_OFFLINE_BILL = '/2/rest/offline/bill'; //线下支付-撤销订单
    const URI_OFFLINE_BILL_STATUS = '/2/rest/offline/bill/status'; //线下订单状态查询
    const URI_OFFLINE_REFUND = '/2/rest/offline/refund'; //线下退款

    const URI_GATEWAY_WITHDRAW = '/2/rest/gateway/withdraw'; //提现申请/提现审批
    const URI_GATEWAY_AMOUNT = '/2/rest/gateway/amount'; //余额查询/余额修改

    static final private function baseParamCheck(array $data) {
        if (!isset($data["app_id"])) {
            throw new \Exception(NEED_PARAM . "app_id");
        }

        if (!isset($data["timestamp"])) {
            throw new \Exception(NEED_PARAM . "timestamp");
        }

        if (!isset($data["app_sign"])) {
            throw new \Exception(NEED_PARAM . "app_sign");
        }
    }

    static final protected function post($api, $data, $timeout, $returnArray) {
        $url = \beecloud\network::getApiUrl() . $api;
        $httpResultStr = \beecloud\network::request($url, "post", $data, $timeout);
        $result = json_decode($httpResultStr, !$returnArray ? false : true);
        if (!$result) {
            throw new \Exception(UNEXPECTED_RESULT . $httpResultStr);
        }
        return $result;
    }

    static final protected function get($api, $data, $timeout, $returnArray) {
        $url = \beecloud\network::getApiUrl() . $api;
        $httpResultStr = \beecloud\network::request($url, "get", $data, $timeout);
        $result = json_decode($httpResultStr,!$returnArray ? false : true);
        if (!$result) {
            throw new \Exception(UNEXPECTED_RESULT . $httpResultStr);
        }
        return $result;
    }
    
    static final protected function put($api, $data, $timeout, $returnArray) {
        $url = \beecloud\network::getApiUrl() . $api;
        $httpResultStr = \beecloud\network::request($url, "put", $data, $timeout);
        $result = json_decode($httpResultStr,!$returnArray ? false : true);
        if (!$result) {
            throw new \Exception(UNEXPECTED_RESULT . $httpResultStr);
        }
        return $result;
    }

    /**
     * @param array $data
     * @return mixed
     * @throws \Exception
     */
    static final public function bill(array $data, $method = 'post') {
        //param validation
        self::baseParamCheck($data);

        if (isset($data["channel"])) {
	        switch ($data["channel"]) {
	            case "WX_JSAPI":
	                if (!isset($data["openid"])) {
	                    throw new \Exception(NEED_WX_JSAPI_OPENID);
	                }
	                break;
	        	case "ALI_QRCODE":
	        		if (!isset($data["qr_pay_mode"])) {
	                    throw new \Exception(NEED_QR_PAY_MODE);
	                }
	            	break;
	            case "ALI_WEB":
	            case "ALI_QRCODE":
	            case 'JD_WEB':
	            case 'JD_WAP': 
	            case "UN_WEB":
	                if (!isset($data["return_url"])) {
	                    throw new \Exception(NEED_RETURN_URL);
	                }
	                break;
	            case "YEE_WAP":
	                if (!isset($data["identity_id"])) {
	                    throw new \Exception(NEED_IDENTITY_ID);
	                }
	                break;
	            case "YEE_NOBANKCARD":
	        		if (!isset($data["cardno"])) {
	                    throw new \Exception(NEED_CARDNO);
	                }
	        		if (!isset($data["cardpwd"])) {
	                    throw new \Exception(NEED_CARDPWD);
	                }
	        		if (!isset($data["frqid"])) {
	                    throw new \Exception(NEED_FRQID);
	                }
	            	break;
	            case "JD":
	            case "JD_WEB":
	            case "JD_WAP":
	                if (isset($data["bill_timeout"])) {
	                    throw new \Exception(BILL_TIMEOUT_ERROR);
	                }
	                break;
	            case "KUAIQIAN":
	            case "KUAIQIAN_WAP":
	            case "KUAIQIAN_WEB":
	                if (isset($data["bill_timeout"])) {
	                    throw new \Exception(BILL_TIMEOUT_ERROR);
	                }
	                break;
	            case "WX_APP":
	            case "WX_NATIVE":
	            case "ALI_APP":
	            case "UN_APP":
	            case "ALI_WAP":
	            case "ALI_OFFLINE_QRCODE":
	            case "YEE":
	            case "YEE_WEB":
	            case "BD":
	            case "BD_WAP":
	            case "BD_WEB":
	            case "PAYPAL_SANDBOX":
	            case "PAYPAL_LIVE":
	                break;
	            default:
	                throw new \Exception(NEED_VALID_PARAM . "channel");
	                break;
	        }
    	}
        
        switch ($method) {
        	case 'get'://支付订单查询
        		if (!isset($data["id"])) {
		            throw new \Exception(NEED_PARAM . "id");
		        }
		        $order_id = $data["id"];
		        unset($data["id"]);
		        return self::get(self::URI_BILL.'/'.$order_id, $data, 30, false);
        		break;
        	case 'post': // 支付
        	default:
        		if (!isset($data["channel"])) {
        			throw new \Exception(NEED_PARAM . "channel");
        		}
        		if (!isset($data["total_fee"])) {
		            throw new \Exception(NEED_PARAM . "total_fee");
		        } else if(!is_int($data["total_fee"]) || 1>$data["total_fee"]) {
		            throw new \Exception(NEED_VALID_PARAM . "total_fee");
		        }
		
		        if (!isset($data["bill_no"])) {
		            throw new \Exception(NEED_PARAM . "bill_no");
		        } 
		    	if (!preg_match('/^[0-9A-Za-z]{8,32}$/', $data["bill_no"])) {
		        	throw new \Exception(NEED_VALID_PARAM . "bill_no");
		        }
		
		        if (!isset($data["title"])) {
		            //TODO: 字节数
		            throw new \Exception(NEED_PARAM . "title");
		        }
		        return self::post(self::URI_BILL, $data, 30, false);
        		break;
        }
    }
    
	static final public function bills(array $data) {
        //required param existence check
        self::baseParamCheck($data);
 		self::channelCheck($data);

        //param validation
        return self::get(self::URI_BILLS, $data, 30, false);
    }
    
    
    static final public function bills_count(array $data){
    	self::baseParamCheck($data);
    	self::channelCheck($data);
    	
    	if (isset($data["bill_no"]) && !preg_match('/^[0-9A-Za-z]{8,32}$/', $data["bill_no"])) {
        	throw new \Exception(NEED_VALID_PARAM . "bill_no");
		}
    	return self::get(self::URI_BILLS_COUNT, $data, 30, false);
    }

    static final public function refund(array $data, $method = 'post') {
        //param validation
        self::baseParamCheck($data);

        if (isset($data["channel"])) {
            switch ($data["channel"]) {
                case "ALI":
                case "UN":
                case "WX":
                case "JD":
                case "KUAIQIAN":
                case "YEE":
                case "BD":
                    break;
                default:
                    throw new \Exception(NEED_VALID_PARAM . "channel");
                    break;
            }
        }

        switch ($method){
        	case 'put': //预退款批量审核
        		if (!isset($data["channel"])) {
		            throw new \Exception(NEED_PARAM . "channel");
		        }
        		if (!isset($data["ids"])) {
		            throw new \Exception(NEED_PARAM . "ids");
		        }
		        if (!is_array($data["ids"])) {
		            throw new \Exception(NEED_VALID_PARAM . "ids(array)");
		        }
        		if (!isset($data["agree"])) {
		            throw new \Exception(NEED_PARAM . "agree");
		        }
		        return self::put(self::URI_REFUND, $data, 30, false);
        		break;
        	case 'get'://退款订单查询
        		if (!isset($data["id"])) {
		            throw new \Exception(NEED_PARAM . "id");
		        }
		        $order_id = $data["id"];
		        unset($data["id"]);
		        return self::get(self::URI_REFUND.'/'.$order_id, $data, 30, false);
        		break;
        	case 'post': //退款
        	default :
        		if (!isset($data["bill_no"])) {
					throw new \Exception(NEED_PARAM . "bill_no");
				}
		    	if (!preg_match('/^[0-9A-Za-z]{8,32}$/', $data["bill_no"])) {
		        	throw new \Exception(NEED_VALID_PARAM . "bill_no");
		        }
		        
		    	if (!isset($data["refund_no"])) {
		            throw new \Exception(NEED_PARAM . "refund_no");
		        }
		        if (!preg_match('/^\d{8}[0-9A-Za-z]{3,24}$/', $data["refund_no"]) || preg_match('/^\d{8}0{3}/', $data["refund_no"])) {
		        	throw new \Exception(NEED_VALID_PARAM . "refund_no");
		        }
		        
		    	if(!is_int($data["refund_fee"]) || 1>$data["refund_fee"]) {
		            throw new \Exception(NEED_VALID_PARAM . "refund_fee");
		        }
		        return self::post(self::URI_REFUND, $data, 30, false);
        		break;
        }
    }


    static final public function refunds(array $data) {
        //required param existence check
        self::baseParamCheck($data);
        self::channelCheck($data);
        //param validation
        return self::get(self::URI_REFUNDS, $data, 30, false);
    }
    
	static final public function refunds_count(array $data) {
        //required param existence check
        self::baseParamCheck($data);
        self::channelCheck($data);
        //param validation
        return self::get(self::URI_REFUNDS_COUNT, $data, 30, false);
    }
    

    static final public function refundStatus(array $data) {
        //required param existence check
        self::baseParamCheck($data);

        switch ($data["channel"]) {
            case "WX":
            case "YEE":
            case "KUAIQIAN":
            case "BD":
                break;
            default:
                throw new \Exception(NEED_VALID_PARAM . "channel");
                break;
        }

        if (!isset($data["refund_no"])) {
            throw new \Exception(NEED_PARAM . "refund_no");
        }
        //param validation
        return self::get(self::URI_REFUND_STATUS, $data, 30, false);
    }
    
    //单笔打款 - 支付宝/微信红包
    static final public function transfer(array $data) {
        self::baseParamCheck($data);
        switch ($data["channel"]) {
            case "WX_REDPACK":
        		if (!isset($data['redpack_info'])) {
					throw new \Exception(NEED_PARAM . 'redpack_info');
				}
                break;
            case "WX_TRANSFER":
                break;
            case "ALI_TRANSFER":
                $aliRequireNames = array(
                    "channel_user_name",
                    "account_name"
                );

                foreach($aliRequireNames as $v) {
                    if (!isset($data[$v])) {
                        throw new \Exception(NEED_PARAM . $v);
                    }
                }
                break;
            default:
                throw new \Exception(NEED_VALID_PARAM . "channel = ALI_TRANSFER | WX_TRANSFER | WX_REDPACK");
                break;
        }

        $requiedNames = array("transfer_no",
            "total_fee",
            "desc",
            "channel_user_id"
            );

        foreach($requiedNames as $v) {
            if (!isset($data[$v])) {
                throw new \Exception(NEED_PARAM . $v);
            }
        }

        return self::post(self::URI_TRANSFER, $data, 30, false);
    }

    //批量打款 - 支付宝
    static final public function transfers(array $data) {
        self::baseParamCheck($data);
        switch ($data["channel"]) {
            case "ALI":
                break;
            default:
                throw new \Exception(NEED_VALID_PARAM . "channel only ALI");
                break;
        }

        if (!isset($data["batch_no"])) {
            throw new \Exception(NEED_PARAM . "batch_no");
        }

        if (!isset($data["account_name"])) {
            throw new \Exception(NEED_PARAM . "account_name");
        }

        if (!isset($data["transfer_data"])) {
            throw new \Exception(NEED_PARAM . "transfer_data");
        }

        if (!is_array($data["transfer_data"])) {
            throw new \Exception(NEED_VALID_PARAM . "transfer_data(array)");
        }

        return self::post(self::URI_TRANSFERS, $data, 30, false);
    }
    
    //代付 - 银行卡
    static final public function bc_transfer(array $data) {
        self::baseParamCheck($data);
        $params = array(
        	'bill_no', 'title', 'trade_source', 'bank_code', 'bank_associated_code', 'bank_fullname', 
        	'card_type', 'account_type', 'account_no', 'account_name'
        );
		
        foreach ($params as $v) {
         	if (!isset($data[$v])) {
                throw new \Exception(NEED_PARAM . $v);
            }
        }
        return self::post(self::URI_BC_TRANSFER, $data, 30, false);
    }
    
    
	static final public function offline_bill(array $data) {
        self::baseParamCheck($data);
        if (isset($data["channel"])) {
	        switch ($data["channel"]) {
	            case "WX_SCAN":
	            case "ALI_SCAN":
	            	if (!isset($data['method']) && !isset($data['auth_code'])) {
	            		throw new \Exception(NEED_PARAM . "auth_code");
	            	}
	            	break;
	            case "WX_NATIVE":
	            case "ALI_OFFLINE_QRCODE":
	            case "SCAN":
	                break;
	            default:
	                throw new \Exception(NEED_VALID_PARAM . "channel = WX_NATIVE | WX_SCAN | ALI_OFFLINE_QRCODE | ALI_SCAN | SCAN");
	                break;
	        }
        }

        if (!isset($data["bill_no"])) {
            throw new \Exception(NEED_PARAM . "bill_no");
        }
        if (!preg_match('/^[0-9A-Za-z]{8,32}$/', $data["bill_no"])) {
            throw new \Exception(NEED_VALID_PARAM . "bill_no");
        }
        
        if (!isset($data['method'])) {
			if (!isset($data["channel"])) {
				throw new \Exception(NEED_PARAM . "channel");
			}
			if (!isset($data["total_fee"])) {
	            throw new \Exception(NEED_PARAM . "total_fee");
	        } else if(!is_int($data["total_fee"]) || 1>$data["total_fee"]) {
	            throw new \Exception(NEED_VALID_PARAM . "total_fee");
	        }
	
	        if (!isset($data["title"])) {
	            throw new \Exception(NEED_PARAM . "title");
	        }
        }
        return self::post(self::URI_OFFLINE_BILL, $data, 30, false);
    }
    
	static final public function offline_bill_status(array $data) {
        self::baseParamCheck($data);
        
        if (isset($data["channel"])) {
	        switch ($data["channel"]) {
	            case "WX_SCAN":
	            case "ALI_SCAN":
	            case "WX_NATIVE":
	            case "ALI_OFFLINE_QRCODE":
	                break;
	            default:
	                throw new \Exception(NEED_VALID_PARAM . "channel = WX_NATIVE | WX_SCAN | ALI_OFFLINE_QRCODE | ALI_SCAN");
	                break;
	        }
        }

		if (!isset($data["bill_no"])) {
			throw new \Exception(NEED_PARAM . "bill_no");
		}
    	if (!preg_match('/^[0-9A-Za-z]{8,32}$/', $data["bill_no"])) {
        	throw new \Exception(NEED_VALID_PARAM . "bill_no");
        }
        return self::post(self::URI_OFFLINE_BILL_STATUS, $data, 30, false);
    }
    
    static final private function offline_refund(array $data){
    	self::baseParamCheck($data);
        if (isset($data['channel'])) {
	        switch ($data["channel"]) {
	            case "ALI":
	            case "WX":
	                break;
	            default:
	                throw new \Exception(NEED_VALID_PARAM . "channel = ALI | WX");
	                break;
	        }
        }

		if (!isset($data["refund_fee"])) {
            throw new \Exception(NEED_PARAM . "refund_fee");
        } else if(!is_int($data["refund_fee"]) || 1>$data["refund_fee"]) {
            throw new \Exception(NEED_VALID_PARAM . "refund_fee");
        }

		if (!isset($data["bill_no"])) {
			throw new \Exception(NEED_PARAM . "bill_no");
		}
    	if (!preg_match('/^[0-9A-Za-z]{8,32}$/', $data["bill_no"])) {
        	throw new \Exception(NEED_VALID_PARAM . "bill_no");
        }
        
    	if (!isset($data["refund_no"])) {
            throw new \Exception(NEED_PARAM . "refund_no");
        }
        if (!preg_match('/^\d{8}[0-9A-Za-z]{3,24}$/', $data["refund_no"]) || preg_match('/^\d{8}0{3}/', $data["refund_no"])) {
        	throw new \Exception(NEED_VALID_PARAM . "refund_no");
        }

        return self::post(self::URI_OFFLINE_BILL, $data, 30, false);
    }
    
    
    static final private function channelCheck($data){
    	if (isset($data["channel"])) {
            switch ($data["channel"]) {
                case "ALI":
                case "ALI_WEB":
                case "ALI_WAP":
                case "ALI_QRCODE":
                case "ALI_APP":
                case "ALI_OFFLINE_QRCODE":
                case "UN":
                case "UN_WEB":
                case "UN_APP":
                case "WX":
                case "WX_JSAPI":
                case "WX_NATIVE":
                case "WX_APP":
                case "JD":
                case "JD_WEB":
                case "JD_WAP":
                case "YEE":
                case "YEE_WAP":
                case "YEE_WEB":
                case "YEE_NOBANKCARD":
                case "KUAIQIAN":
                case "KUAIQIAN_WAP":
                case "KUAIQIAN_WEB":
                case "BD":
                case "BD_WAP":
                case "BD_WEB":
                case "PAYPAL":
                case "PAYPAL_SANDBOX":
                case "PAYPAL_LIVE":
                    break;
                default:
                    throw new \Exception(NEED_VALID_PARAM . "channel");
                    break;
            }
        }
    }

    static final public function gateway_withdraw($data, $method){
        self::baseParamCheck($data);

        switch($method){
            case 'post':
                $validFields = array('bank_account_name', 'bank_account_no', 'bank_name', 'branch_bank_name',
                        'subbranch_bank_name', 'is_personal', 'bank_province', 'bank_city', 'note',
                        'email', 'withdraw_amount'
                );
                foreach($validFields as $v) {
                    if (!isset($data[$v])) {
                        throw new \Exception(NEED_PARAM . $v);
                    }
                }
                return self::post(self::URI_GATEWAY_WITHDRAW, $data, 30, false);
                break;
            case 'put':
                $validFields = array('withdraw_id', 'agree');
                foreach($validFields as $v) {
                    if (!isset($data[$v])) {
                        throw new \Exception(NEED_PARAM . $v);
                    }
                }
                return self::put(self::URI_GATEWAY_WITHDRAW, $data, 30, false);
                break;
        }
    }

    static final public function gateway_amount($data, $method){
        self::baseParamCheck($data);
        switch($method){
            case 'post':
                if (!isset($data['email'])) {
                    throw new \Exception(NEED_PARAM . 'email');
                }
                return self::get(self::URI_GATEWAY_AMOUNT, $data, 30, false);
                break;
            case 'put':
                $validFields = array('email', 'delta_amount');
                foreach($validFields as $v) {
                    if (!isset($data[$v])) {
                        throw new \Exception(NEED_PARAM . $v);
                    }
                }
                if(!is_numeric($data['delta_amount'])){
                    throw new \Exception(NEED_VALID_PARAM.'delta_amount');
                }
                return self::put(self::URI_GATEWAY_AMOUNT, $data, 30, false);
                break;
        }
    }
}