/**
 * 
 */
var attr='';
var model='';
var info='';
getAttr();
signPackage();
function getAttr(){
	var id=getUrlParam('id');
	var u = '/index.php/nonstandard/submitorder/quotePlan';
	var d = 'id='+id;
	var f=function(res){
			var response=eval(res);
			var radio='';
			var check='';
			attr=response['data']['attr'];
			model=response['data']['model'];
			info=response['data']['info'];
			var num=1;
			if(response['status'] == request_succ){
				$.each(attr,function(k,v){
					if(num == 1){
						var sign='block';
						var method=function (val,key){ return "property_click(this,'"+val+"','"+key+"')"};
					}else{
						var sign='none';
						var method=function (val,key){ return "item_click(this,'"+val+"','"+key+"')";}
					}					
					//多选
					if(model[k]['model'] != 2){						
						/*if(response['data']['attr'].lenght  == num){
							var next='ok';
						}else{
							var next='';
						}*/
						radio = radio + '<dd id="sign_'+k+'"><div class="property_title  pinggu_title_on clearfix">'+
							'<span class="fl">'+model[k]['name']+'</span><p class="conTxt fl TextOverflow"></p>'+
							'<input type="hidden"  id="'+k+'" name="'+k+'" value=""></input>'+
							'<a class="xiugai_btn fr" href="javascript:;" style="display:none" onclick="li_mod(this)">修改</a>'+																					
							'</div><ul class="pinggu_other widthBig" style="display:'+sign+' ;">';
						var temp='';
						/*//是否是bool 选项
						if(model[k]['model'] == 1){
							var isbool=function(sign,type){ return "isbool(this,'"+sign+"',"+type+")";}
							temp = temp +'<li onclick="'+isbool(k,1)+'" name="'+k+'">'+						
							'<span class="property_value"><i>是</i></span><span class="gou"></span></li>'+
							'<li onclick="'+isbool(k,2)+'" name="'+k+'">'+	
							'<span class="property_value"><i>否</i></span><span class="gou"></span></li>';							
						}else {*/
							$.each(v,function(i,n){
								temp = temp +'<li onclick="'+method(k,n)+'" next="'+''+'" name="'+k+'">'+							
								'<span class="property_value"><i>'+info[n]+'</i></span><span class="gou"></span></li>';
							});
						/*}*/
						radio = radio + temp + '<div class="clear">&nbsp;</div></ul></dd>';
						num ++;
					}else{
							check = check +'<div id="step2"><dl><dd id="sign_'+k+'">'+
								 '<div class="property_title pinggu_title_on"><span>'+model[k]['name']+'(选填)</span></div>'+
								 '<input type="hidden"  id="'+k+'" name="'+k+'" value=""></input>'+
								 '<ul class="pinggu_other2 widthBig">';
								 var temp='';
								 $.each(v,function(i,n){
									 temp = temp +'<li onclick="'+method(k,n)+'" name="'+k+'_'+n+'">'+
									 "<span class='property_value'>"+info[n]+"</span> <span class='gou'></span></li>";
								 });
								 check = check+temp+'<div class="clear">&nbsp;</div></ul></dd></dl></div>';
					}
				});
				content ='<div id="step1"><dl>' + radio +'</dl></div>' + check;
				$(".property_list").html('');
				$(".property_list").html(content);
				//检查是否有回调参数
				var select =getUrlParam('select');
				if (select==null||select=='') {
					return ;
				}
				var arrstr = select.split('_');
				for (var i = 0; i < arrstr.length; i++) {
					var atri = arrstr[i].split('-');
					$("#"+atri[0]).val(atri[1]);
				};
				subOrder();
			}
			if(response['status'] == request_fall){
				alert(response['msg']);
			}
		}
	AjaxRequest(u,d,f);
}
function isBoolList(logic) {
	var radio='',check='';
	$.each(attr,function(k,v){
		var sign='none';
		var method=function (val,key){ return "item_click(this,'"+val+"','"+key+"')";}
		if(logic.indexOf(k) >= 0){
		//多选
			if(model[k]['model'] != 2){
				radio = radio + '<dd id="sign_'+k+'"><div class="property_title  pinggu_title_on clearfix">'+
					'<span class="fl">'+model[k]['name']+'</span><p class="conTxt fl TextOverflow"></p>'+
					'<input type="hidden"  id="'+k+'" name="'+k+'" value=""></input>'+
					'<a class="xiugai_btn fr" href="javascript:;" style="display:none" onclick="li_mod(this)">修改</a>'+																					
					'</div><ul class="pinggu_other widthBig" style="display:'+sign+' ;">';
				var temp='';
				//是否是bool 选项
				if(model[k]['model'] == 1){
					var isbool=function(sign,type){ return "isbool(this,'"+sign+"',"+type+")";}
					temp = temp +'<li onclick="'+isbool(k,1)+'" name="'+k+'">'+						
					'<span class="property_value"><i>是</i></span><span class="gou"></span></li>'+
					'<li onclick="'+isbool(k,2)+'" name="'+k+'">'+	
					'<span class="property_value"><i>否</i></span><span class="gou"></span></li>';							
				}else {
					$.each(v,function(i,n){
						temp = temp +'<li onclick="'+method(k,n)+'" name="'+k+'">'+							
						'<span class="property_value"><i>'+info[n]+'</i></span><span class="gou"></span></li>';
					});
				}
				radio = radio + temp + '<div class="clear">&nbsp;</div></ul></dd>';
				
			}else{
				check = check +'<dd id="sign_'+k+'">'+
					 '<div class="property_title pinggu_title_on"><span>'+model[k]['name']+'</span></div>'+
					 '<input type="hidden"  id="'+k+'" name="'+k+'" value=""></input>'+
					 '<ul class="pinggu_other2 widthBig">';
					 var temp='';
					 $.each(v,function(i,n){
						 temp = temp +'<li onclick="'+method(k,n)+'" name="'+k+'_'+n+'">'+
						 "<span class='property_value'>"+info[n]+"</span> <span class='gou'></span></li>";
					 });
					 check = check+temp+'<div class="clear">&nbsp;</div></ul></dd>';
			}
		}
	});
	var response=new Array();
	response['radio']=radio;
	response['check']=check;
	return response;
}
function subOrder(){
	var id=getUrlParam('id');
	var u = '/index.php/nonstandard/submitorder/planOrder';
	var d = $("#attr").serialize()+'&id='+id;
	var t = $("#attr").serialize();
	var reg = new RegExp("=","g");
	t= t.replace(reg,'-');
	var reg = new RegExp("&","g");
	t= t.replace(reg,'_');
	d += '&select='+t;
	var f=function(res){
		var response=eval(res);
		if(response['status'] == request_succ){
			UrlGoto(response['url']);
		}
		if(response['status'] == request_fall){
			alert(response['msg']);
			UrlGoto(response['url']);
		}
	};
	AjaxRequest(u,d,f);
}
function signPackage(){	 
	var url=location.href.split('#')[0];
	var u = '/index.php/nonstandard/system/signPackage';
	var d = 'url='+encodeURIComponent(url);
	var f=function(res){
		var response=eval(res);
		if(response['status'] == request_succ){
			var signPackage='<script type="text/javascript">wx.config({'+
	         'debug: false,'+
	         'appId: "'+response['data']['appId']+'",'+
	         'timestamp:"'+response['data']['timestamp']+'",'+
	         'nonceStr: "'+response['data']['nonceStr']+'",'+
	         'signature:"'+response['data']['signature']+'",'+
	         'jsApiList: [ '+	   
	             '"getLocation", '+
	         ']'+
	       '});</script>';
			$("body").append(signPackage);			
		}
		if(response['status'] == request_fall){
			alert(response['msg']);
		}
	};
	AjaxRequest(u,d,f);	
}
