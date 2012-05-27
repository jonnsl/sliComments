(function(o){var q=new Class({options:{},,initialize:function(g,i){this.textarea=o(g);this.counter=o(i);this.textarea.store("Cc",this);this.options.maxLength=g.get("data-maxlength")||500;var f=this.update_counter.bind(this);!Browser.chrome&&!Browser.safari&&this.textarea.addEvents({blur:f,change:f,focus:f,keydown:f,keypress:f,keyup:f,paste:f});this.textarea.addListener("input",f);f()},update_counter:function(){var g=this.options.maxLength-this.textarea.value.length;
this.counter.set("text",g);5>=g?this.counter.setStyle("color","#900"):this.counter.setStyle("color",null);0>g?this.textarea.form.getElement("button").set("disabled",!0):this.textarea.form.getElement("button").set("disabled",!1)}});window.addEvent("domready",function(){var g=o("comments");g.removeClass("no-js");var i=o("comments_counter"),f=o("comments_list"),h=function(a){return function(b){b.stop();(new Request({url:this.get("href"),format:"raw",method:"get",onSuccess:a.bind(this),onFailure:function(a){alert(a.responseText)}})).send()}},
j=h(function(a){var b=this.getParent(".content-container").getElement(".metadata"),b=b.getElement(".rating")||(new Element("span.rating")).inject(b),a=(b.get("text").toInt()||0)+a.toInt();b.set("text",0<a?"+"+a:a).removeClass("(?:positive|negative)").addClass(0<a?"positive":"negative")});g.addEvents({"click:relay(.comment-delete)":h(function(){this.getParent("li.comment").nix(!0);i.set("text",i.get("text").toInt()-1)}),"click:relay(.comment-like)":j,"click:relay(.comment-dislike)":j,"click:relay(.comment-flag)":h(function(a){alert(a)}),
"click:relay(.slicomments-spoiler button)":function(){var a=this.getParent();a.hasClass("spoiler-hide")?(a.removeClass("spoiler-hide"),this.set("text",this.get("data-hide"))):(a.addClass("spoiler-hide"),this.set("text",this.get("data-show")))},"click:relay(.cancel-reply)":function(a){a.stop();this.getParent(".comment").getElement(".comment-reply").getParent().setStyle("display",null);this.getParent("form").destroy()}});var k=g.getElement("form");if(k){h=k.getElement("textarea");new q(h,k.getElement(".chars-count"));
var j=1==k.get("data-logged")?!0:!1,d=document.getElement("html").get("lang"),d=d.split("-");d[1]=d[1].toUpperCase();d=d.join("-");Locale.list().contains(d)&&Locale.use(d);var e=function(a){var b=a.retrieve("validator");b||(b=new Form.Validator.Inline(a,{scrollToErrorsOnSubmit:!1,evaluateFieldsOnChange:!1,evaluateFieldsOnBlur:!1}),a.store("validator",b));return b.validate()},m=h.getDimensions().y;h.addEvents({focus:function(){0==this.value.length&&this.tween("height",m,100)},blur:function(){0==this.value.length&&
this.tween("height",m)}});"placeholder"in document.createElement("input")?h.get("disabled")||$$(".comments_form_inputs li label").setStyle("display","none"):(OverText.implement({attach:function(){var a=this.element,b=this.options,c=b.textOverride||a.get("placeholder")||a.get("alt")||a.get("title");if(!c)return this;c=this.text=(a.getPrevious(b.element)||(new Element(b.element)).inject(a,"after")).addClass(b.labelClass).setStyles({lineHeight:"normal",position:"absolute",cursor:"text"}).set("html",
c).addEvent("click",this.hide.pass("label"==b.element,this));"label"==b.element&&(a.get("id")||a.set("id","input_"+String.uniqueID()),c.set("for",a.get("id")));b.wrap&&(this.textHolder=(new Element("div.overTxtWrapper",{styles:{lineHeight:"normal",position:"relative"}})).grab(c).inject(a,"before"));return this.enable()}}),new OverText(h,{positionOptions:{offset:{x:6,y:6}}}),j||(new OverText(k.name),new OverText(k.email)));g.addEvent("click:relay(.comment-submit)",function(a){a.stop();e(this.form)&&
(new Request.HTML({url:this.form.get("action"),format:"raw",method:"post",data:this.form,onSuccess:function(a){a[0].inject(f,this.form.get("data-position"));i.set("text",i.get("text").toInt()+1);if(this.form.hasClass("reply-form"))this.form.destroy();else{this.form.reset();this.form.text.retrieve("Cc").update_counter();this.form.text.fireEvent("blur")}OverText.update()}.bind(this),onFailure:function(a){alert(a.responseText)}})).send()});g.addEvent("click:relay(.comment-reply)",function(a){a.stop();
a=k.clone();a.addClass("reply-form");a.getElements(".validation-advice").destroy();var b=this.getParent(".comment"),c=b.getElement(".metadata .author").get("text").trim();a.inject(b.getElement(".clr"),"before");b=a.getElement("textarea").addClass("init");b.set("value","@"+c+" ").setCaretPosition("end");new q(b,a.getElement(".chars-count"));this.getParent().setStyle("display","none")});if(!j){var n=k.getElement(".profile-image"),l=n.get("src");~l.indexOf("gravatar.com")&&(l=l.toURI(),o("comments_form_email").addEvent("change",
function(){n.set("src",l.set("file",s.digest_s(this.get("value")))+"")}))}}});
/**
 * md5.js
 * Copyright (c) 2011, Yoshinori Kohyama (http://algobit.jp/)
 * all rights reserved.
 */
var s={digest:function(g){function i(a,b,c){return a&b|~a&c}function f(a,b,c){return a&c|b&~c}function h(a,b,c){return a^b^c}function j(a,b,c){return b^(a|~c)}function k(a){return[a&255,a>>>8&255,a>>>16&255,a>>>24&255]}function d(a,b,c,d,e,f,g,h){a[0]+=h(b[0],c[0],d[0])+r[e]+g;a[0]=a[0]<<f|a[0]>>>32-f;a[0]+=b[0]}var e,m,n,l,a,b,c,o,q,r,p;e=g.length;g.push(128);a=56-g.length&63;for(m=0;m<a;m++)g.push(0);k(8*e).forEach(function(a){g.push(a)});
[0,0,0,0].forEach(function(a){g.push(a)});e=[1732584193];a=[4023233417];b=[2562383102];c=[271733878];for(m=0;m<g.length;m+=64){r=[];for(n=0;64>n;n+=4)l=m+n,r.push(g[l]|g[l+1]<<8|g[l+2]<<16|g[l+3]<<24);n=e[0];l=a[0];o=b[0];q=c[0];d(e,a,b,c,0,7,3614090360,i);d(c,e,a,b,1,12,3905402710,i);d(b,c,e,a,2,17,606105819,i);d(a,b,c,e,3,22,3250441966,i);d(e,a,b,c,4,7,4118548399,i);d(c,e,a,b,5,12,1200080426,i);d(b,c,e,a,6,17,2821735955,i);d(a,b,c,e,7,22,4249261313,i);d(e,a,b,c,8,7,1770035416,i);d(c,e,a,b,9,12,
2336552879,i);d(b,c,e,a,10,17,4294925233,i);d(a,b,c,e,11,22,2304563134,i);d(e,a,b,c,12,7,1804603682,i);d(c,e,a,b,13,12,4254626195,i);d(b,c,e,a,14,17,2792965006,i);d(a,b,c,e,15,22,1236535329,i);d(e,a,b,c,1,5,4129170786,f);d(c,e,a,b,6,9,3225465664,f);d(b,c,e,a,11,14,643717713,f);d(a,b,c,e,0,20,3921069994,f);d(e,a,b,c,5,5,3593408605,f);d(c,e,a,b,10,9,38016083,f);d(b,c,e,a,15,14,3634488961,f);d(a,b,c,e,4,20,3889429448,f);d(e,a,b,c,9,5,568446438,f);d(c,e,a,b,14,9,3275163606,f);d(b,c,e,a,3,14,4107603335,
f);d(a,b,c,e,8,20,1163531501,f);d(e,a,b,c,13,5,2850285829,f);d(c,e,a,b,2,9,4243563512,f);d(b,c,e,a,7,14,1735328473,f);d(a,b,c,e,12,20,2368359562,f);d(e,a,b,c,5,4,4294588738,h);d(c,e,a,b,8,11,2272392833,h);d(b,c,e,a,11,16,1839030562,h);d(a,b,c,e,14,23,4259657740,h);d(e,a,b,c,1,4,2763975236,h);d(c,e,a,b,4,11,1272893353,h);d(b,c,e,a,7,16,4139469664,h);d(a,b,c,e,10,23,3200236656,h);d(e,a,b,c,13,4,681279174,h);d(c,e,a,b,0,11,3936430074,h);d(b,c,e,a,3,16,3572445317,h);d(a,b,c,e,6,23,76029189,h);d(e,a,b,
c,9,4,3654602809,h);d(c,e,a,b,12,11,3873151461,h);d(b,c,e,a,15,16,530742520,h);d(a,b,c,e,2,23,3299628645,h);d(e,a,b,c,0,6,4096336452,j);d(c,e,a,b,7,10,1126891415,j);d(b,c,e,a,14,15,2878612391,j);d(a,b,c,e,5,21,4237533241,j);d(e,a,b,c,12,6,1700485571,j);d(c,e,a,b,3,10,2399980690,j);d(b,c,e,a,10,15,4293915773,j);d(a,b,c,e,1,21,2240044497,j);d(e,a,b,c,8,6,1873313359,j);d(c,e,a,b,15,10,4264355552,j);d(b,c,e,a,6,15,2734768916,j);d(a,b,c,e,13,21,1309151649,j);d(e,a,b,c,4,6,4149444226,j);d(c,e,a,b,11,10,
3174756917,j);d(b,c,e,a,2,15,718787259,j);d(a,b,c,e,9,21,3951481745,j);e[0]+=n;a[0]+=l;b[0]+=o;c[0]+=q}p=[];k(e[0]).forEach(function(a){p.push(a)});k(a[0]).forEach(function(a){p.push(a)});k(b[0]).forEach(function(a){p.push(a)});k(c[0]).forEach(function(a){p.push(a)});return p},digest_s:function(g){var i=[],f,h;for(f=0;f<g.length;f++)i.push(g.charCodeAt(f));i=s.digest(i);h="";i.forEach(function(f){for(g=f.toString(16);2>g.length;)g="0"+g;h+=g});return h}}})(document.id);