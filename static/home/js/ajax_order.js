/**
 * 
 */
MG();
GetOrdreList(); 
/**
 * 订单列表 获得处在交易过程中的订单
 */
function   GetOrdreList(){
	var u='/index.php/nonstandard/order/GetOrderT';
	var d='';
	$.ajax({
        url:u,
        type:"POST",
        dataType:"json",
        data:d,
        beforeSend: function(){
       	 	$("#turn_gif_box").css('display','block');
        },
        success:function(res){
        	var response =  eval(res);
        	var listj ='<div class="Tit">报价结束</div>';
        	var listq ='<div class="Tit">等待回收商确认</div>';
        	var listt ='<div class="Tit">等待交易</div>';
        	var listw ='<div class="Tit">未完成</div>';
        	var listy ='<div class="Tit">已发送报价中</div>';
        	if(response['status'] == request_succ){
        		$.each(response['data'],function(n,data){
        			switch (n) {
						case 'w':
							if(data == 0){
								listw = '';
							}else{
								$.each(data,function(n,val){
									listw = listw + '<div class="modeBox bb">'+
			                        '<div class="imgBox"><img src="../../../static/home/images/iphone.png" alt=""/></div>'+
			                        '<div class="conBox"><div>'+val['name']+'</div>'+
			                        '<div class="btn btnS1">继续填写</div>'+
			                        '</div><div class="delBox"><span class="btnS2">删除物品</span>'+
			                    	'</div></div>';
								});
							}
							break;
						case 'y':
							if(data == 0){
								listy = '';
							}else{
								$.each(data,function(n,val){
									listy = listy + '<div class="modeBox bb">'+
				                    '<div class="imgBox"><img src="../../../static/home/images/iphone.png" alt=""/></div>'+
				                    '<div class="conBox"><div>'+val['name']+'</div>'+
				                    '<div class="btn btnS1">订单详情</div></div>'+
				                    '<div class="delBox"><span class="btnS1">查看报价</span>'+
				                    '</div></div><div class="time">'+
				                    '<div class="timeL"><img src="../../../static/home/images/ben.png"  alt=""/>报单时间: '+val['time']+'</div>'+
				                    '<div class="timeR">已报<span>'+val['offer']+'</span>人</div></div>';
								});
							}
							break;
						case  'j':
							if(data == 0){
								listj = '';
							}else{
								$.each(data,function(n,val){
									listj = listj + '<div class="modeBox bb">'+
				                    '<div class="imgBox"><img src="../../../static/home/images/iphone.png" alt=""/></div>'+
				                    '<div class="conBox"><div>'+val['name']+'</div>'+
				                    '<div class="btn btnS1">订单详情</div></div>'+
				                    '<div class="delBox"><span class="btnS1">查看报价</span>'+
				                    '</div></div><div class="time">'+
				                    '<div class="timeL"><img src="../../../static/home/images/ben.png"  alt=""/>报单时间: '+val['time']+'</div>'+
				                    '<div class="timeR">已报<span>'+val['offer']+'</span>人</div></div>';
								});
							}
							break;
						case  'q':
							if(data == 0){
								listq = '';
							}else{
								$.each(data,function(n,val){
									listq = listq + '<div class="modeBox bb">'+
				                    '<div class="imgBox"><img src="../../../static/home/images/iphone.png" alt=""/></div>'+
				                    '<div class="conBox2"><div class="phone">'+val['coopmobile']+'</div>'+
				                    '<div style="clear: both;"></div><div class="WP">'+
				                    '<span class="WSF">'+val['coopname']+'</span><span>'+val['coopmobile']+'</span>'+
				                    '</div><div class="btnBox"><span class="btnS1">订单详情</span>'+
				                    '<span class="btnS2">取消交易</span></div></div>'+
				                    '</div><div class="time"><div class="timeL">'+
				                    '<img src="../../../static/home/images/ben.png"  alt=""/>报单时间: '+val['time']+'</div></div>';
								});
							}
							break;
						case  't':
							if(data == 0){
								listt = '';
							}else{
								$.each(data,function(n,val){
									listt = listt + '<div class="modeBox bb">'+
				                    '<div class="imgBox"><img src="../../../static/home/images/iphone.png" alt=""/></div>'+
				                    '<div class="conBox2"><div class="phone">iPhone5S</div>'+
				                    '<div style="clear: both;"></div><div class="WP">'+
				                    '<span class="WSF">王师傅</span><span>13294868475</span>'+
				                    '</div><div class="btnBox"><span class="btnS1">订单详情</span>'+
				                    '<span class="btnS2">取消交易</span></div></div>'+
				                    '</div><div class="time"><div class="timeL">'+
				                    '<img src="../../../static/home/images/ben.png"  alt=""/>报单时间: 2015-8-17 10:34</div></div>';
								});
							}
							break;
					}
        		});
        		$("#listw").html(listw);
        		$("#listy").html(listy);
        		$("#listj").html(listj);
        		$("#listq").html(listq);
        		$("#listt").html(listt);
        	}
        },
        complete: function(){
       	 	$("#turn_gif_box").css('display','none');
       	},
        error:function(msg){
            alert(msg_request_fall+msg);
        }
    });
}
/**
 * 订单列表  获取已经成交的订单
 */

