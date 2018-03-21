<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0,user-scalable=no">
    <title>批量回收</title>
    <link rel="stylesheet" type="text/css" href="/static/m/css/cssReset.css" />
    <link rel="stylesheet" type="text/css" href="/static/m/css/batch.css" />
</head>
<body>
<div class="title">
    批量回收
    <a class="back" href="javascript:;">返回</a>
</div>
<div class="full-top"></div>
<div class="content">
    <form id="moredata">
    <div class="comprise">
        <div class="amount">
            <div class="words fl">手机数量</div>
            <div class="words fr">台</div>
            <div class="sum">
                <input id="amount" name="amount" type="text" class="number" placeholder="0"/>
            </div>
        </div>
        <div class="remark">
            <div class="words fl">备注</div>
            <div class="desc">
                <textarea class="note" name="oather" placeholder="手机品牌型号数量详情描述"></textarea>
            </div>
        </div>

        <div class="mode">
            <div class="cue fl">提现方式</div>
            <div class="way fr">银行卡</div>
        </div>

        <div class="information">
            <div class="covers">
                <div class="motif fl">姓名</div>
                <div class="case">
                    <input id="name" type="text" name="name" class="entry" placeholder="请输入您的姓名"/>
                </div>
            </div>
            <div class="covers">
                <div class="motif fl">银行卡号</div>
                <div class="case">
                    <input id="card" type="text" name="cardid" class="entry" placeholder="请输入您的银行卡号"/>
                </div>
            </div>
            <div class="covers">
                <div class="motif fl">开户行</div>
                <div class="case">
                    <input id="bank" type="text" name="cardname" class="entry" placeholder="请输入开户行"/>
                </div>
            </div>
        </div>
        <div class="illustrate fl">
            <div class="words fl">注：</div>
            <div class="side">
                <p>由于批量回收的特殊性，需填写银行卡信息方便大额交易；当回收金额≤10000时，会转入您的余额可通过微信提现；当回收金额＞10000时，会以银行卡方式转入您的账户</p>
            </div>
        </div>
    </div>
    </form>
</div>
<div class="fastener fl">
    <a class="submit" href="javascript:;" onclick="More.submit();">提交</a>
</div>
<script type="text/javascript" charset='utf-8' src="/static/m/js/jquery-1.11.1.min.js"></script>
<script type="text/javascript" charset='utf-8' src="/static/m/js/batch.js"></script>
<script type="text/javascript" charset='utf-8' src="/static/m/ajax/r_common.js"></script>
<script type="text/javascript" charset='utf-8' src="/static/m/ajax/r_more.js"></script>
</body>
</html>