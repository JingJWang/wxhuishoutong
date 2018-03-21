function GetType() {
	var u='/index.php/newmobile/newmobile/ShowBigView';
	var d='';
	var f=function(res){
        	 var response=eval(res)
        	 var content='';
             if(response['status'] == request_succ){
                 $.each(response['data'],function(k,v){
                	 if(v['name'] != "贵金属" && v['name']!='步步高' && v['id']!=540){
                		 content = content +'<a href="list.html?bid='+v['id']+'&name='+escape(v['name'])+'" class="brand_logo"><img class="logo" src="http://www.recytl.com'+v['img']+'" /><p class="name">'+v['name']+'</p></a>';
                	 }
                 });
                 $('.brand').html(content);
             }else{
        		 alert('加载产品出现异常！');
        	 }
         }
	AjaxRequest(u,d,f);
};

function getSeach(){
	var u='/index.php/newmobile/newmobile/search';
	var se=$('.inp').val();
	var d='name='+se;
	var f=function(res){
        	 var response=eval(res)
        	 var content='';
             if(response['status'] == request_succ){
            	 $.each(response['data'],function(k,v){
                	 var phonename = v['name'];
                	 if(phonename.length>4){
                    	 if(phonename.indexOf(" ") == -1){
                    		 phonename = v['name'];
                    	 }else{
                    		 phonename = phonename.split(' ');	//先按照空格分割成数组
                    		 var firphonename = phonename.shift();		//删除第一个元素
                    		 phonename = phonename.join(' ');//在拼接成字符串
                    		 phonename = phonename.replace(/\s/g, "");		//去空格
                    	 }
                	 }
                	 if(v['img']==''){
                		 v['img']='/product/mobile/bdefault.png'
                	 }
                	 content = content +'<a href="offer.html?id='+v['id']+'&name='+escape(v['name'])+'" class="moblie"><img class="mobile_img" src="http://www.recytl.com'+v['img'].trim()+'"  /><p class="mob_name" id="'+v['id']+'">'+phonename+'</p></a>';
                	 
                 });
            	 $('.phone').html('');
                 $('.phone').html(content);
             }else{
            	 $('.phone').html('');
        		 alert(response['msg']);
        	 }
         }
	AjaxRequest(u,d,f);
	
}