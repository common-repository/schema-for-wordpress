(function() {
    tinymce.create('tinymce.plugins.sfwpplugin', {
        init : function(ed, url) {
            var t = this;
            ed.addCommand('sfwp_do', function() {
                    ed.windowManager.open({
                    file:url+"/getsfwp.php?a=1",
                    width:400+parseInt(ed.getLang("media.delta_width",0)),
                    height:350+parseInt(ed.getLang("media.delta_height",0)),
                    inline:1
                },{
                    plugin_url:url
                });

			});

            ed.addButton('sfwp', {
                title : 'Insert Schema for WordPress',
                image :url+'/sfwp-logo.gif',
                onclick : function() {                   
               ed.execCommand("sfwp_do");
                }
            });

            ed.onBeforeSetContent.add(function(ed, o) {
				o.content = t._process_sfwp(o.content,url);
			});

           ed.onPostProcess.add(function(ed, o) {
				if (o.get)
					o.content = t._get_sfwp(o.content);
			});
        },

        _process_sfwp : function(co,url) {
			return co.replace(/\[sfwp([^\]]*)\]/g, function(a,b){
                                    var im= b.replace(/id=([0-9]*) img=/,"");
                                    var img="sfwp.png";if(im.length) img=im.replace(/^\s+/,"");//ltrim - a white space creeps out in the left; maybe regexe's fault ?
				return '<img src="'+url+'/../i/'+img+'" class="sfwpitem mceItem" title="sfwp'+tinymce.DOM.encode(b)+'" />';
			});
		},
	_get_sfwp : function(co) {
			function getAttr(s, n) {
				n = new RegExp(n + '=\"([^\"]+)\"', 'g').exec(s);
				return n ? tinymce.DOM.decode(n[1]) : '';
			};
			return co.replace(/(<img[^>]+>)/g, function(a,im) {
				var cls = getAttr(im, 'class');

				if ( cls.indexOf('sfwpitem') != -1 )
					return '['+tinymce.trim(getAttr(im, 'title'))+']';

				return a;
			});
		},
        createControl : function(n, cm) {
            return null;
        },
        getInfo : function() {
            return {
                longname : "Schema for WordPress",
                author : 'Ian Walters',
                authorurl : 'http://wordpress-plugins.org',
                infourl : 'http://schemaforwordpress.com',
                version : "0.0.0.1"
            };
        }
    });
    tinymce.PluginManager.add('sfwpplugin', tinymce.plugins.sfwpplugin);
})();