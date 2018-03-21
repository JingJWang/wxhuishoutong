function switchTab(ProTag, ProBox) {
		
		for (i = 1; i < 5; i++) {
			if ("tab" + i == ProTag) {
				document.getElementById(ProTag).getElementsByTagName("a")[0].className = "tl_on";
			} else {
				document.getElementById("tab" + i).getElementsByTagName("a")[0].className = "";
			}
			
			if ("con" + i == ProBox) {
				document.getElementById(ProBox).style.display = "";
			} else {
				document.getElementById("con" + i).style.display = "none";
			}
			
			
		}
	}
	


//弹出层 start
// function uploadPhoto(){
// 	var ouploadBtn = document.getElementById('one1');//点击后出现弹出框
// 	var ofx_pHead = document.getElementById('faqdiv');//内容
// 	var ofx_perDataShdaw = document.getElementById('faqbg');//背景层	
// 	//弹出
// 	ouploadBtn.onclick = function(){
// 		ofx_pHead.style.display = 'block';
// 		ofx_perDataShdaw.style.display = 'block';
// 		$(document.body).css("overflow","hidden");
// 	}	
// 	//关闭弹出框
// 	 ofx_perDataShdaw.onclick = function(){
// 		$(document.body).show();
// 		ofx_pHead.style.display = 'none';
// 		ofx_perDataShdaw.style.display = 'none';
// 		$(document.body).css("overflow","");
// 	}
// }
// uploadPhoto();
//弹出层 end	

