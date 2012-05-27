/**
 * md5.js
 * Copyright (c) 2011, Yoshinori Kohyama (http://algobit.jp/)
 * all rights reserved.
 *
 * Redistribution and use in source and binary forms, with or
 * without modification, are permitted provided that the following
 * conditions are met:
 * 
 * Redistributions of source code must retain the above copyright
 * notice, this list of conditions and the following disclaimer.
 * Redistributions in binary form must reproduce the above
 * copyright notice, this list of conditions and the following
 * disclaimer in the documentation and/or other materials provided
 * with the distribution.
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND
 * CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF
 * USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING
 * IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF
 * THE POSSIBILITY OF SUCH DAMAGE.
 */
MD5=window.MD5||{};MD5.digest=function(i){function f(a,b,c){return a&b|~a&c}function g(a,b,c){return a&c|b&~c}function h(a,b,c){return a^b^c}function j(a,b,c){return b^(a|~c)}function o(a){return[a&255,a>>>8&255,a>>>16&255,a>>>24&255]}function e(a,b,c,d,e,f,g,h){a[0]+=h(b[0],c[0],d[0])+p[e]+g;a[0]=a[0]<<f|a[0]>>>32-f;a[0]+=b[0]}var a,k,l,m,b,c,d,q,r,p,n;a=i.length;i.push(128);b=56-i.length&63;for(k=0;k<b;k++)i.push(0);o(8*a).forEach(function(a){i.push(a)});[0,0,0,0].forEach(function(a){i.push(a)});a=[1732584193];b=[4023233417];c=[2562383102];d=[271733878];for(k=0;k<i.length;k+=64){p=[];for(l=0;64>l;l+=4)m=k+l,p.push(i[m]|i[m+1]<<8|i[m+2]<<16|i[m+3]<<24);l=a[0];m=b[0];q=c[0];r=d[0];e(a,b,c,d,0,7,3614090360,f);e(d,a,b,c,1,12,3905402710,f);e(c,d,a,b,2,17,606105819,f);e(b,c,d,a,3,22,3250441966,f);e(a,b,c,d,4,7,4118548399,f);e(d,a,b,c,5,12,1200080426,f);e(c,d,a,b,6,17,2821735955,f);e(b,c,d,a,7,22,4249261313,f);e(a,b,c,d,8,7,1770035416,f);e(d,a,b,c,9,12,2336552879,f);e(c,d,a,b,10,17,4294925233,f);e(b,c,d,a,11,22,2304563134,f);e(a,b,c,d,12,7,1804603682,f);e(d,a,b,c,13,12,4254626195,f);e(c,d,a,b,14,17,2792965006,f);e(b,c,d,a,15,22,1236535329,f);e(a,b,c,d,1,5,4129170786,g);e(d,a,b,c,6,9,3225465664,g);e(c,d,a,b,11,14,643717713,g);e(b,c,d,a,0,20,3921069994,g);e(a,b,c,d,5,5,3593408605,g);e(d,a,b,c,10,9,38016083,g);e(c,d,a,b,15,14,3634488961,g);e(b,c,d,a,4,20,3889429448,g);e(a,b,c,d,9,5,568446438,g);e(d,a,b,c,14,9,3275163606,g);e(c,d,a,b,3,14,4107603335,g);e(b,c,d,a,8,20,1163531501,g);e(a,b,c,d,13,5,2850285829,g);e(d,a,b,c,2,9,4243563512,g);e(c,d,a,b,7,14,1735328473,g);e(b,c,d,a,12,20,2368359562,g);e(a,b,c,d,5,4,4294588738,h);e(d,a,b,c,8,11,2272392833,h);e(c,d,a,b,11,16,1839030562,h);e(b,c,d,a,14,23,4259657740,h);e(a,b,c,d,1,4,2763975236,h);e(d,a,b,c,4,11,1272893353,h);e(c,d,a,b,7,16,4139469664,h);e(b,c,d,a,10,23,3200236656,h);e(a,b,c,d,13,4,681279174,h);e(d,a,b,c,0,11,3936430074,h);e(c,d,a,b,3,16,3572445317,h);e(b,c,d,a,6,23,76029189,h);e(a,b,c,d,9,4,3654602809,h);e(d,a,b,c,12,11,3873151461,h);e(c,d,a,b,15,16,530742520,h);e(b,c,d,a,2,23,3299628645,h);e(a,b,c,d,0,6,4096336452,j);e(d,a,b,c,7,10,1126891415,j);e(c,d,a,b,14,15,2878612391,j);e(b,c,d,a,5,21,4237533241,j);e(a,b,c,d,12,6,1700485571,j);e(d,a,b,c,3,10,2399980690,j);e(c,d,a,b,10,15,4293915773,j);e(b,c,d,a,1,21,2240044497,j);e(a,b,c,d,8,6,1873313359,j);e(d,a,b,c,15,10,4264355552,j);e(c,d,a,b,6,15,2734768916,j);e(b,c,d,a,13,21,1309151649,j);e(a,b,c,d,4,6,4149444226,j);e(d,a,b,c,11,10,3174756917,j);e(c,d,a,b,2,15,718787259,j);e(b,c,d,a,9,21,3951481745,j);a[0]+=l;b[0]+=m;c[0]+=q;d[0]+=r}n=[];o(a[0]).forEach(function(a){n.push(a)});o(b[0]).forEach(function(a){n.push(a)});o(c[0]).forEach(function(a){n.push(a)});o(d[0]).forEach(function(a){n.push(a)});return n};MD5.digest_s=function(i){var f=[],g,h;for(g=0;g<i.length;g++)f.push(i.charCodeAt(g));f=MD5.digest(f);h="";f.forEach(function(f){for(i=f.toString(16);2>i.length;)i="0"+i;h+=i});return h};
