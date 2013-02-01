// JavaScript Document
/**
var leftTime = 89132;
window.onload = function(){
	leftTimeCounter();
}
*/
function leftTimeCounter() {
			var _timer = null; 
			if (leftTime > 0) {
				var sec = leftTime % 60;
				var minutes = Math.floor(leftTime / 60);
				var min = minutes % 60;
				var hours = Math.floor(minutes / 60);
				var h = hours % 24;
				var day = Math.floor(hours / 24);
				var html = '剩';
				if(day > 0){
					html += '<span class="item"><span class="day">'+day+'</span>天</span>';
				}
				html += '<span class="item"><span class="hour_num">';
				if(h < 10){
					html += '0';
				}
				html += h+'</span>小时</span><span class="item"><span class="minute_num">';
				if(min < 10){
					html += '0';
				}
				html += min+'</span>分</span>';
				if(day <= 0){
					html += '<span class="item"><span class="second_num">';
					if(sec < 10){
						html += '0';
					}
					html += sec+'</span>秒</span>';
				}
				document.getElementById('v:timeCounter').innerHTML = html;
				leftTime = leftTime - 1;
				_timer = setTimeout(leftTimeCounter, 1000);
			}else{
				document.getElementById('v:timeCounter').innerHTML = "已结束";
				if(_timer != null){
					clearTimeout(_timer);
				}
			}
}