$(function(){
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
          getVerify();
      }
    }
  });
})

var interval_hd;
var remain_time = 5;
function setRemainTime(){
    if(remain_time > 0) {
      remain_time--;
      $("#error-msg p").text("注册成功，"+remain_time+"s后跳转到登录页面");
    }else{
      window.clearInterval(interval_hd);
      var a = window.ThinkPHP.DOMAIN;
      window.location.href = window.ThinkPHP.DOMAIN+"index.php/Home/Users/login";
    }
}