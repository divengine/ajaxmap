/**
 * Div PHP Ajax Mapping
 * 
 * Mapping PHP data, functions and methods in JavaScript
 *
 * Client side
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License
 * for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program as the file LICENSE.txt; if not, please see
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt.
 *
 * @author Rafa Rodriguez <rafacuba2015@gmail.com>
 * @link http://divengine.com/solutions/div-php-ajax-mapping
 * @version 1.0
 */

var DIV_AJAX_MAPPING_ACCESS_DENIED_HOST = "DIV_AJAX_MAPPING_ACCESS_DENIED_HOST";
var DIV_AJAX_MAPPING_ACCESS_DENIED_USER = "DIV_AJAX_MAPPING_ACCESS_DENIED_USER";
var DIV_AJAX_MAPPING_LOGIN_SUCCESSFUL = "DIV_AJAX_MAPPING_LOGIN_SUCCESSFUL";
var DIV_AJAX_MAPPING_LOGIN_FAILED = "DIV_AJAX_MAPPING_LOGIN_FAILED";
var DIV_AJAX_MAPPING_LOGOUT_SUCCESSFUL = "DIV_AJAX_MAPPING_LOGOUT_SUCCESSFUL";
var DIV_AJAX_MAPPING_METHOD_EXECUTED = "DIV_AJAX_MAPPING_METHOD_EXECUTED";
var DIV_AJAX_MAPPING_METHOD_NOT_EXISTS = "DIV_AJAX_MAPPING_METHOD_NOT_EXISTS";

/**
 * divAjaxMapping
 *
 * This is a static class and contain some methods
 * using by divAjaxMappingClient
 */
var divAjaxMapping = {

    /**
     * Get a valiXMLHttpRequest object
     */
    getXMLHttpRequestObject: function(){
        var result = false;
        try {
            result = new XMLHttpRequest();
        } 
        catch (e) {
            var XmlHttpVersions = ["MSXML2.XMLHTTP.6.0", "MSXML2.XMLHTTP.5.0", "MSXML2.XMLHTTP.4.0", "MSXML2.XMLHTTP.3.0", "MSXML2.XMLHTTP", "Microsoft.XMLHTTP"];
            for (var i = 0; i < XmlHttpVersions.length && !result; i++) {
                try {
                    result = new ActiveXObject(XmlHttpVersions[i]);
                } 
                catch (e) {
                }
            }
        }
        return result;
    },
	
	/**
	 * Send ajax request
	 * @param {Object} params
	 */
	ajax: function(params){

		if (typeof params.url == 'undefined'){
			return null;
		}
		
		var xhr = this.getXMLHttpRequestObject();

		params.data = (typeof params.data == 'undefined')?{}:params.data;
		
		xhr.open("POST", encodeURI(params.url), params.async);
		xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');

		var s = "";
		var k = 0;
		for(var i in params.data){
			if (k++ > 0) s = s + "&";
			s = s + encodeURIComponent(i) + "=" + encodeURIComponent(params.data[i]);
		}
		
		xhr.send(s);
		
		var result = null;
		
		eval("result = " + xhr.responseText);
		
		return result;
	},
    
    /**
     * Call a remote PHP method
     * @param {Object} server
     * @param {Object} method
     * @param {Object} params
     */
    call: function(server, method, params){

        var result = this.ajax({
			url: server + "?execute=" + method,
			data: params
		});
		
        return result;
    },
    
    /**
     * Login on server
     * @param {Object} server
     * @param {Object} username
     * @param {Object} password
     */
    login: function(server, username, password){
        var result = this.ajax({
			 url: server + "?login=" + username + "&password=" + password
		});
		
		if (result == null)
		 	return DIV_AJAX_MAPPING_LOGIN_FAILED;
			
        return result;
    },
    
    /**
     * Logout on server
     * @param {Object} server
     */
    logout: function(server){
		return this.ajax({
			 url: server + "?logout"
		});
    }
};

/**
 * Client instance.
 *
 * @param {Object} params
 * 	 - params.server is a string that contain the server address
 *
 */

/*
 How to use?
 var client new divAjaxMappingClient({server: "http://example.com/server.php"});
 var persons = client.Company.getEmployees();
 var companyPhone = client.Company.phone;
 var enterprise = client.getEnterprise();
 */

var divAjaxMappingClient = function(server){

    // Call to server for retrieving the PHP mapping
	var methods = divAjaxMapping.ajax({
		url: server+"?mapping"
	});
	
    // Add methods to this instance
    for (m in methods) {
        if (methods[m] != "function") 
            methods[m].__server = server;
        eval("this." + m + " = methods." + m + ";");
    }
    
    // Add some necessary properties and methods
    
    /* Server address */
    this.__server = server;
    
    /* Login on server */
    this.__login = function(username, password){
        return divAjaxMapping.login(this.__server, username, password);
    }
    
    /* Logout on server */
    this.__logout = function(){
    	divAjaxMapping.logout(this.__server);
    }
};
