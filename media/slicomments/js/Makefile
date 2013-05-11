
.PHONY: js

js: comments_admin.js slicomments.js livecomments.js

comments_admin.js: comments_admin.uncompressed.js chosen.js DynamicTextarea.js meio.autocomplete.js
	wrup --require ./comments_admin.uncompressed.js --source-map ./tmp.map > tmp.js
	uglifyjs tmp.js -d DEV=false --compress --mangle --output ./comments_admin.js --source-map comments_admin.js.map --in-source-map tmp.map
	rm tmp.map
	rm tmp.js

slicomments.js: slicomments.uncompressed.js md5.js charCount.js
	wrup --require ./slicomments.uncompressed.js --source-map ./tmp.map > tmp.js
	uglifyjs tmp.js -d DEV=false --compress --mangle --output ./slicomments.js --source-map slicomments.js.map --in-source-map tmp.map
	rm tmp.map
	rm tmp.js

livecomments.js: tmpl_comment.ejs livecomments.uncompressed.js utils.js filters.js
	ejs-compile --min tmpl_comment.ejs > tmpl_comment.js
	wrup --require ./livecomments.uncompressed.js --source-map ./tmp.map > tmp.js
	uglifyjs tmp.js -d DEV=false --compress --mangle --output ./livecomments.js --source-map livecomments.js.map --in-source-map tmp.map
	rm tmp.map
	rm tmp.js
	rm tmpl_comment.js
