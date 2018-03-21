/**
 * 
 */
jQuery(function(){
	addressinfo();
});
/*
 * 查询地点
 */
function addressinfo(){
	 $.ajax({ 
		 type: "post",
		 url: "do/index.php",
         data:{action:'addressinfo'},
         dataType:"json",
         success: function(result){
        	 var data=eval(result);
        	 var content='';
			 for(var i=0;i<data.row;i++){
				 content+='<p class="paddingl30">'+data['info'][i]['branch_date'].substring(0,10)+'</p><p class="paddingl60">'+data['info'][i]['branch_address']+'<br/><br/></p>';
				
			 }
			 $("#content").html(content);
		 }
	});
}