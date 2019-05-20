#!/bin/bash
echo "Go to your web browser and open http://localhost:9090"
rm ./assets/divAjaxMapping.js
cp ../divAjaxMapping.js ./assets/divAjaxMapping.js
php -S localhost:9090

