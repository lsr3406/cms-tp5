/**
 * 讲表单中获取的数据转化成可以直接用于 post 的数据
 * @param  {array()} formId 表单 id
 * @return {[array()]}        关联数组
 */
function getPostDataFromForm(formId) {
	var res = $('#'+formId).serializeArray();	// 得到的 res 是一个对象数组
	postData = {};
	// 使用 js 的 each() 方法整理数据
	$(res).each(function(i){
		postData[this.name] = this.value;
	});
	return postData;
}

/**
 * 点击添加按钮, 不提交任何数据, 调用服务器的添加方法
 * @return void
 */
$('#button-add').click(function(event) {
	window.location.href = SCOPE.add_url;
});

/**
 * 点击提交按钮, 提交表单中的数据, 调用服务器的添加方法
 * @return void
 */
$('#mooncms-button-submit').click(function(event) {
	
	postData = getPostDataFromForm('mooncms-form');

	// 讲获取的数据提交给服务器
	url = SCOPE.save_url;
	jump_url = SCOPE.jump_url;
	$.post(url,postData,function(result){
		if(result.status == 1)
			// dialog.success(result.message, jump_url);
			window.location.href = jump_url;
		else
			dialog.error(result.message);
		
	},'JSON');

});

/**
 * 点击修改按钮, 得到对应数据的 id 并进入相应的方法中
 */
$('.mooncms-table #mooncms-edit').click(function(event) {
	// 修改数据页面, get 传参
	var url = SCOPE.edit_url + '/id/' + $(this).attr('attr-id');
	window.location.href = url;
});

/**
 * 点击删除按钮, 得到相应数据的 id 弹出对话框, 确认删除后提交给服务器, 设置相应记录 status 为 -1
 */
$('.mooncms-table #mooncms-delete').click(function(event) {
	// 删除数据页面, post 传参
	var url = SCOPE.set_status_url;
	var message = $(this).attr('attr-message');
	var data = {};
	data['id'] = $(this).attr('attr-id');
	data['status'] = $(this).attr('attr-status');
/*
	layer.open({
        type : 0,
        title : '是否提交？',
        btn: ['是', '否'],
        icon : 3,
        closeBtn : 2,
        content: "是否确定"+message,
        scrollbar: true,
        yes: function(){
            // 执行相关跳转
            todelete(url, data);
        },
    });*/
    todelete(url, data);
});

/**
 * 点击状态, 得到相应数据的 id 弹出对话框, 确认修改状态后提交给服务器, 设置相应记录 status 为 0
 */
$('.mooncms-table #mooncms-on-off').click(function(event) {
	// 删除数据页面, post 传参
	var url = SCOPE.set_status_url;
	// var message = $(this).attr('attr-message');
	var data = {};
	data['id'] = $(this).attr('attr-id');
	data['status'] = $(this).attr('attr-status');

    todelete(url, data);

});

/**
 * 向服务器发送 post 请求, 删除相应记录
 * @return void
 */
function todelete(url, data) {
	
	$.post(url, data, function(result){
		// result.status ? dialog.success(result.message,'') : dialog.error(result.message);
		if (result.status){
			window.location.href = '';
		}else{
			dialog.error(result.message)
		}
	}, 'JSON');
}

/**
 * 点击更新排序按钮, 读取当前页的数据并提交给服务器
 * @return void
 */
$('#button-listorder').click(function(event) {
	var url = SCOPE.listorder_url;
	var data = $('.mooncms-table #listorderInput');
	postData = {};
	$(data).each(function(index, el) {
		postData[this.name] = this.value - 0;
	});

	$.post(url, postData, function(result){
		// result.status ? dialog.success(result.message,'') : dialog.error(result.message);
		if (result.status){
			window.location.href = '';
		}else{
			dialog.error(result.message)
		}

	}, 'JSON');
});

// 下拉菜单
$('#menuType > li').click(function(event) {
	var url = window.location.href;
	url = url.replace(/\?page=\d*/, '');
	typePara = url.search(/\/type\/\d*/);
	if(typePara >= 0)
		url = url.replace(/\/type\/\d*/, '/type/' + this.value)
	else
		url += '/type/' + this.value;
	window.location.href = url;
});

/**
 * 点击后台搜索按钮, 筛选相关的文章
 */
$("#btn-search").click(function(event) {
	var url = window.location.href;
	var title = $("#search-text").val();
	title = encodeURI(title);

	if(title == ''){
		url = url.replace(/\/title\/[a-zA-Z0-9%]*/, '');
	} else {
		url = url.replace(/\?page=\d*/, '');
		titlePara = url.search(/\/title\/[a-zA-Z0-9%]*/);
		if(titlePara >= 0)
			url = url.replace(/\/title\/[a-zA-Z0-9%]*/, '/title/' + title);
		else
			url += '/title/' + title;
	}

	window.location.href = url;
});

// 下拉菜单2 positionSubmit
$('#positionSubmit > li').click(function(event) {
	push = {};
	postData = {};
	$("input[name='positionCheckbox']:checked").each( function (i) {
		push[i] = $(this).attr('attr-id');
	});

	// postData 中的 id 是推荐位的 id, push 中的数字是 news 的 id
	postData['push'] = push;
	postData['id'] = this.value;

	url = SCOPE.push_url;
	$.post(url,postData, function(result) {
		// result.status ? dialog.success(result.message,'') : dialog.error(result.message);
		if (result.status){
			window.location.href = '';
		}else{
			dialog.error(result.message)
		}

	},'JSON');

});

// 下拉菜单2 positionSelect
$('#positionSelect > li').click(function(event) {
	var url = window.location.href;
	url = url.replace(/\?page=\d*/, '');
	positionPara = url.search(/\/position\/\d*/);
	if(positionPara >= 0)
		url = url.replace(/\/position\/\d*/, '/position/' + this.value)
	else
		url += '/position/' + this.value;
	window.location.href = url;

});


