//获取验证码
function getVerify()
{
  var rand = Math.random();
  var url = window.ThinkPHP.APP + '/Home/Users/getVerify/'+rand;
  document.getElementById('captcha').setAttribute('src',url);
}