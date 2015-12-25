//获取验证码
function getVerify()
{
  var url = window.CG.CONTROLLER + '/getVerify';
  $('#captcha').attr('src',url);
}