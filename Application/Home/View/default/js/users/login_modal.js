 $(function(){
  $('#rememberPwd').change(function(){
    if ($('#rememberPwd').val()=='1'){
      $('#rememberPwd').val('0');
    }else{
      $('#rememberPwd').val('1');
    }
  })
  // jquery.form.js插件发送登录请求
  $("#signin").ajaxForm({
    type:'post',
    dataType:'json',
    success:function(result){
      if ( result.status > 0)
        {
          $("#error-msg").hide();
          window.location.reload();
        }
        else
        {
          $("#error-msg p").remove();
          $('#error-msg').append("<p>"+result.msg+"</p>").show();
          getVerify();
        }
    }
  });
 
  //忘记密码
  $('#forget-password').on('click',function(){
    $('#modal-forget-password').modal();
  });
  //发送重置密码邮件
  $('#btn-send-email').on('click',function(){
    var url = "{:U('User/modifyPassword')}";
    var data = {"email":$('#reset-password-email').val()};
    $.ajax({
      url:url,
      type:'post',
      data:data,
      dataType:'json',
      success:function(result){
      }
    });
  });
}) ;

function appendModal(){
	$("body").append(
      '<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">'+
      '<div id="error-msg" class="alert alert-warning alert-dismissible error text-center" style="display:none;">'+
      '<button type="button" class="close" data-dismiss="alert" ><span >&times;</span></button>'+
     '</div>'+
        '<div class="modal-dialog " role="document" style="width:450px;">'+
          '<div class="modal-content">'+
           '<div class="modal-header">'+
            '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
            '<h4 class="modal-title">请先登录</h4>'+
            '</div>'+
            '<div class="modal-body">'+
              '<div style="width:350px;margin:20px auto;">'+
               '<form id="signin" action="'+window.ThinkPHP.APP+'/Home/Users/checkLogin" method="post">'+
                '<div class="form-group">'+
                  '<label class="control-label sr-only" for="singin-user_name">用户名</label>'+
                  '<div class="input-group">'+
                    '<span class="input-group-addon "><span class="glyphicon glyphicon-user"></span></span>'+
                    '<input type="input" class="form-control input-lg" name="loginuser_name"id="loginuser_name"required="required"autofocus="autofocus"  placeholder="用户名" >'+
                  '</div>'+
                '</div>'+
                '<div class="form-group" >'+
                  '<label class="control-lable sr-only" for="signin-password">密码</label>'+
                  '<div class="input-group">'+
                    '<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>'+
                    '<input type="password" class="form-control input-lg" name="loginPassword"id="loginPassword"required="required"autofocus="autofocus"  placeholder="密码" >'+
                  '</div>'+
                '</div>'+
                '<div style="max-width:150px;position:relative;">'+
                    '<input type="text" class="form-control input-lg" name="captcha"id="signin-captcha"required="required"placeholder="验证码" >'+
                    '<div id="signin-cap-container"style="position:absolute; top:3px;left:160px;width:140px;height:40px;">'+
                      '<img id="captcha" style="width:140px;height:40px;" />'+
                    '</div>'+
                '</div>'+
              '<a id="signin-gen-cap"href="javaScript:getVerify();">看不清楚？换一张</a>'+
              '<input type="submit" name="signin" id="signinBtn" class="btn btn-success btn-lg btn-block" style="margin-top:10px;outline:none;" value="登录">'+
                '<div class="checkbox" style="display:inline-block;">'+
                  '<label>'+
                    '<input type="checkbox" checked="checked" name="rememberPwd"id="rememberPwd" value="1" >'+
                    '记住密码'+
                  '</label>'+
                '</div>'+
                '<a id="forget-password"href="javaScript:void(0);">忘记密码?</a>'+
              '</form>'+
            '</div>'+
            '</div>'+
          '</div>'+
        '</div>'+
      '</div> ');
}
//获取验证码
function getVerify()
{
  var rand = Math.random();
  var url = window.ThinkPHP.APP + '/Home/Users/getVerify/'+rand;
  $('#captcha').attr('src',url);
}
// 显示模态窗口
function showLoginModal(){
  //加载模态框
  appendModal();
  getVerify();
  $('#loginModal').modal('show');
}
// 用户退出
function logout(){
  $.ajax({
        url:window.ThinkPHP.APP+'/Home/Users/logout',
        method:'post',
        success:function(){
          window.location.href = window.ThinkPHP.APP+"/Home/Users/login";
        }
      }
    );
}


