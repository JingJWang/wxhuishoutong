$(document).ready(function() {
	var id=getUrlParam('id');
	GetType(id);
});
function GetType(id) {
	var u='/index.php/nonstandard/option/product';
	var d='type=1';
	var f=function(res){
        	 var response=eval(res)
        	 var title='';
        	 var content='';
        	 var typeid='';
             if(response['status'] == request_succ){
                 $.each(response['data'],function(k,v){
                	 if(k == 0 ){
                		 typeid=v['id'];
                		 content = content +'<li data-id="'+v['id']+'" class="current"><a  href="javascript:;" onclick="clickType(this),GetBrands('+v['id']+');">'+v['name']+'</a></li>';
                	 }else{
                		 content = content +'<li data-id="'+v['id']+'"><a href="javascript:;" onclick="clickType(this),GetBrands('+v['id']+');">'+v['name']+'</a></li>';
                	 }
                 });
                 var r = /^\+?[1-9][0-9]*$/;　
                 if( r.test(id)){
                	 GetBrands(id);
                 }else{
                	 GetBrands(typeid);
                 }
                 $('.msUl').html(content);
             }else{
        		 alert('加载产品出现异常！');
        	 }
         }
	AjaxRequest(u,d,f);
}
/**
 * 加载品牌列表,型号列表
 */
function GetBrands(id){
	var u='/index.php/repair/repair/brand';
	var d='name='+name;
	var f=function(res){
        	 var response=eval(res)
        	 var title='';
        	 var brand='';
        	 var type='';
             if(response['status'] == request_succ){
                 $.each(response['data']['brand'],function(k,v){
                	 if(k == 0){
                		 brand = brand +'<li class="current" onclick="model('+v['id']+'),brandSign(this);"><span>'+v['name']+'</span></li>'; 
                	 }else{
                		 brand = brand +'<li onclick="model('+v['id']+'),brandSign(this);"><span>'+v['name']+'</span></li>'; 
                	 }
                 });
                 $("#brand").html(brand);
                 if(response['data']['brand'] !=0){
                	 $.each(response['data']['type'],function(k,v){
	                	type = type + '<li><a href="/view/repair/repair.html?name='+escape(v['name'])+'&id='+v['id']+'"><span></span>'+v['name']+'</a></li>';
                	 });
                	 $("#type").html(type);
                	 Number();
                 }
             } 
        	 if(response['status'] == request_fall){
        		 alert('请登录系统后在进行操作');
        		 UrlGoto('../../index.php/nonstandard/system/Login');
        	 }
         }
	AjaxRequest(u,d,f);
}
/**
 * 获取选择的品牌下的型号列表
 */
