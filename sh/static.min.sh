#!/bin/sh

YUI_COMPRESSOR="java -jar yuicompressor.jar";
YUI_JS_PARAMS="--type js --charset utf-8 -v";
YUI_CSS_PARAMS="--type css --charset utf-8 -v";

JS_RAW="/www/exifer/www/s/js_raw";
JS_MIN="/www/exifer/www/s/js_min";

CSS_RAW="/www/exifer/www/s/css_raw";
CSS_MIN="/www/exifer/www/s/css_min";

# JS
$YUI_COMPRESSOR $JS_RAW/main.js $YUI_JS_PARAMS -o $JS_MIN/main.js
$YUI_COMPRESSOR $JS_RAW/paginator.js $YUI_JS_PARAMS -o $JS_MIN/paginator.js
$YUI_COMPRESSOR $JS_RAW/photo.js $YUI_JS_PARAMS -o $JS_MIN/photo.js
$YUI_COMPRESSOR $JS_RAW/unitpngfix.js $YUI_JS_PARAMS -o $JS_MIN/unitpngfix.js

# CSS
$YUI_COMPRESSOR $CSS_RAW/paginator.css $YUI_CSS_PARAMS -o $CSS_MIN/paginator.css

cat $CSS_RAW/reset.css $CSS_RAW/layout.css $CSS_RAW/fonts.css $CSS_RAW/forms.css $CSS_RAW/main.css > $CSS_MIN/style.css
$YUI_COMPRESSOR $CSS_MIN/style.css $YUI_CSS_PARAMS -o $CSS_MIN/style.css

exit 0;
