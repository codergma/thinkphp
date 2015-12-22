(function($){
	window.Think = {
        "ROOT"   : "__ROOT__",
        "APP"    : "__APP__",
        "PUBLIC" : "__PUBLIC__",
        "DEEP"   : "{:C('URL_PATHINFO_DEPR')}",
        "MODEL"  : ["{:C('URL_MODEL')}", "{:C('URL_CASE_INSENSITIVE')}", "{:C('URL_HTML_SUFFIX')}"],
        "VAR"    : ["{:C('VAR_MODULE')}", "{:C('VAR_CONTROLLER')}", "{:C('VAR_ACTION')}"],
        "CITYID" : "{$currArea['areaId']}",
        "MALL_TITLE" : "{$CONF['mallName']}",
        "SMS_VERFY"  : "{$CONF['smsVerfy']}"
	}

})(jQuery);