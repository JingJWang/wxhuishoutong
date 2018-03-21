var    request_fall  = 3000;
var    request_succ  = 1000;
var UrlGoto=function(url) {
	if(url != ''){
		location.href=url;
	}else{
		return '路径为空!';	
	}
}
var  AjaxRequest=function(u,d,f){
	var result= '';
	$.ajax({
        url:u,
        type:"POST",
        dataType:"json",
        data:d,
        beforeSend: function(){
       	 	
        },
        success:function(res){
        	  f(res);
        },
        complete: function(){
       	},
        error:function(){
          
        }
    });
	
}
function getUrlParam(name) {
	var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
	var r = window.location.search.substr(1).match(reg);  //匹配目标参数
	if (r != null) return unescape(r[2]); return null; //返回参数值
}
$.extend({
	        myTime: {
	            /**
	             * 当前时间戳
	             * @return <int>        unix时间戳(秒)  
	             */
	            CurTime: function(){
	                return Date.parse(new Date())/1000;
	            },
	            /**              
	             * 日期 转换为 Unix时间戳
	             * @param <string> 2014-01-01 20:20:20  日期格式              
	             * @return <int>        unix时间戳(秒)              
	             */
	            DateToUnix: function(string) {
	                var f = string.split(' ', 2);
	                var d = (f[0] ? f[0] : '').split('-', 3);
	                var t = (f[1] ? f[1] : '').split(':', 3);
	                return (new Date(
	                        parseInt(d[0], 10) || null,
	                        (parseInt(d[1], 10) || 1) - 1,
	                        parseInt(d[2], 10) || null,
	                        parseInt(t[0], 10) || null,
	                        parseInt(t[1], 10) || null,
	                        parseInt(t[2], 10) || null
	                        )).getTime() / 1000;
	            },
	            /**              
	             * 时间戳转换日期              
	             * @param <int> unixTime    待时间戳(秒)              
	             * @param <bool> isFull    返回完整时间(Y-m-d 或者 Y-m-d H:i:s)              
	             * @param <int>  timeZone   时区              
	             */
	            UnixToDate: function(unixTime, isFull, timeZone) {
	                if (typeof (timeZone) == 'number')
	                {
	                    unixTime = parseInt(unixTime) + parseInt(timeZone) * 60 * 60;
	                }
	                var time = new Date(unixTime * 1000);
	                var ymdhis = "";
	                ymdhis += time.getUTCFullYear() + "-";
	                ymdhis += (time.getUTCMonth()+1) + "-";
	                ymdhis += time.getUTCDate();
	                if (isFull === true)
	                {
	                    ymdhis += " " + time.getUTCHours() + ":";
	                    ymdhis += time.getUTCMinutes() + ":";
	                    ymdhis += time.getUTCSeconds();
	                }
	                return ymdhis;
	            }
	        }
});
function dropout(){
    var u = '/index.php/center/login/outlogin';
    var d = '';
    var f = function(res){
        response = eval(res);
        location.href=response['url'];
    }
    AjaxRequest(u,d,f);
}


