var api={
	'getmenu_L':'/index.php/maijinadmin/menu/getmenu_L',
	'addmenu_L':'/index.php/maijinadmin/menu/addmenu_L'
};
$(document).ready(function(){
	//点击显示二级菜单
	$(".lab-menu").live('click',function(){
		//为此span添加一个class，为了右侧的保存配置
		$('.span-checked').removeClass('span-checked');
		$(this).addClass('span-checked');
		var par=$(this).parent();
		if(par.hasClass('one-menu-li')){
		   par.next().toggle();
		}
		var name=$(this).html();
		if(name=='&nbsp;'){
			name='';
		}
		var link=$(this).next().val();
		$(".menu-info-name").val(name);
		$(".menu-info-link").val(link);
	});
	//添加一级菜单
	$(".add-menu").live('click',function(){
		//判断一级菜单的个数
		var len=$('.one-menu-li').length;
		if(len==3){
			alert('一级菜单最多只能添加3个');
			return false;			
		}else{
			var con=$('<li class="list-group-item one-menu-li"><span class="badge badge-delete">&nbsp;</span><span class="badge badge-add">&nbsp;</span><span class="lab-menu" level="1">&nbsp;</span><input class="link-menu" type="hidden" value=""/></li><div class="two-div"></div>');
			con.appendTo($('.menu-group'));
		}
	});
	//添加二级菜单
	$(".badge-add").live('click',function(){
		//判断此一级菜单下有多少个二级菜单
		var len=$(this).parent().next().find('li').length;
		if(len==5){
			alert('二级菜单最多只能添加5个');
			return false;
		}else{
			var con=$('<li class="list-group-item two-menu-li"><span class="badge badge-delete">&nbsp;</span><span class="lab-menu" level="2">&nbsp;</span><input class="link-menu" type="hidden" value=""/></li>');
			con.appendTo($(this).parent().next());
			$(this).parent().next().show();
		}
	});
	//删除菜单
	$('.badge-delete').live('click',function(){
		var par=$(this).parent();
		if(par.hasClass('one-menu-li')){
		   par.next().remove();
		   par.remove();			
		}else{
		   par.remove();
		}
	});
	//保存配置
	$('.save-edit').live('click',function(){
		var name=$(".menu-info-name").val();
		var link=$(".menu-info-link").val();
		if(name!=""){
			if(!$('.span-checked').parent().hasClass('one-menu-li')){
				if(link!=""){
					$('.span-checked').html(name);
					$('.span-checked').next().val(link);
					alert('保存配置成功');
				}else{
					alert('链接不能为空');
				}
			}else{
				$('.span-checked').html(name);
				$('.span-checked').next().val(link);
			}		
		}else{
			alert('名称不能为空');
		}		
	});
	//发布菜单
	$('.fabu-menu').live('click',function(){
		var err=false;
		var len=$('.one-menu-li').length;
		if(len<2){
			alert('必须要有2-3个菜单');
			return false;			
		}else{
			var data='{"button":[';
			var two_menu='';
			var one_len=$('.one-menu-li').length-1;
			
			$('.one-menu-li').each(function(i){
				var one_span=$(this).find('.lab-menu').html();				
				var one_link=$(this).find('.link-menu').val();
				if(one_link!=""){//如果一级菜单设置了链接，那二级菜单就失效了
					if(i==one_len){
						data+='{"type":"view","name":"'+one_span+'","url":"'+one_link+'"}';
					}else{
						data+='{"type":"view","name":"'+one_span+'","url":"'+one_link+'"},';
					}					
				}else{
					var two_len=$(this).next().find('li').length-1;
					if(two_len==-1){
						alert(one_span+"菜单必须要有链接或者有子菜单");
						err=true;
						return false;
					}
					data+='{"name":"'+one_span+'","sub_button":[';					
					$(this).next().find('li').each(function(j){
						var two_span=$(this).find('.lab-menu').html();				
						var two_link=$(this).find('.link-menu').val();						
						if(two_span=='&nbsp;'){
							alert(one_span+"菜单下面的二级菜单名称需填写完整");
							err=true;
							return false;
						}
						if(j==two_len){
							data+='{"type":"view","name":"'+two_span+'","url":"'+two_link+'"}';
						}else{
							data+='{"type":"view","name":"'+two_span+'","url":"'+two_link+'"},';
						}						
					});
					if(i==one_len){
						data+=']}';
					}else{
						data+=']},';
					}
				}
			});
			data+=']}';
			if(err==false){				
				var dataObj={
						menu:data	
				};
				$.ajax({ 
					 type: "post",
					 url: api.addmenu_L,
					 data:dataObj,
					 dataType:"json",
					 success: function(data){
						 var result=eval(data);
						 alert(result.info);					 
					 }
				});
			}
		}
	});
	
	//获取菜单
	getmenu_list();
});

function getmenu_list(){
	var dataObj={};
	$.ajax({ 
		 type: "post",
		 url: api.getmenu_L,
         data:dataObj,
         dataType:"json",
         success: function(data){
			 var result=eval(data);
			 if(result.status==0){
				var menu_con=result.data.content;
				var obj = eval('('+menu_con+')');
				var one_len=obj.button.length;
				var content='';
				for(var i=0;i<one_len;i++){
					var one_span=obj.button[i].name;
					if (obj.button[i].hasOwnProperty('sub_button') == false){
						var one_link=obj.button[i].url;
						content+='<li class="list-group-item one-menu-li"><span class="badge badge-delete">&nbsp;</span><span class="badge badge-add">&nbsp;</span><span class="lab-menu" level="1">'+one_span+'</span><input class="link-menu" type="hidden" value="'+one_link+'"/></li><div class="two-div"></div>';
					}else{			
						var two_len=obj.button[i].sub_button.length;			
						content+='<li class="list-group-item one-menu-li"><span class="badge badge-delete">&nbsp;</span><span class="badge badge-add">&nbsp;</span><span class="lab-menu" level="1">'+one_span+'</span><input class="link-menu" type="hidden" value=""/></li><div class="two-div">';
						for(var j=0;j<two_len;j++){
							var two_span=obj.button[i].sub_button[j].name;			
							var two_link=obj.button[i].sub_button[j].url;
							content+='<li class="list-group-item two-menu-li"><span class="badge badge-delete">&nbsp;</span><span class="lab-menu" level="2">'+two_span+'</span><input class="link-menu" type="hidden" value="'+two_link+'"/></li>';
						}
						content+='</div>';
					}		
				}
				$(".menu-group").html(content);
			 }else{
				 alert(result.info);
			 }
		 }
	});
}