function model(id) {
	var u='/index.php/repair/repair/type';
	var d='id='+id;
	var f=function(res){
        	 var response=eval(res)
        	 var title='';
        	 var brand='';
        	 var type='';
             if(response['status'] == request_succ){
                $.each(response['data'],function(k,v){
	               type = type + '<li><a href="/view/repair/repair.html?name='+escape(v['name'])+'&id='+v['id']+'"><span></span>'+v['name']+'</a></li>';
                });
                $("#type").html(type);
                Number();
             } 
        	 if(response['status'] == request_fall){
        		 alert('加载产品出现异常！');
        	 }
         }
	AjaxRequest(u,d,f);
}
/**
 * 获取手机故障选项
*/
function getRepair(name,id){
	var cons='';
	var u='/index.php/repair/repair/getRepairList';
	var d='name='+name+'&id='+id;
	var contents='';
	var typetitle='';
	var pname='';
	var f=function(res){
		var response=eval(res);
            if(response['status'] == request_succ){
            	$(".repfont").html(name+'故障选择');
             	var typetitle = '<div class="repList">';
             	$.each(response['data']['model'],function(k,v){
             		typetitle +='<div class="cate_big"><p class="maintain" onclick="clickShow(this);">'+v['mname']+'<a href="javascript:;" class="transfromA"></a></p><ul class="mainList" style="display: none">';
             		$.each(response['data']['con'],function(km,vm){
             			if(vm[0]==v['mid']){
             				$.each(response['data']['small'],function(ks,vs){
                 				if(vm[1]==vs['pid']){
                 					pname=vs['pname'];
                 				}
                 			})
                 			if(vm[3]==1){
             					typetitle += '<li class="repLi"  datali=0 onclick="liststyle(this),repMoney();">'
     								+'<p class="mainP" data-id="'+vm[0]+':'+vm[1]+'" id="'+pname+'">'+pname+'</p>'
     								+'<p class="priceList" id="'+parseInt(vm[2])+'" data="'+parseInt(vm[2])+'">'+parseInt(vm[2]/100)+'元</p>'
     							+'</li>';
             				}
             			}
             		})
             		typetitle += '</ul></div>';
             	})
             	 //添加其他其他异常
             	var qita = '<p class="maintain" onclick="clickShow(this);">其他异常<a href="javascript:;" class="transfromA"></a></p><ul class="mainList" style="display: none"><p class="p_yichang">最终维修价以检测后报价为准</p><textarea id="other" name="other" placeholder="请描述您手机的异常情况,一百字以内" class="input_yichang" maxlength="100" ></textarea></ul>';
                contents += typetitle+ qita +'</div>';
                $(".repairList").html(contents);
                if(response['data']['addr']!=''){
                	$('#name').val(response['data']['addr']['0']['name']);
                	$('#phone').val(response['data']['addr']['0']['phone']);
                	$('#address').val(response['data']['addr']['0']['coun']+response['data']['addr']['0']['pro']
                					+response['data']['addr']['0']['city']+response['data']['addr']['0']['det']);
                }
                $('.cate_big .mainList').each(function(){
                	//if($(this).children())
                	if($(this).children().length==0){
                		$(this).parent().hide();	
                	}
                });
            } 
        	if(response['status'] == request_fall){
        		 alert('加载参数出现异常！');
        	}
         }
	AjaxRequest(u,d,f);
}
//维修价格
function repMoney(){
	$('.allmoney').css('display','block');
	var countmon='';
	var indexMoney =''; 
	var i = 0;
	$('.repLi_hover').each(function() {
		i++;
		indexMoney = $(this).find('.priceList').attr('data')/100;
		indexMoney = Number(indexMoney);
		countmon =Number(countmon)+indexMoney;
	});
	if(i>1){
		var youhui = (i-1)*40
		countmon = countmon - youhui;
		$('.money_youhui').html('（已优惠' + youhui +'元）');
	}else{
		$('.money_youhui').html('');
	}
	if($('.repLi_hover').length == ''){
		$('.allmoney').css('display','none');
	};
	$('.money_money').html('￥' + countmon);
}
//保存下单维修信息
function saveRepair(){
	var first=second='';
	var i=money=bonus=estprice=0; //money 总价 bonus优惠价 estprice 预估价	
	$('.repLi_hover').each(function(){
		i++;
		first+=$(this).children().eq(0).attr("id")+';';
		money+=parseInt($(this).children().eq(1).attr("id"));
		second+=$(this).children().eq(0).attr("data-id")+':'+$(this).children().eq(1).attr("id")+';';
	})
	if(i>1){
		//优惠价
		bonus=parseInt((i-1))*4000;
	}else if(i<=0 && $('#other').val()==''){
		alert("你的手机故障没有选择,请重新选择");
		return false
	}
	//预估价
	estprice=money-bonus;
	var wheight = $(window).height();
	$('.shadow').height(wheight);
	//手机号
	var names = $('#name').val();
	if(names == ""){
        alert("联系人不能为空");
		$('.shadow').css('display','none');
		return false;
	  }
	var phone = $('#phone').val();
	if(phone == ""){
      alert("手机号不能为空");
		$('.shadow').css('display','none');
		return false;
	}else if(!(/^1[34578]\d{9}$/.test(phone))){
      alert("请输入正确的手机号码");
      $('.shadow').css('display','none');
      return false;
	}
	var address = $('#address').val();
	if(address == ""){
      alert("收货地址不能为空");
		$('.shadow').css('display','none');
		return false;
	}
	var other=$('#other').val();
	var u='/index.php/repair/repair/saveRepairs';
	var d='gooodsname='+name+'&id='+id+'&phone='+phone+'&name='+names+'&address='+address
		  +'&content='+first+'&second='+second+'&bonus='+bonus+'&estprice='+estprice+'&other='+other;
	var f=function(res){
      	 var response=eval(res)
      	 var type='';
           if(response['status'] == request_succ){
				$('.success_box').css('display','block');
				$("body").css("overflow", "hidden");
           }
           else{
      		alert('您这一周的下单次数已经用完');
      		UrlGoto('../../view/repair/repairform.html');
      	 }
       }
	AjaxRequest(u,d,f);
}
//订单完成隐藏弹框
function hide(){
	$('.success_box').css('display','none');
	$("body").css("overflow", "auto");
	UrlGoto('/view/repair/repairform.html');
};
//手机维修总列表页面
function selectList(obj,status){
	var u='/index.php/repair/repair/selectList';
	var d='status='+status;
	$(obj).addClass('indent_hover').siblings().removeClass('indent_hover');
	var f=function(res){
        	 var response=eval(res)
        	 var type='';
        	 var status='';
             if(response['status'] == request_succ){
            	 if(response['data']!=''){
            		 $.each(response['data'],function(k,v){
	            		 switch(v['status']){
	             		 	case '0':status='已取消';break;
	             		 	case '1':status='已下单';break;
	             		 	case '2':status='已发货';break;
	             		 	case '3':status='维修中';break;
	             		 	case '4':status='已完成';break;
	             		 }
	            		type += '<div class="list">'
								+'<div class="list_top">'
									+'<span class="list_logo">手机维修</span>'
									+'<span class="detilsp">'+status+'</span>'
								+'</div>'
								+'<div class="list_left">'
									+'<p class="left_name">'+v.goodsname+'</p>'
									+'<p class="left_time">'+userDate(v.jointime)+'</p>'
								+'</div>'
								+'<a  href="../../../view/repair/repdetails.html?id='+v['id']+'" class="btnright">订单详情</a>'
							+'</div>'
                      });
            		 $(".accomplishList").html(type);
            	 }else{
            		 type='<div class="noxinxi">暂无数据</div>';
            		 $(".accomplishList").html(type);
            	 }
             } 
        	 if(response['status'] == request_fall){
        		 alert('参数错误出现异常！,请重新登录后查看');
				 UrlGoto('../../index.php/nonstandard/system/Login');
        	 }
         }
	AjaxRequest(u,d,f);
};
//查看详情页面
function getDetail(id){
	var u='/index.php/repair/repair/selectDetail';
	var d='id='+id;
	var f=function(res){
        	 var response=eval(res);
        	 var type='';
        	 var content='';
        	 var contents='';
             if(response['status'] == request_succ){
            	switch(response['data']['0']['status']){
     		 	case '0':status='已取消';
	     		 	$("#yifahuo").css('display','none');
	 		 		$("#yixiadan").css('display','none');
	 		 		$("#weixiuzhong").css('display','none');
	 		 		$("#yiwancheng").css('display','none');
     		 	break;
     		 	case '1':status='已下单';
     		 		$("#yifahuo").css('display','none');
     		 		$("#yixiadan").css('display','block');
     		 		$("#weixiuzhong").css('display','none');
     		 		$("#yiwancheng").css('display','none');
     		 		break;
     		 	case '2':status='已发货';
	     		 	$("#yifahuo").css('display','block');
	     		 	$("#yifahuo #express").html(response['data']['0']['express']);
	     		 	$("#yifahuo #num").html(response['data']['0']['num']);
	     		 	$("#yifahuo .inpmessage").html(response['data']['0']['com']);
	 		 		$("#yixiadan").css('display','none');
	 		 		$("#weixiuzhong").css('display','none');
	 		 		$("#yiwancheng").css('display','none');
     		 		break;
     		 	case '3':status='维修中';
	     		 	$("#yifahuo").css('display','none');
	 		 		$("#yixiadan").css('display','none');
	 		 		$("#weixiuzhong").css('display','block');
	 		 		$("#weixiuzhong #express").html(response['data']['0']['express']);
	     		 	$("#weixiuzhong #num").html(response['data']['0']['num']);
	 		 		$("#yiwancheng").css('display','none');
	 		 		$(".stas").html("维修价:");
	 		 		if(response['data']['0']['paysta']==1){
	 		 			$('.payBtnNew').css('display','none');
	 		 		}
	 		 		if(response['data']['0']['com']!=''){
	 		 			$("#weixiuzhong .repp").html(response['data']['0']['com']);
	 		 		}
	 		 		break;
     		 	case '4':status='已完成';
	     		 	$("#yifahuo").css('display','none');
	 		 		$("#yixiadan").css('display','none');
	 		 		$("#weixiuzhong").css('display','none');
	 		 		$("#yiwancheng").css('display','block');
	 		 		$("#yiwancheng #express").html(response['data']['0']['express']);
	     		 	$("#yiwancheng #num").html(response['data']['0']['num']);
	     		 	if(response['data']['0']['com']!=''){
	 		 			$("#yiwancheng .inpmessage").html(response['data']['0']['com']);
	 		 		}
	     		 	$(".stas").html("维修价:");
	 		 		break;
         		}
            	if(response['data']['0']['paysta']==1 && response['data']['0']['status']==3){
            		$(".repTop .state").html(status+'(已支付)');
            	}else{
            		$(".repTop .state").html(status);
            	}
                
                if(status=='已取消'){
       			 $('.cancelBtn').html('已取消').css('color','#b5afaf');
       			 $('.cancelBtn').removeAttr("onclick");
       		 	}
                if(status=='已完成'){
          			 $('.cancelBtn').html('已完成').css('color','#b5afaf');
          			 $('.cancelBtn').removeAttr("onclick");
          		 	}
                if(response['data']['0']['other']!=''){
                	$('.leftrep').append('<p class="leftp">其他故障：</p>');
                	$('.rightp').append('<p class="property_other repP">'+response['data']['0']['other']+'</p>');
                }
                $(".repDetail .property_name").html(response['data']['0']['goodsname']);
                $(".repDetail .property_names").html(response['data']['0']['name']);
                $(".repDetail .property_phone").html(response['data']['0']['phone']);
                $(".repDetail .property_adr").html(response['data']['0']['address']);
                $(".repDetail .property_time").html(userDate(response['data']['0']['jointime']));
                if(response['data']['0']['content'].length<=0){
                	$('.leftrep').find('.leftp').eq(1).hide();
                }else{
					$(".repDetail .property_content").html(response['data']['0']['content'].replace(/;/g,";<br/>"));
                	var phei = $('.property_content').height();
                	$('.leftrep').find('.leftp').eq(1).css('height',phei);
                }
                $(".repDetail .property_price").html(parseInt(response['data']['0']['discount']/100)+'元');
                $(".repDetail .property_jiage").html(parseInt(response['data']['0']['money']/100)+'元');
                $(".repDetail .property_money").html(parseInt(response['data']['0']['bonus']/100)+'元&nbsp;(请在您的个人中心预支付查看)');
                if(response['data']['0']['com']!=''){
                	 $("#yiwancheng .repp").html(response['data']['0']['com']);
                }
             } 
        	 if(response['status'] == request_fall){
        		 alert('参数错误出现异常！,请重新登录后查看');
				 UrlGoto('../../index.php/nonstandard/system/Login');
        	 }
         }
	AjaxRequest(u,d,f);
}
//取消订单
function cancle(){
	var money=$(".repDetail .repCont .property_money").html();
	var u='/index.php/repair/repair/cancelOrder';
		var d='id='+id+'&money='+money;
		var f=function(res){
	        	 var response=eval(res)
	        	 var type='';
	             if(response['status'] == request_succ){
	               alert('取消成功');
	               location=location;
				} 
	        	 if(response['status'] == request_fall){
	        		 alert('参数错误出现异常！,请重新登录后查看');
					 UrlGoto('../../index.php/nonstandard/system/Login');
	        	 }
	         }
		AjaxRequest(u,d,f);
}
function blurFun(obj){
	var b = /^[0-9a-zA-Z]*$/g;
	var num = $(obj).val();
	if(b.test(num) == false){
		$(obj).val('');
		return false;
	}
}
//客户填写单号
function saveOdd(){
	var express=$("#express").val();
	var num=$("#num").val();
	if($.trim(express)=='' ){
		alert("请填写快递公司!");
		return false;
	}else if($.trim(num)==''){
		alert("请填写快递单号!");
		return false;
	}
	var u='/index.php/repair/repair/saveOdd';
	var d='id='+id+'&express='+express+'&num='+num;
	var f=function(res){
        	 var response=eval(res)
        	 var type='';
             if(response['status'] == request_succ){
               alert('填写成功');
               location=location;
			}else{
        		 alert('参数错误出现异常！,请重新登录后查看');
				 UrlGoto('../../index.php/nonstandard/system/Login');
        	}
         }
	AjaxRequest(u,d,f);
}
//弹框支付
function shahei(){
	var shahei = $(window).height();
	$('.shadow').height(shahei);
	$('.paybox').css('display','block');
};
function hovermyli(obj){
	if($(obj).hasClass('bg_img')){
		$(obj).attr('class','bg_img_hover');
	}else{
		$(obj).attr('class','bg_img');
	}
	var ua = window.navigator.userAgent.toLowerCase();
	var iswx = (ua.match(/MicroMessenger/i) == 'micromessenger')?1:0;
	if(iswx==1 || iswx==0){
		var u ='/index.php/repair/repair/orderPay';
		var d ='id='+id;
		var f =function(res){
			var response=eval(res);
			if(response['status'] == request_succ){
				$('body').append(response['data']);
				$(".yesbtn").attr('onclick','callpay();');
			}else{
				alert(response['msg']);
			}
		}
		AjaxRequest(u,d,f);
	}else{
		alert("请在微信客户端支付");
		return false;
	}
}
function canbox(){
	$('.paybox').css('display','none');
	/*var u ='/index.php/repair/repair/cancleoOrder';
	var d ='id='+id;
	var f =function(res){
		var response=eval(res);
		if(response['status'] == request_succ){
			$('.paybox').css('display','none');
			alert("您已取消支付订单");
		}else{
			alert(response['msg']);
		}
	}
	AjaxRequest(u,d,f);*/
	
}
function userDate(uDatas){
	  var date = new Date(uDatas);  
	    date.setTime(uDatas * 1000);  
	    var y = date.getFullYear();      
	    var m = date.getMonth() + 1;      
	    m = m < 10 ? ('0' + m) : m;      
	    var d = date.getDate();      
	    d = d < 10 ? ('0' + d) : d;      
	    var h = date.getHours();    
	    h = h < 10 ? ('0' + h) : h;    
	    var minute = date.getMinutes();    
	    var second = date.getSeconds();    
	    minute = minute < 10 ? ('0' + minute) : minute;      
	    second = second < 10 ? ('0' + second) : second;     
	    return y + '-' + m + '-' + d+' '+h+':'+minute+':'+second;      
}