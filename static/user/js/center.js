function Verifi(){
	var myDate = new Date();
	if((myDate.getHours()>=10 && myDate.getMinutes()>0 ) && (myDate.getHours()<=21 && myDate.getMinutes()<59)){
		return true;
	}else{
		 alert('10:00-22:00期间可以提现');
		 return false;
	}
}