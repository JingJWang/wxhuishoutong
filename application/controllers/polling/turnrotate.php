<?php
header('Content-type:text/html;charset=utf-8;');
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Turnrotate extends CI_Controller {
    
    /**
     * 更新转盘的奖励库存
     */
    function shiprew(){     
        $this->load->model('polling/turnrotate_model');
        $result = $this->turnrotate_model->getrew();
        foreach ($result as $k => $v) {
            if ($v['totime']>strtotime(date('Y-m-d'))) {
                continue;//没到更新的时间 
            }
            if ($v['stock']<=0 || $v['evednum']<=0 || $v['evednum']==$v['number']) {
                continue;//库存没有、每日不给奖励、每天要给的和已经上架要给出的数量相同，不用更新。
            }
            $re = $this->turnrotate_model->getship($v);
            echo 'OK<br>';
        }
    }
}