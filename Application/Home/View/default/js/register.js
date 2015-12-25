 // jquery.form.js插件发送注册请求
  $('#signup').ajaxForm({
    type:'post',
    dataType:'json',
    success:function(result){
      if ( result.status > 0)
       {
          $("#error-msg p").remove();
          $('#error-msg').append("<p>"+"注册成功，5s后跳转到登录页面"+"</p>").show();
          interval_hd = window.setInterval("setRemainTime()",1000);
       }
      else
      {
          $("#error-msg p").remove();
          $('#error-msg').append("<p>"+result.msg+"</p>").show();
          genCaptcha('#signup-cap-container');
      }
    }
  });