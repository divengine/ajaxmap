/**
 *
 * Client side
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program as the file LICENSE.txt; if not, please see
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt.
 *
 * @author Rafa Rodriguez <rafageist@hotmail.com>
 * @link https://divengine.org
 * @version 1.2
 */

var DIV_AJAX_MAPPING_ACCESS_DENIED_HOST = "DIV_AJAX_MAPPING_ACCESS_DENIED_HOST";
var DIV_AJAX_MAPPING_ACCESS_DENIED_USER = "DIV_AJAX_MAPPING_ACCESS_DENIED_USER";
var DIV_AJAX_MAPPING_LOGIN_SUCCESSFUL = "DIV_AJAX_MAPPING_LOGIN_SUCCESSFUL";
var DIV_AJAX_MAPPING_LOGIN_FAILED = "DIV_AJAX_MAPPING_LOGIN_FAILED";
var DIV_AJAX_MAPPING_LOGOUT_SUCCESSFUL = "DIV_AJAX_MAPPING_LOGOUT_SUCCESSFUL";
var DIV_AJAX_MAPPING_METHOD_EXECUTED = "DIV_AJAX_MAPPING_METHOD_EXECUTED";
var DIV_AJAX_MAPPING_METHOD_NOT_EXISTS = "DIV_AJAX_MAPPING_METHOD_NOT_EXISTS";

/**
 * Client instance.
 *
 * @param {Object} params - params.server is a string that contain the server address
 *
 */

/*
 * How to use?
 *
 * var client = new ajaxmap({server: "http://example.com/server.php"});
 * var persons = client.Company.getEmployees();
 * var companyPhone = client.Company.phone;
 * var enterprise = client.getEnterprise();
 */

var ajaxmap = function (server) {

    /**
     * Get a valiXMLHttpRequest object
     */
    this.getXMLHttpRequestObject = function () {
        var result = false;
        try {
            result = new XMLHttpRequest();
        } catch (e) {
            var XmlHttpVersions = ["MSXML2.XMLHTTP.6.0", "MSXML2.XMLHTTP.5.0",
                "MSXML2.XMLHTTP.4.0", "MSXML2.XMLHTTP.3.0",
                "MSXML2.XMLHTTP", "Microsoft.XMLHTTP"];
            for (var i = 0; i < XmlHttpVersions.length && !result; i++) {
                try {
                    result = new ActiveXObject(XmlHttpVersions[i]);
                } catch (e) {
                }
            }
        }
        return result;
    };

    /**
     * Send ajax request
     *
     * @param Object params
     */
    this.ajax = function (params) {

        if (typeof params.url == 'undefined') {
            return null;
        }

        var xhr = this.getXMLHttpRequestObject();

        params.data = (typeof params.data == 'undefined') ? {} : params.data;

        xhr.open("POST", encodeURI(params.url), params.async);
        xhr.setRequestHeader('Content-Type',
            'application/x-www-form-urlencoded; charset=UTF-8');

        var s = "";
        var k = 0;
        for (var i in params.data) {
            if (k++ > 0)
                s = s + "&";
            s = s + encodeURIComponent(i) + "="
                + this.serialize(params.data[i]);
        }

        xhr.send(s);

        var result = null;

        eval("result = " + xhr.responseText);

        return result;
    };

    /**
     * PHP Serializer
     *
     * @param mixed_value
     * @returns {*}
     */
    this.serialize = function (mixed_value) {
        var _getType = function (inp) {
            var type = typeof inp, match;
            var key;
            if (type == 'object' && !inp) {
                return 'null';
            }
            if (type == "object") {
                if (!inp.constructor) {
                    return 'object';
                }
                var cons = inp.constructor.toString();
                match = cons.match(/(\w+)\(/);
                if (match) {
                    cons = match[1].toLowerCase();
                }
                var types = ["boolean", "number", "string", "array"];
                for (key in types) {
                    if (cons == types[key]) {
                        type = types[key];
                        break;
                    }
                }
            }
            return type;
        };

        var type = _getType(mixed_value);
        var val, ktype = '';
        switch (type) {
            case"function":
                val = "";
                break;
            case"boolean":
                val = "b:" + (mixed_value ? "1" : "0");
                break;
            case"number":
                val = (Math.round(mixed_value) == mixed_value ? "i" : "d") + ":" + mixed_value;
                break;
            case"string":
                mixed_value = this.utf8_encode(mixed_value);
                val = "s:" + encodeURIComponent(mixed_value).replace(/%../g, 'x').length + ":\"" + mixed_value + "\"";
                break;
            case"array":
            case"object":
                val = "a";
                var count = 0;
                var vals = "";
                var okey;
                var key;
                for (key in mixed_value) {
                    ktype = _getType(mixed_value[key]);
                    if (ktype == "function") {
                        continue;
                    }
                    okey = (key.match(/^[0-9]+$/) ? parseInt(key, 10) : key);
                    vals += this.serialize(okey) +
                        this.serialize(mixed_value[key]);
                    count++;
                }
                val += ":" + count + ":{" + vals + "}";
                break;
            case"undefined":
            default:
                val = "N";
                break;
        }
        if (type != "object" && type != "array") {
            val += ";";
        }
        return val;
    };

    /**
     * UTF8 Encode
     *
     * @param {string} argString
     * @return {string}
     */
    this.utf8_encode = function (argString) {
        var string = (argString + '');
        var utftext = "";
        var start, end;
        var stringl = 0;
        start = end = 0;
        stringl = string.length;
        for (var n = 0; n < stringl; n++) {
            var c1 = string.charCodeAt(n);
            var enc = null;
            if (c1 < 128) {
                end++;
            } else if (c1 > 127 && c1 < 2048) {
                enc = String.fromCharCode((c1 >> 6) | 192) + String.fromCharCode((c1 & 63) | 128);
            } else {
                enc = String.fromCharCode((c1 >> 12) | 224) + String.fromCharCode(((c1 >> 6) & 63) | 128) + String.fromCharCode((c1 & 63) | 128);
            }
            if (enc !== null) {
                if (end > start) {
                    utftext += string.substring(start, end);
                }
                utftext += enc;
                start = end = n + 1;
            }
        }
        if (end > start) {
            utftext += string.substring(start, string.length);
        }
        return utftext;
    }


    /**
     * Call a remote PHP method
     *
     * @param {string} server
     * @param {string} method
     * @param {string} params
     */
    this.call = function (server, method, params) {

        var result = this.ajax({
            url: server + "?execute=" + method,
            data: params
        });

        return result;
    };

    /**
     * Login on server
     *
     * @param {string} server
     * @param {string} username
     * @param {string} password
     */
    this.login = function (server, username, password) {
        var result = this.ajax({
            url: server + "?login=" + username + "&password=" + password
        });

        if (result == null)
            return DIV_AJAX_MAPPING_LOGIN_FAILED;

        return result;
    };

    /**
     * Logout on server
     *
     * @param {string} server
     */
    this.logout = function (server) {
        return this.ajax({
            url: server + "?logout"
        });
    };

    if (typeof server !== 'undefined') {

        // Call to server for retrieving the PHP mapping
        var methods = this.ajax({
            url: server + "?mapping"
        });

        // Add methods to this instance
        for (m in methods) {
            if (methods[m] != "function")
                methods[m].__server = server;
            eval("this." + m + " = methods." + m + ";");
        }
    }
    // Add some necessary properties and methods

    /* Server address */
    this.__server = server;

    /* Login on server */
    this.__login = function (username, password) {
        return this.login(this.__server, username, password);
    };

    /* Logout on server */
    this.__logout = function () {
        this.logout(this.__server);
    };
};