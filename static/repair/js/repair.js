//点击大项出来小项
function clickShow(obj){
//	var changea = $('.maintain a');
//	if($(obj).next('ul').is(':visible')){
//		$(obj).next('ul').hide().prev().find(changea).attr('class','hoverTrano');
//	}else{
//		$(obj).next('ul').show().prev().find(changea).attr('class','hoverTran');
//	};
//	var oul = $(obj).next('ul');
//	oul.siblings('ul').each(function(){
//		if($(this).is(':visible')){
//			$(this).hide().prev().find(changea).attr('class','hoverTrano');
//		};
//	})
	var changea = $('.maintain a');
	var oul = $(obj).next('ul');
	if($(obj).next('ul').is(':hidden')){
		$(obj).next('ul').show().prev().find(changea).attr('class','hoverTran');
		$(obj).next('ul').find('li').attr('datali','2');
	}
	var oul = $(obj).next('ul');
	oul.siblings('ul').each(function(){
		if($(this).find('li').attr('datali') == 1){
			$(this).css('display','block').prev().find(changea).attr('class','hoverTran');
		}else if($(this).find('li').attr('datali') == 2){
			$(this).css('display','none').prev().find(changea).attr('class','hoverTrano');
		}else{
			$(this).css('display','none');
		}
	})
};
//故障选项加背景图
function liststyle(obj){
	if($(obj).hasClass('repLi_hover')){
		$(obj).attr('class','repLi');
		$(obj).attr('datali','2');
	}else{
		$(obj).attr('class','repLi_hover');
		$(obj).attr('datali','1');
	}
}
//点击查看协议
function agreeFun(obj,num){
	var cont = '';
	if(num == 1){
		cont = '<span>用户维修协议</span>'+'<p>欢迎您选择回收通！ <br/>'
		+'回收通在此特别提醒您（用户）在预约维修服务之前，请认真阅读本《服务协议》（以下简称“协议”），确保您充分理解本协议中各条款。请您审慎阅读并选择接受或不接受本协议。除非您接受本协议所有条款，否则您无权使用本协议所涉服务。您的预约、查询、使用等行为将视为对本协议的接受，并同意接受本协议各项条款的约束。 '
		+'如果您未满18周岁，请在法定监护人的陪同下阅读本协议。 <br/>'
		+'一、【协议的范围】<br/>'
		+'1.1 【协议适用主体范围】<br/>'
		+'本协议是您与回收通之间关于用户使用回收通相关服务所订立的协议。“回收通”是指回收通网站及其相关服务可能存在的运营关联单位。“用户”是指使用回收通相关服务的使用人，在本协议中更多地称为“您”。 <br/>'
		+'1.2 【本许可协议指向内容】<br/> '
		+'本协议项下的许可内容是指回收通向用户提供的包括但不限于移动设备的维修服务（以下简称“服务”）。 <br/>'
		+'1.3 【协议关系及冲突条款】 <br/>'
		+'本协议可由回收通随时更新，更新后的协议条款一旦公布即代替原来的协议条款，恕不再另行通知，用户可在本网站查阅最新版协议条款。在回收通修改协议条款后，如果用户不接受修改后的条款，请立即停止使用回收通提供的服务，用户继续使用回收通提供的服务将被视为接受修改后的协议。 <br/>'
		+'二、【个人隐私信息保护】<br/>'
		+'2.1个人隐私信息是指涉及用户个人身份或个人隐私的信息，比如，用户真实姓名、手机号码、手机设备识别码、详细地址等等。非个人隐私信息是指用户对本服务的操作状态以及使用习惯等明确且客观反映在回收通服务器端的基本记录信息、个人隐私信息范围外的其它普通信息，以及用户同意公开的上述隐私信息。<br/>' 
		+'2.2尊重用户个人隐私信息的私有性是回收通的一贯制度，回收通将采取技术措施和其他必要措施，确保用户个人隐私信息安全，防止在本服务中收集的用户个人隐私信息泄露、毁损或丢失。在发生前述情形或者回收通发现存在发生前述情形的可能时，将及时采取补救措施。<br/> '
		+'2.3 回收通未经用户同意不向任何第三方公开、 透露用户个人隐私信息。但以下特定情形除外：<br/>'
		+'(1) 回收通根据法律法规规定或有权机关的指示提供用户的个人隐私信息；<br/> '
		+'(2) 由于用户将其个人隐私信息告知他人或与他人共享个人隐私信息，由此导致的任何个人信息的泄漏，或其他非因回收通原因导致的个人隐私信息的泄露；<br/>' 
		+'(3) 用户自行向第三方公开其个人隐私信息； <br/>'
		+'(4) 用户与回收通及合作维修单位之间就用户个人隐私信息的使用公开达成约定，回收通因此向合作维修单位公开用户个人隐私信息；<br/>' 
		+'(5) 任何由于黑客攻击、电脑病毒侵入及其他不可抗力事件导致用户个人隐私信息的泄露。<br/> '
		+'2.4 用户同意回收通可在以下事项中使用用户的个人隐私信息： <br/>'
		+'(1) 回收通向用户及时发送重要通知，如个人服务信息、本协议条款的变更； <br/>'
		+'(2) 回收通内部进行审计、数据分析和研究等，以改进回收通的产品、服务和与用户之间的沟通；<br/>' 
		+'(3) 依本协议约定，回收通管理、审查用户信息及进行处理措施； <br/>'
		+'(4) 适用法律法规规定的其他事项。<br/> '
		+'除上述事项外，如未取得用户事先同意，回收通不会将用户个人隐私信息使用于任何其他用途。<br/> '
		+'2.5 为了改善回收通的服务，向用户提供更好的服务体验，回收通或可会自行收集使用或向第三方提供用户的非个人隐私信息。<br/> '
		+'三、风险保障<br/>'
		+'3.1回收通给予客户维修风险保障，预估维修价格，回收通预先打入机器对应型号风险金，给予客户最大维修保障，如在维修过程中出现赔偿事宜，经双方协商风险金可用做赔偿金。<br/>'
		+'四、【关于邮寄过程的权责】<br/>'
		+'<samp style="float:left;font-size:0.5rem; color:red">4.1 请您在寄送待检修设备时选择优质物流商，推荐使用顺丰，并采用到付费的方式邮寄。在邮寄设备时，请自主选择保价邮寄，保价费由您自行选择承担，回收通不会承担您在邮寄过程中因丢失、损坏等因素对设备造成的任何损失及责任。若待检修设备于寄送过程中受损或丢失，回收通会且仅会提供相关资料配合您向物流商进行维权。<br/>'
		+'4.2回收通会优先选择顺丰将检修后的设备邮寄返还与您，（邮寄费/快递报价费用由回收通承担。）（如经过检测无法进行维修的，不认可故障议价的机器，）回收通将机器寄还予您，（邮寄费用由您承担承担）如若此过程中出现意外，造成机器丢失涉及赔偿事宜（需物流工作人员提供相关证明），经双方，以及快递方协商处理。<br/></samp>'
		+'五、【关于数据保护的权责】<br/>'
		+'5.1 回收通有着严格的流程监管制度，工程师不得以维修以外的任何理由转移、查阅、调用客户设备里的任何数据。<br/> '
		+'5.2 请您务必于下单前备份并清除待检修设备的敏感数据。回收通不会承担该设备于维修过程中因数据丢失、泄露等因素而造成的任何损失及责任。<br/> '
		+'六、【关于维修流程的权责】<br/>'
		+'6.1 维修过程中，如因维修失误而造成的其他故障，回收通承诺将免费修复，不额外收取费用；如设备完全损坏，经双方协商回收通赔偿对应机器风险金。<br/>'
		+'6.2 维修过程中，若由于个人原因，工程师和用户之间出现的任何纠纷问题，回收通会积极主动为用户协商解决。<br/> '
		+'6.3 设备经回收通检测后，如果您自愿放弃维修，需要承担回寄给您的快递费和快递物品保价费。回收通机器免费寄存时间为30天，如30天内客户仍未支付相关邮寄费用回收通有权处理机器。 <br/>'
		+'6.4 对于因跌落、磕碰、挤压或进液面积达10%以上而丧失部分或全部功能的设备，回收通仅对您指定需修复的功能负维修责任。对于设备其他已知或未知的故障不承担任何责任。<br/> '
		+'6.5 请您务必于下单时认真填写待检修设备的相关信息。回收通不会承担因您所提交信息的错误或缺漏而导致设备于维修过程中出现任何故障的责任。 <br/>'
		+'6.6 如果检修设备于回收通维修期间受损或丢失，经双方协商回收通赔偿对应机器风险金。<br/>'
		+'6.7 设备维修完成后，如在30天内不在线支付维修费用，经沟通无果回收通视您放弃设备的处置权，回收通将获得设备处理权。<br/> ' 
		+'七、【关于保修服务的权责】<br/>'
		+'7.1保修仅服务于在回收通维修且维修成功的设备<br/> '
		+'7.2 回收通对所有维修故障点实施保修，但以下情况除外： <br/>'
		+'(1)出现维修故障点之外的故障；（非保修范围）<br/>'
		+'(2)人为损坏；（非保修范围）</p>';
		$('.xieyi').html(cont);
	}else{
		cont = '<span>手机维修流程</span>'+'<p>一.填写信息</br>'
		+'1.在维修设备之前，您需要先在 回收通 网站上创建订单，并填写相关的信息，包括维修设备的信息和故障说明，以及您的联系方式和收货地址。<br/>'
		+'2.现在也可以关注回收通微信公众号，通过微信公众号下单，可以随时随地查询到维修进度。<br/>'
		+'二.提交订单 <br/>'
		+'1.填写完订单信息后，请点击“提交订单”按钮。只有提交了的订单，才能够在「我的订单」中找到。<br/>' 
		+'2.成功提交订单后，系统会给出预估报价，然后再寄送您的故障设备。<br/>'
		+'三.寄送设备 <br/>'
		+'1.您需要将您的故障设备寄送给我们，以便我们检测和维修，如无必要，请不要寄送耳机、充电器等配件。<br/>'
		+'2.寄送快递前，请先备份好您的数据并删除敏感数据。回收通在维修过程中不会主动备份您的数据，亦不对数据丢失负责。<br/>'
		+'3.手机等数码产品属于易碎品，请选择服务规范的快递公司，并在寄送快递时做好必要的保护，以免在寄送过程中有所损毁。<br/>'
		+'4.请您在寄送设备之后，保管好您的快递单据，并在订单中填写物流信息，方法是 登录回收通 > 进入「我的订单」 > 打开订单详情页面 > 选择快递公司并填写快递单号。<br/>'
		+'四.确认维修方案 <br/>'
		+'1.回收通在收到您寄送的设备之后，将对其进行检测，并根据检测结果给出相应的维修方案。<br/>'
		+'2.检测完成之后，我们的客服人员会通过电话与您联系，告知是否需要二次报价维修，如果您接受我们的二次报价，可以在「我的订单」中完成二次报价支付。<br/>'
		+'3.如果您不接受二次报价方案，可以告知客服人员，或在「我的订单」中取消订单，订单取消之后，我们的客服人员会尽快将设备寄还给您。 <br/>'
		+'4.如果您有其他维修需求，请在确认方案之时提出，以便于与我们二次报价。<br/>'
		+'五.在线付款 <br/>'
		+'1.在确认接受 回收通 给出的维修报价后，您需要登录个人中心进行在线支付（支持微信支付）<br/>'
		+'2.为了不耽误您的设备的维修，请您在确认报价后尽快完成付款。若您未在1天内付款，我们的订单会自动关闭。维修风险金将做取消处理。<br/>'
		+'六.维修完成回寄设备 <br/>'
		+'1.检测完成后，回收通 会开始按照维修方案维修您的设备。<br/>'
		+'2.维修完成后，我们会将设备通过顺丰速运寄回给您，并在发货时通知您。您可以在「我的订单」中查看设备的物流信息。<samp style="float:left;font-size:0.5rem; color:red">（维修产生的旧配件由回收通环保处理不做2次使用。）</samp><br/>' 
		+'3.请签收确认设备已修好，无其他问题。如有任何问题，请直接联系我们的客服人员。<br/>'
		+'4.如果因为订单中的地址信息不准确而造成快递无法正常派发，用户需自行承担由此带来的损失。<br/>'
		+'</p>';
		$('.xieyi').html(cont);
	}
	//$("body").css("overflow", "hidden");
	$('html').addClass('noscroll');
	var wheight = $(window).height();
	$('.shadow').height(wheight);
	$('.repar').css('display','block');
	$('.alls').css('display','block');
}
//协议弹框关闭
function hideagree(){
	//$("body").css("overflow", "auto");
	$('html').removeClass('noscroll');
	$('.repar').css('display','none');
	$('.alls').css('display','none');
}
//同意协议是否选中
function consethover(obj){
	if($(obj).hasClass('consent_span_hover')){
		$(obj).attr('class','consent_span');
	}else{
		$(obj).attr('class','consent_span_hover');
	}
}
//填写个人信息之前判断是否选择故障信息
function addtype(obj){
	if($('.mainList li').hasClass('repLi_hover') || $('#other').val() != ''){
		return true;
	}else{
		alert("请先填写您的故障信息!");
		$(obj).val('');
		return false;
	}
}
//判断其它选项
/*function testTextarea(obj){
	
}*/
