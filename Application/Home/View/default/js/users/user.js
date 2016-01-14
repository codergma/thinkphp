//获取验证码
function getVerify()
{
  var url = window.ThinkPHP.APP + '/Home/Users/getVerify';
  $('#captcha').attr('src',url);
}