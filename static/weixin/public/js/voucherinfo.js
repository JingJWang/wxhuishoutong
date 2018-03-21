/**
 * 
 */
jQuery(function(){
	hellpinfo();
});
/*
 * 查询地点
 */
function hellpinfo(){
	 $.ajax({ 
		 type: "post",
		 url: "do/index.php",
         data:{action:'hellpinfo'},
         dataType:"json",
         success: function(result){
        	 var data=eval(result);
        	 var content='';
			 for(var i=0;i<data.row;i++){
				 content+='<div class="hongbaotit"><p>'+data['info'][i]['instruction_name']+'</p></div><div class="boxf"><div class="boxwrap">'+data['info'][i]['instruction_content']+'</div></div>';
				
			 }
			 $("#content").html(content);
		 }
	});
}