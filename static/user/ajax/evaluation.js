//获取我收到的评价列表
function GetMyEvaluation(option) {
	var u='/index.php/nonstandard/wxuser/GetEveluation';
	var d='type='+option;
	var f=function(res){
		response=eval(res);
		if(response['status'] == request_succ){
			var content='';
			var img='';
			$.each(response['data'],function(n,v){
				img='';
				if(v['img'] != ''){
					img = v['img'];
				}else{
					img = '../../../static/user/images/default_pj.jpg';
				}
				content = content + '<div class="renSay oh"><div class="sayImg fl">'+
	            '<img src="'+img+'" alt=""/>'+
	            '</div><div class="txtBox fl"><p class="sayNT oh">'+
	            '<span class="saYName fl">'+v['name']+'</span><span class="saYTime fr">'+v['time']+'</span>'+
	            '</p><p class="sayTxt">'+v['content']+'</br>'+v['remark']+'</p>'+
	            '</div></div>';
			})
			if(option == 'g'){
				$("#list_g").html(content);
			}else{
				$("#list_s").html(content);
			}
		}
		if(response['static'] == request_fall){
			
		}
	}
	AjaxRequest(u,d,f);
}
GetMyEvaluation('g');