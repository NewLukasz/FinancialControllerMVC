window.onload = calendar;

function calendar(){
var date=new Date();
	var day=date.getDate();
	var month=date.getMonth()+1;
	var year=date.getYear()+1900;
	if (day<10){
		day="0"+day;
	}
	if (month<10){
		month="0"+month;
	}
	
	$("#datePicker").attr('value',year+"-"+month+"-"+day);
	$("#datePicker").datepicker({
		dateFormat: "yy-mm-dd"
	});
	
	$("#datePickerSession").datepicker({
		dateFormat: "yy-mm-dd"
	});
	
	$("#datePickerFirstLimit").datepicker({
		dateFormat: "yy-mm-dd"
	});
	
	$("#datePickerSecondLimit").datepicker({
		dateFormat: "yy-mm-dd"
	});
}
		
	