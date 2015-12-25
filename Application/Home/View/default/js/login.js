
$(function(){
  
  // jquery.form.js插件发送登录请求
  $("#signin").ajaxForm({
    type:'post',
    dataType:'json',
    success:function(result){
      if ( result.status > 0)
        {
          $("#error-msg").hide();
          window.location.href = "{:U('User/index')}";
        }
        else
        {
          $("#error-msg p").remove();
          $('#error-msg').append("<p>"+result.msg+"</p>").show();
          genCaptcha('#signin-cap-container');
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
  
});