<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Fund extends CI_Controller{
    
    function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->model('appsunny/reg_model');
        $this->load->model('appsunny/order_model');
        $this->load->model('appsunny/offer_model');
        $this->load->model('appsunny/fund_model');
    }
    
    /************公共类函数***************/
    /*
     * 判断请求方法是否为POST
     */
    private function method_is_post(){
        if ($this->input->server('REQUEST_METHOD') != "POST"){
            $result = array(
                'status' => $this->config->item('app_req_method_err'),
                'msg' => $this->lang->line('app_req_method_err'),
                'data' => ''
            );
            echo json_encode($result);
            exit();
        }
    }
    
    /*
     *  验证必填参数是否为空
     *  @param $args boolean 是有空参数.(逻辑与运算, True 没有空参数, False 有空参数)
     *  @return
     */
    private function param_is_null($args){
        if ($args == FALSE){
            $result = array(
                'status' => $this->config->item('app_param_null'),
                'msg' => $this->lang->line('app_param_null'),
                'data' => ''
            );
            echo json_encode($result);
            exit();
        }
    }
    
    /*
     *  验证参数类型是否正确.
     *  @param $args boolean 参数类型是否正确.(True 正确, False 错误)
     *  @return
     */
    private function param_type_is_right($args){
        if ($args == FALSE){
            $result = array(
                'status' => $this->config->item('app_param_err'),
                'msg' => $this->lang->line('app_param_err'),
                'data' => ''
            );
            echo json_encode($result);
            exit();
        }
    }
    /*
     *  更新用户令牌值.
     *  成功时返回True,失败时返回False.
     */
    private function update_user_token($user_id){
        $data = array(
            'user_id' => $user_id,
            'access_token' => sha1('maijin'.(string)time().(string)mt_rand(100000,999999)),
        );
        $result = $this->reg_model->update_user_token($data);
        return $result;
    }
    
    /*
     * 验证令牌是否有效(用户不存在,令牌错误,令牌过期)
     *
     */
    private function verify_token_valid($user_id,$access_token){
        $get_result = $this->reg_model->get_token($user_id);
        // 用户不存在
        if(!$get_result){
            $result = array(
                'status' => $this->config->item('app_user_null'),
                'msg' => $this->lang->line('app_user_null'),
                'data' => ''
            );
            echo json_encode($result);
            exit();
        }
        // 令牌错误
        if ($access_token != $get_result['access_token']){
            $result = array(
                'status' => $this->config->item('app_user_token_err'),
                'msg' => $this->lang->line('app_user_token_err'),
                'data' => ''
            );
            echo json_encode($result);
            exit();
        }
        // 令牌过期
        $date_now = time();
        if ($access_token == '-1' ||
            ($get_result['datetime'] + $this->config->item('cooperator_token_expire')) < $date_now){
            $this->update_user_token($user_id);
            $result = array(
                'status' => $this->config->item('app_user_token_expire'),
                'msg' => $this->lang->line('app_user_token_expire'),
                'data' => ''
            );
            echo json_encode($result);
            exit();
        }
    }
    
    /*
     *  验证用户状态(0待审核 1冻结 2未通过 3 通过) 和用户开关(-1 关闭 1 打开)
     */
    private function verify_user_status($user_id){
        $get_data = $this->offer_model->get_user_status($user_id);
        // 获取数据成功时.
        if ($get_data == TRUE){
            switch ($get_data['user_status']){
                case 0:
                    $result = array(
                    'status' => $this->config->item('app_user_wait'),
                    'msg' => $this->lang->line('app_user_wait'),
                    'data' => '',
                    );
                    echo json_encode($result);
                    exit();
                case 1:
                    $result = array(
                    'status' => $this->config->item('app_user_freeze'),
                    'msg' => $this->lang->line('app_user_freeze'),
                    'data' => '',
                    );
                    echo json_encode($result);
                    exit();
                case 2:
                    $result = array(
                    'status' => $this->config->item('app_user_fail'),
                    'msg' => $this->lang->line('app_user_fail'),
                    'data' => '',
                    );
                    echo json_encode($result);
                    exit();
                default:
            }
            if ($get_data['switchs'] == -1){
                $result = array(
                    'status' => $this->config->item('app_user_swich_close'),
                    'msg' => $this->lang->line('app_user_swich_close'),
                    'data' => ''
                );
                echo json_encode($result);
                exit();
            }
        }
        // 获取数据失败时.
        else{
            $result = array(
                'status' => $this->config->item('app_get_data_fail'),
                'msg' => $this->lang->line('app_get_data_fail'),
                'data' => ''
            );
            echo json_encode($result);
            exit();
        }
    }
    
    /*
     * 获取短信验证码
     */
    function msg(){
        $this->method_is_post();
        // 验证参数是否为空.
        $param_has_null = ($this->input->post('phone_number') && $this->input->post('timestamp')
            && $this->input->post('imei'));
        $this->param_is_null($param_has_null);
        // 获取参数值.
        $phone_number = $this->input->post('phone_number');
        // 验证是否为合法手机号.
        $param_type = preg_match('#^13[\d]{9}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#',
            $phone_number);
        $this->param_type_is_right($param_type);
        // 伪随即验证码
        $content = array(mt_rand(100000,1000000),$this->config->item('checkcode_Invalid_time')/60);
        // 发送短信验证码
        $this->load->library('alidayu/alimsg');
        $this->alimsg=new Alimsg();
        $this->alimsg->mobile=$phone_number;
        $this->alimsg->appkey=$this->config->item('alidayu_appkey');
        $this->alimsg->secret=$this->config->item('alidayu_secretKey');
        $this->alimsg->sign=$this->config->item('alidayu_signname');
        $this->alimsg->template=$this->config->item('APP_alidayu_cash_msg');
        $this->number=$content[0];
        $this->alimsg->content="{\"code\":\"".$this->number."\",\"minute\":\"5\"}";
        $response=$this->alimsg->SendVerifyCode();
        $data = array(
            'code_type' => 3, //3 表示回收商短信.
            'code_moblie' => $phone_number,
            'code_number' => $content[0],
            'code_jointime' => time(),
        );
        // 短信端口返回的结果值.
        if($response == FALSE){
            $data['response_status'] = $this->alimsg->code;
            $data['response_info'] = $this->alimsg->msg;
            $data['code_status'] = 0;
        }
        else{
            $data['response_status'] = $this->alimsg->code;
            $data['response_time'] = 0;
            $data['response_sid'] = $this->alimsg->msg;
            $data['code_status'] = 1;
        }
        // 短信信息存入数据库.
        $msg_result = $this->reg_model->save_verify_code($data);
        // 根据短信运营商返回的状态判断是否发送成功.
        if ($msg_result && $this->alimsg->code == '0'){
            $status = $this->config->item('app_success');
            $msg = $this->lang->line('app_success');
            $data = '';
        }
        else{
            $status = $this->config->item('app_send_code_fail');
            $msg = $this->lang->line('app_send_code_fail');
            $data = '';
        }
        $result = array(
            'status' => $status,
            'msg'    => $msg,
            'data'   => $data
        );
        echo json_encode($result);
        exit();
    }
    
    /*
     * 生成20位长度的支付编号
     */
    private function get_payid(){
        $number = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8).rand(1000,9999);
        return $number;
    }
    
    /*
     * 充值
     */
    function deposit(){
        // 验证请求类型是否为POST
        $this->method_is_post();
        // 验证参数是否为空.
        $param_has_null = ($this->input->post('user_id') && $this->input->post('timestamp')
            && $this->input->post('imei') && $this->input->post('access_token')
            && $this->input->post('pay_type') && $this->input->post('amount'));
        $this->param_is_null($param_has_null);
        // 获取参数
        $user_id = $this->input->post('user_id');
        $access_token = $this->input->post('access_token');
        $amount = $this->input->post('amount');
        $pay_type = $this->input->post('pay_type');
        // 检测参数长度或值是否合法.
        $user_id_len = (strlen($user_id) < 1 || strlen($user_id) > 32);
        $token_len = (strlen($access_token) < 1 || strlen($access_token) > 60);
        $amount_len = ($amount < 1);
        $pay_type_len = ($pay_type == '1' || $pay_type == '2' || $pay_type == '3');
        if ( $user_id_len || $token_len || $amount_len || !$pay_type_len){
            $result = array(
                'status' =>$this->config->item('app_param_illegal'),
                'msg' => $this->lang->line('app_param_illegal'),
                'data'=>'',
            );
            echo json_encode($result);
            exit();
        }
        // 验证用户身份.
        $this->verify_token_valid($user_id,$access_token);
        // 验证用户状态
        $this->verify_user_status($user_id);
        // 生成支付编号
        $pay_number = $this->get_payid();
        // 支付处理.
        switch ($pay_type){
            // 微信
            case 1:
                $this->load->library('wxsdk/wxpay');
                $info=array(
                    'body' => '账户充值',
                    'orderid' => $pay_number,
                    'pro_id' => $pay_number,
                    'moeny'=> $amount,
                    'type'=>'APP'
                );
                $pre_info = $this->wxpay->create_order($info);
                $pre_info['prepay_number'] = $pay_number;
                break;
                // 支付宝
            case 2:
                $pre_info ='';
                break;
                // 百度钱包
            case 3:
                $this->load->library('baifubao/pay_unlogin');
                $pre_info = $this->pay_unlogin->create_orderinfo($pay_type,array(
                    'order_no' => $pay_number,
                    'goods_name' => '账户充值',
                    'goods_desc' => '充值',
                    'total_amount' => $amount,
                    'buyer_sp_username' => $user_id,
                ));
                break; 
        }
        if ($pre_info == TRUE){
            $status = $this->config->item('app_success');
            $msg = $this->lang->line('app_success');
            $data = $pre_info;
        }
        else{
            $status = $this->config->item('app_pay_err');
            $msg = $this->lang->line('app_pay_err');
            $data = '';
        }
        $pre_result = array(
            'status' => $status,
            'msg' => $msg,
            'data' => $data,
        );
        echo json_encode($pre_result);
        exit();
    }
    
    /*
     * 查询支付结果
     */
    function query(){
        // 验证请求类型是否为POST
        $this->method_is_post();
        // 验证参数是否为空.
        $param_has_null = ($this->input->post('user_id') && $this->input->post('timestamp')
            && $this->input->post('imei') && $this->input->post('access_token')
            && $this->input->post('pay_type') && $this->input->post('prepay_number'));
        $this->param_is_null($param_has_null);
        // 获取参数
        $user_id = $this->input->post('user_id');
        $access_token = $this->input->post('access_token');
        $pay_type = $this->input->post('pay_type');
        $prepay_number = $this->input->post('prepay_number');
        // 检测参数长度或值是否合法.
        $user_id_len = (strlen($user_id) < 1 || strlen($user_id) > 32);
        $token_len = (strlen($access_token) < 1 || strlen($access_token) > 60);
        $prepay_number_len = (strlen($prepay_number) < 1 || strlen($prepay_number) > 20);
        $pay_type_len = ($pay_type == '1' || $pay_type == '2' || $pay_type == '3');
        if ( $user_id_len || $token_len || !$pay_type_len || $prepay_number_len){
            $result = array(
                'status' =>$this->config->item('app_param_illegal'),
                'msg' => $this->lang->line('app_param_illegal'),
                'data'=>'',
            );
            echo json_encode($result);
            exit();
        }
        // 验证用户身份.
        $this->verify_token_valid($user_id,$access_token);
        // 验证用户状态
        $this->verify_user_status($user_id);
        // 查询支付结果..
        switch ($pay_type){
            // 微信
            case 1:
                $this->load->library('wxsdk/wxpay');
                $info = $this->wxpay->order_query($prepay_number);
                if ($info == FALSE){
                    sleep(2);
                    $info = $this->wxpay->order_query($prepay_number);
                }
                if ($info == TRUE){
                    $info = $info['total_fee'];   // 单位：分
                }
                break;
                // 支付宝
            case 2:
                $info ='';
                break;
                // 百度钱包
            case 3:
                $this->load->library('baifubao/pay_unlogin');
                $info=$this->pay_unlogin->query_order($prepay_number);
                if ($info == FALSE){
                    // 第一次查询失败后,延时2s再次查询
                    sleep(2);
                    $info=$this->pay_unlogin->query_order($prepay_number);
                }
                if ($info == TRUE){
                    $info = $info['total_amount']; // 单位：分
                }
                break;
        }
        // 更新支付结果.
        $get_result = '';
        if ($info == TRUE){
            // 更新数据库数据。
            $data = array(
                'prepay' => $prepay_number,
                'user_id' => $user_id,
                'amount' => $info,
                'pay_type' => $pay_type,
            );
            $get_result = $this->fund_model->account_charge($data);
            
        }
        if ($info && $get_result){
            $status = $this->config->item('app_success');
            $msg = $this->lang->line('app_success');
            $data = '';
        }
        else{
            $status = $this->config->item('app_pay_err');
            $msg = $this->lang->line('app_pay_err');
            $data = '';
        }
        $result = array(
            'status' => $status,
            'msg'    => $msg,
            'data'   => $data,
        );
        echo json_encode($result);
        exit();
    }
	
    /*
     * 提现
     */
    function cash(){
        // 验证请求类型是否为POST
        $this->method_is_post();
        // 验证参数是否为空.
        $param_has_null = ($this->input->post('user_id') && $this->input->post('timestamp')
            && $this->input->post('imei') && $this->input->post('access_token')
             && $this->input->post('account') && $this->input->post('amount')
            && $this->input->post('verify_code') && $this->input->post('phone_number'));
        $this->param_is_null($param_has_null);
        // 获取参数
        $user_id = $this->input->post('user_id');
        $access_token = $this->input->post('access_token');
        $account = $this->input->post('account');
        $amount = $this->input->post('amount');
        $verify_code = $this->input->post('verify_code');
        $phone_number = $this->input->post('phone_number');
        // 检测参数长度或值是否合法.
        $user_id_len = (strlen($user_id) < 1 || strlen($user_id) > 32);
        $token_len = (strlen($access_token) < 1 || strlen($access_token) > 60);
        $account_len = (strlen($account) < 1);
        $amount_len = (is_numeric($amount) && (int)$amount >= 1);
        $verify_code_len = (strlen($verify_code) != 6);
        $param_type = (preg_match('#^13[\d]{9}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#',
            $phone_number) && is_numeric($verify_code));
        if ( $user_id_len || $token_len || $account_len || !$amount_len || $verify_code_len || !$param_type){
            $result = array(
                'status' =>$this->config->item('app_param_illegal'),
                'msg' => $this->lang->line('app_param_illegal'),
                'data'=>'',
            );
            echo json_encode($result);
            exit();
        }
        // 验证手机号和验证码是否匹配.
        $verify_result = $this->reg_model->check_code($phone_number,$verify_code);
        // 验证码错误.
        if ($verify_result == 0){
            $result = array(
                'status' => $this->config->item('app_param_code_err'),
                'msg' => $this->lang->line('app_param_code_err'),
                'data' => ''
            );
            echo json_encode($result);
            exit();
        }
        // 验证码过期
        if ($verify_result == 1){
            $result = array(
                'status' => $this->config->item('app_param_code_expire'),
                'msg' => $this->lang->line('app_param_code_expire'),
                'data' => ''
            );
            echo json_encode($result);
            exit();
        }
        // 验证用户身份.
        $this->verify_token_valid($user_id,$access_token);
        // 验证用户状态
        $this->verify_user_status($user_id);
        // 验证提现金额 
        $balance = $this->fund_model->get_balance($user_id);
        if ($balance == FALSE || $balance['balance'] < $amount){
            $result = array(
                'status' =>$this->config->item('app_param_illegal'),
                'msg' => $this->lang->line('app_param_illegal'),
                'data'=>'',
            );
            echo json_encode($result);
            exit();
        }
        // 保存到数据库
        $data = array(
            'user_id' => $user_id,
            'name' => $balance['name'],
            'mobile' => $balance['mobile'],
            'account' => $account,
            'amount' => $amount,
        );
        $result = $this->fund_model->add_cash($data);
        // 返回结果
        if ($result == TRUE){
            $status = $this->config->item('app_success');
            $msg = $this->lang->line('app_success');
        }
        else{
            $status = $this->config->item('app_update_data_fail');
            $msg = $this->lang->line('app_update_data_fail');
        }
        $result = array(
            'status' => $status,
            'msg'    => $msg,
            'data'   => '',
        );
        echo json_encode($result);
        exit();
    }
    
	
}
/* End of file fund.php */
/* Location: ./application/controllers/cooperation/fund.php */

