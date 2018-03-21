/**
 * 获得选中的属性
 * @param 	 obj   获取当前选中元素的对象
 * @returns  选中元素的值
 */
function Getattr(obj){
	var o=obj;
	var attrkey=$(o).attr("data-key");
	var attrname=$(o).attr("data-val");
	var attdocument='.'+attrkey+" .tex_on";
	if(attrkey != 'oather'){
		$(attdocument).attr('class','tex');
	}
	if(attrkey == 'oather'){
		if( $(o).attr('class') == "tex_on"){
			$(o).attr('class','tex')
			var c=$("#oather").val();
			var n=c.replace(','+attrname,'');
			if(typeof $(".oather .tex_on").html() == 'undefined'){
				$("#oather").val(' ');
			}else{
				$("#oather").val(n);
			}
			return false;
		}else{
			$(o).attr('class','tex_on')
		}
	}else{
		$(o).attr('class','tex_on')
	}	
    var docid='#'+attrkey;
    var attr = $(docid).val();
    if(typeof attr == 'undefined' || attr == ''){
		var data='<input type="hidden" id="'+attrkey+'" name="'+attrkey+'" value="'+attrname+'">';
		var content=$("#request").html();
		$("#request").html(content+data);
	}else{
		if(attrkey == 'oather'){
			  var val=$('#'+attrkey).val();
			 $('#'+attrkey).val(val+','+attrname);			 
	    }else{
		  $('#'+attrkey).val(attrname);
	    }
	}
};
/**
 * 校验选择的属性信息
 */
function  CheckAttr(){
	var u='/index.php/nonstandard/submitorder/CheckAtrr';
	var d=$("#request").serialize();
	var f=function(res){
		 var data=eval(res);
		 if (data.status == request_succ) {
			 UrlGoto(data.url);
		 }
		 if(data.status != request_succ){
			 alert(data.msg);
		 }
	}
	AjaxRequest(u,d,f);
}
