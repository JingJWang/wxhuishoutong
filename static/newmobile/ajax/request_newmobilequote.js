var attr='';
var arr=new Array();
var oid=getUrlParam('oid');
function GetTypeQuote(){
	var u='/index.php/newmobile/newmobile/getOneList';
	var d = 'oid='+oid;
	var f=function(res){
			var response=eval(res);
			if(response['status'] == request_succ){
				$(".xinghao").html(response['data'][0]['name']);
				con=response['data'][0]['content'];
				infoid=response['data']['option'];
				arr=con.split(',');
				for(var i=0;i<arr.length;i++){
					if(arr[i].length>0){
						for(var j=0;j<infoid.length;j++){
							if(arr[i]==infoid[j]['id']){
								attr+='<p class="stat_p">'+infoid[j]['info']+'</p>';
							}
						}
						
					}
				}
				$('.money_h1').html('<b class="smallB">¥</b>'+response['data'][0]['price']/100);
				
				$('.status_all').html(attr);
				$('.now').attr('href','finalPrice.html?oid='+oid);
				$('.again').attr('href','javascript:history.go(-1)');
			}
			if(response['status'] == request_fall){
				alert(response['msg']);
			}
		}
	AjaxRequest(u,d,f);
}

function repalc(){
	var u='/index.php/newmobile/newmobile/getOneList';
	var d = 'oid='+oid;
	var f=function(res){
			var response=eval(res);
			if(response['status'] == request_succ){
				$('.price_h1').html('<b class="smallB">¥</b>'+response['data'][0]['price']/100);
			}
			if(response['status'] == request_fall){
				alert(response['msg']);
			}
		}
	AjaxRequest(u,d,f);
}