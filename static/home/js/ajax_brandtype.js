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
             } 
        	 if(response['status'] == request_fall){
        		 alert('加载产品出现异常！');
        	 }
         }
	AjaxRequest(u,d,f);
}
/**
 * 加载品牌列表,型号列表
 */
function GetBrands(id){
	var u='/index.php/nonstandard/option/brand';
	var d='id='+id;
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
                 brand = brand +'<li><span><a href="/index.php/nonstandard/submitorder/more">批量回收</span></li>';
                 $("#brand").html(brand);
                 if(response['data']['brand'] !=0){
                	 $.each(response['data']['type'],function(k,v){
	                	type = type + '<li><a href="/index.php/nonstandard/submitorder/ViewAttr?id='+v['id']+'"><span></span>'+v['name']+'</a></li>';
                	 });
                	 $("#type").html(type);
                	 Number();
                 }
             } 
        	 if(response['status'] == request_fall){
        		 alert('加载产品出现异常！');
        	 }
         }
	AjaxRequest(u,d,f);
}
/**
 * 获取选择的品牌下的型号列表
 */
function model(id) {
	var u='/index.php/nonstandard/option/type';
	var d='id='+id;
	var f=function(res){
        	 var response=eval(res)
        	 var title='';
        	 var brand='';
        	 var type='';
             if(response['status'] == request_succ){
                $.each(response['data'],function(k,v){
	               type = type + '<li><a href="/index.php/nonstandard/submitorder/ViewAttr?id='+v['id']+'"><span></span>'+v['name']+'</a></li>';
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
