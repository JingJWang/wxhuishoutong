$(document).ready(function() {
	load(31);
	menu(1,0);
});
//获取类别列表数据
function menu(state,page){
	var content=cont_big='';
	var mid=$('.pname').val();
	var u='/index.php/repair/repairHome/getMenu';
	var d='state='+state+'&page='+page+'&mid='+mid;
	var f=function(res){
		var response = eval(res);
            if(response['status'] == request_succ){
                $.each(response['data']['list'],function(k,v){
                	if(state == 1){
                		content += 
		    					'<li class="cont_ul_li">'
		    						+'<p class="list_tab_p tab_p">'+v.id+'</p>'
			    					+'<p class="list_tab_p tab_p" data="'+v.name+'">'+v.name+'</p>'
			    					+'<p class="list_tab_p tab_p">'+$.myTime.UnixToDate(v.jointime)+'</p>'
			    					+'<p class="list_tab_p tab_p"><span onclick="changeform(this);" class="change_span form_chre_span">修改</span>/<span class="remove_span form_chre_span" onclick="removeform('+v.id+',1,'+page+');">删除</span></p>'
		    						+'<div class="change_box">'
		    							+'<input type="text" id="change" class="change_inp" value="'+v.name+'" onchange="changeBigInp(this);">'
		    							+'<input type="hidden" id="hidbig" name="hidbig">'
		    							+'<span class="change_box_sure form_chre_span" onclick="sureChange('+v.id+',1,'+page+');"></span>'
		    							+'<span class="change_box_cancel form_chre_span" onclick="close_change(this);">x</span>'
		    						+'</div>'
		    					+'</li>';
                		cont_big = '<div class="add_this_p" onclick="showAdd(this,0)">添加故障类型</div>'
		    				+'<form class="form_big" style="display: none">'
		                    	+'<input type="text" class="inp_big_add big_add" placeholder="输入您要添加的故障类型"/>'
		                    	+'<span class="add_save big_btn" onclick="addcont(1);">添加</span>'
		                    	+'<span class="add_cancel big_btn" onclick="cancelcont(this);">取消</span>'
		                    +'</form>'
		    				+'<div class="list_tab">'
		    					+'<p class="list_tab_p tab_p">编号</p>'
		    					+'<p class="list_tab_p tab_p">故障类型</p>'
		    					+'<p class="list_tab_p tab_p">加入时间</p>'
		    					+'<p class="list_tab_p tab_p">操作</p>'
		    				+'</div>'
		    				+'<ul class="cont_ul">'
                	}else if(state == 2){
                		content += 
		    					'<li class="cont_ul_li">'
		    						+'<p class="list_tab_p tab_p">'+v.pid+'</p>'
			    					+'<p class="list_tab_p tab_p" data="'+v.mid+'">'+v.mname+'</p>'
			    					+'<p class="list_tab_pa tab_p" data="'+v.pname+'">'+v.pname+'</p>'
			    					+'<p class="list_tab_p tab_p">'+$.myTime.UnixToDate(v.jointime)+'</p>'
			    					+'<p class="list_tab_p tab_p"><span onclick="changeform(this);" class="change_span form_chre_span">修改</span>/<span class="remove_span form_chre_span" onclick="removeform('+v.pid+',2,'+page+');">删除</span></p>'
		    						+'<div class="change_box_small change_box">'
		    							+'<input type="text" id="change" class="change_inp" value="'+v.pname+'" onchange="changeSmallInp(this);">'
		    							+'<input type="hidden" id="hidsmall" name="hidsmall">'
		    							+'<span class="change_box_sure form_chre_span" onclick="sureChange('+v.pid+',2,'+page+');"></span>'
		    							+'<span class="change_box_cancel form_chre_span" onclick="close_change(this);">x</span>'
		    						+'</div>'
		    					+'</li>';
                		cont_big = '<div class="add_this_p" onclick="showSeAdd('+state+'),showAdd(this,1)">添加故障详情</div>'
                			+'<div class="add_this_p" onclick="showSeAdd('+state+'),showAdd(this,2)">搜索</div>'
		    				+'<form class="form_big" style="display: none">'
		    					+'<select name="pname" class="pname select_select"></select>'
		                    	+'<input type="text" class="inp_small_add big_add" placeholder="输入您要添加的故障类型"/>'
		                    	+'<span class="add_save big_btn" onclick="addcont(2);">添加</span>'
		                    	+'<span class="sousuo_Small big_btn" onclick="menu(2,0);">开始查询</span>'		//查询搜索
		                    	+'<span class="add_cancel big_btn" onclick="cancelcont(this);">取消</span>'
		                    +'</form>'
		    				+'<div class="list_tab">'
		    					+'<p class="list_tab_p tab_p">编号</p>'
		    					+'<p class="list_tab_p tab_p">故障大类型</p>'
		    					+'<p class="list_tab_pa tab_p">故障小类型</p>'
		    					+'<p class="list_tab_p tab_p">加入时间</p>'
		    					+'<p class="list_tab_p tab_p">操作</p>'
		    				+'</div>'
		    				+'<ul class="cont_ul">'
		    				;
                	}
                });
                $('.content').html(cont_big + content + '</ul>');
                //分页
                var one_pag = 10;
     	        var now = Number(response['data']['num']['now']);//当前开始数字
     	        var num = response['data']['num']['0']['sum'];//总数
     	        pagess(one_pag,now,num,state);
             } else{
        		 alert('加载产品出现异常！');
        	 }
         }
	AjaxRequest(u,d,f);
}
//添加故障
function addcont(state){
	var  name=d='';
	if(state == 1){
		name = $('.inp_big_add').val();
		d='state='+state+'&name='+name;
	}else if(state == 2){
		name = $('.inp_small_add').val();
		mid=$('.pname').val();
		d='state='+state+'&name='+name+'&mid='+mid;
	}
	var u='/index.php/repair/repairHome/saveAdd';
	var f=function(res){
    	 var response=eval(res)
         if(response['status'] == request_succ){
           alert('添加成功');
           $('.pname').val('');
           menu(state,0);
           $('.form_big').css('display','none');
		} 
    	 if(response['status'] == request_fall){
    		 alert('参数错误出现异常！,请重新登录后查看');
    	 }
     }
	AjaxRequest(u,d,f);
}
//给修改的input赋值
function changeBigInp(obj){
	$('#hidbig').val($(obj).val());
}
//给修改的小类input赋值
function changeSmallInp(obj){
	$('#hidsmall').val($(obj).val());
}
//修改故障
function sureChange(id,state,page){
	var name=d='';
	if(state == 1){
		name = $('#hidbig').val();
		d='id='+id+'&state='+state+'&name='+name;
	}else if(state == 2){
		name = $('#hidsmall').val();
		d='id='+id+'&state='+state+'&name='+name;
	}
	var u='/index.php/repair/repairHome/saveUpMenu';
	var f=function(res){
        	 var response=eval(res)
             if(response['status'] == request_succ){
               alert('修改成功');
               menu(state,page);
			}else{
        		 alert('参数错误出现异常！,请重新登录后查看');
        	 }
         }
	AjaxRequest(u,d,f);
}
//删除数据
function removeform(id,state,page){
	if(confirm("你确定要删除么")){
		var u='/index.php/repair/repairHome/delMenu';
		var d='id='+id+'&state='+state;
		var f=function(res){
	        var response=eval(res)
	        if(response['status'] == request_succ){
	            alert('删除成功');
	            menu(state,page);
			} 
	        if(response['status'] == request_fall){
	        	alert('参数错误出现异常！');
	        }
	    }
		AjaxRequest(u,d,f);
	}else{
		return false;
	}
}
//添加内容切换
function showSeAdd(state){
	var option='';
	var moption='';
	var u='/index.php/repair/repairHome/getFault';
	var d='state='+state;
	var f=function(res){
        var response=eval(res)
        if(response['status'] == request_succ){
        	$.each(response['data'],function(k,v){
        	 	if(v.id!='' && v.id){
        	 		option+='<option value='+v.id+'>'+v.name+'</option>';
        	 	}
        	})
          	 $('.pname').html(option);
        	$('.three_form').find('select').eq(0).attr('onchange','showTh()');
		}else{
        	alert('参数错误出现异常！');
        }
    }
	AjaxRequest(u,d,f);
}
//显示二类故障菜单内容
function showTh(){
	var val=$(".pname").val();
	var option='';
	var u='/index.php/repair/repairHome/getSeFault';
	var d='id='+val;
	var f=function(res){
        var response=eval(res)
        if(response['status'] == request_succ){
        	 $.each(response['data'],function(k,v){
        	 	if(v.id!='' && v.id){
        	 		option+='<option value='+v.id+'>'+v.name+'</option>';
        	 	}
        	 })
          	 $('.small').html(option);
		}else{
        	alert('参数错误出现异常！');
        }
    }
	AjaxRequest(u,d,f);
}