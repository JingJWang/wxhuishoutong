function GetTypeList(bid,name) {
	var u='/index.php/newmobile/newmobile/ShowSamilView';
	var d='bid='+bid;
	var f=function(res){
        	 var response=eval(res)
        	 var content='';
             if(response['status'] == request_succ){
               if(response['data']!=false){
                	 $.each(response['data'],function(k,v){
                    	 var phonename = v['name'];
                    	 console.log(phonename);
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
                    	 content = content 
                    	 			+'<a href="offer.html?id='+v['id']+'&name='+escape(v['name'])+'" class="moblie">'
                    	 			+'<img class="mobile_img" src="http://www.recytl.com'+v['img'].trim()+'"  />'
                    	 			+'<span class="bign_mob"></span>'
                    	 			+'<p class="mob_name" id="'+v['id']+'">'+phonename+'</p></a>';
                    	 
                     });
                     $('.xinghao').html(name);
                     $('.phone').html(content);
                 }else{
                	 alert('暂不支持该品牌回收');
                	 UrlGoto('m_index.html');
                 }
             }else{
        		 alert('加载产品出现异常！');
        	 }
         }
	AjaxRequest(u,d,f);
}