function GetOrdreListT(){
	var u='/index.php/nonstandard/order/GetOrderF';
	var d='';
	$.ajax({
        url:u,
        type:"POST",
        dataType:"json",
        data:d,
        beforeSend: function(){
       	 	$("#turn_gif_box").css('display','block');
        },
        success:function(res){
        	var response = eval(res);
        	if(response['status'] == request_succ){
        		var content='';
        		var evaluation='';
        		if(response['data'] !=0 ){
        			$.each(response['data'],function(n,val){
	        			if(val['evaluation'] = 1){
	        				evaluation = '<span class="btnW btnS4">已评价</span>'
	        			}else{
	        				evaluation ='<span class="btnW btnS3">去评价</span>'
	        			}
	        			content = content + '<div class="modeA bt">'+
	        			'<div class="modeBox bb"><div class="imgBox">'+
	        			'<img src="../../../static/home/images/iphone.png" alt=""/></div>'+
	                    '<div class="conBox2"><div class="phone">'+val['name']+'</div>'+
	                    '<div style="clear: both;"></div><div class="WP">'+
	                    '<span class="WSF">'+val['coopname']+'</span><span>'+val['coopmobile']+'</span>'+
	                    '</div><div style="clear: both;"></div><div class="btnBox">'+val['price']+'</div>'+
	                    '</div></div><div class="time"><div class="timeL">'+
	                    '<img src="../../../static/home/images/time.png"  alt=""/>'+
	                    '成交时间: '+val['dealtime']+'</div><div class="timeR">'+evaluation+
	                    '</div></div></div>';
        			});
        		}else{
        			content='';
        		}
        		$("#content").html(content);
        	}        	
        },
        complete: function(){
       	 	$("#turn_gif_box").css('display','none');
       	},
        error:function(msg){
            alert(msg_request_fall+msg);
        }
    });
        
}
/**
 * 订单列表 获取已取消的订单列表
 */
function GetOrdreListC(){
	var u='/index.php/nonstandard/order/GetOrderC';
	var d='';
	$.ajax({
        url:u,
        type:"POST",
        dataType:"json",
        data:d,
        beforeSend: function(){
       	 	$("#turn_gif_box").css('display','block');
        },
        success:function(res){
        	var response = eval(res);
        	if(response['status'] == request_succ){
        		var content='';
        		var evaluation='';
        		if(response['data'] !=0 ){
        			$.each(response['data'],function(n,val){
        				content = content +'<div class="modeA bt"><div class="modeBox bb">'+
                            '<div class="imgBox"><img src="../../../static/home/images/iphone.png" alt=""/></div>'+
                            '<div class="conBox2"><div class="phone">'+val['name']+'</div>'+
                            '<div style="clear: both;"></div><div class="WP">'+
                            '<span class="WSF">'+val['coopname']+'</span><span>'+val['coopmobile']+'</span>'+
                            '</div><div style="clear: both;"></div><div class="btnBox">'+
                            '取消原因: '+val['remark']+'</div></div></div><div class="time">'+
                            '<div class="timeL"><img src="../../../static/home/images/time.png"  alt=""/>'+
                            '成交时间: '+val['remark']+'</div></div><div class="again bb bt">'+
                            '<span class="again1">再次发送</span><span class="again2">评价</span></div></div>';
        			});
        		}else{
        			content='';
        		}
        		$("#list").html(content);
        	}        	
        },
        complete: function(){
       	 	$("#turn_gif_box").css('display','none');
       	},
        error:function(msg){
            alert(msg_request_fall+msg);
        }
    });
        
}
