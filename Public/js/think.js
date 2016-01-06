(function($){

        ThinkPHP.test = function (url){
                var pattern = /(?:\d{4})-(?:\d{2}-(\d{2}))/;
                var str = "2010-01-02 2010-01-02";
                var result = str.match(pattern);
                return result;
        }
        /**
         * 解析URL
         * @param  string url 被解析的URL
         * @return object     解析后的数据
         */
         ThinkPHP.parseURL = function (url){
                var parse = url.match(/^(?:([a-z]+):\/\/)?([\w-]+(?:\.[\w-]+)+)?(?::(\d+))?([\w-\/]+)?(?:\?((?:\w+=[^#&=\/]*)?(?:&\w+=[^#&=\/]*)*))?(?:#([\w-]+))?$/i);
                parse || $.error("url不合法");
               
                return{
                       "scheme" : parse[1],
                       "host"   : parse[2], 
                       "port"   : parse[3],
                       "path"   : parse[4],
                       "query"  : parse[5],
                       "fragment":parse[6]
                };
         }



})(jQuery);