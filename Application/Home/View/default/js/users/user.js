//获取验证码
function getVerify()
{
  var url = window.ThinkPHP.CONTROLLER + '/getVerify';
  $('#captcha').attr('src',url);
}