var login = {

	check: function(){

		var username = $("#username").val();
		var password = $("#password").val();

		if (!username || !password){
			dialog.toconfirm("用户名或密码不能为空");
			return;
		}

		var url = "/admin/Login/check";
		var data = { 'username': username, 'password': password};

		// 执行异步请求
		$.post(url, data, function(result) {	// 这里的 result 是访问第一个参数 url 后得到的结果
			if(!result.status)
				return dialog.error(result.message);
			// return dialog.success(result.message,'/admin/Index/index');
			window.location.href='/admin/Index/index';
		},"JSON");
	}
}

// 绑定登录按钮的点击事件
$("#submit").click(function(){
	login.check();
});
