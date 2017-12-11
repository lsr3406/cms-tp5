// dialog 类
var dialog = {
	// 错误弹出层
	error: function(message){
		layer.open({
			content: message,
			icon: 2,
			title: '错误提示',
		});
	},

	// 成功弹出层
	success: function(message, url) {
		layer.open({
			content: message,
			icon: 1,
			title: '成功提示',
			yes: function(){
				window.location.href = url;	// 这里的 url 是在当前控制器下追加的
			},
		});
	},

	// 确认弹出层
	confirm: function(message, url) {
		layer.open({
			content: message,
			icon: 3,
			btn: ['是','否'],
			title: '确认提示',
			yes: function(){
				window.location.href = url;	// 这里的 url 是在当前控制器下追加的
			}
		});
	},
	
	// 确认弹出层
	toconfirm: function(message, url) {
		layer.open({
			content: message,
			icon: 3,
			btn: ['是'],
			title: '确认提示',
		});
	}
}