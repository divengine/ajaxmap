// Mapping the server
var server = new divAjaxMapping("server.php");

/**
 * Load a list of products from server and
 * show each product in a UL
 */
function loadProducts() {

    // getting the list from server (a list of objects, see server.php)
    var products = server.getProducts();

    // cleannig the ul
    var l = document.getElementById("products").childNodes.length;

    for (i = 0; i < l; i++) {
        document.getElementById("products").removeChild(
            document.getElementById("products").firstChild);
    }

    // adding the li elements in ul
    for (var i in products) {
        var e = document.createElement('li');
        var t = document.createTextNode(products[i].Name + ' ('
            + products[i].QuantityPerUnit + ') - $'
            + products[i].UnitPrice + ' per unit');
        e.appendChild(t);
        document.getElementById("products").appendChild(e);
    }
}

var divAjaxMappingExamples = {
    server: server,
	
    serverTime: function(){
        var serverTime = this.server.getServerTime();
        $("#block-example-result").append("<p>The server time is " + serverTime + "</p>");
		document.getElementById("block-example-result").scrollTop = 99999;
    },
	
    myIP: function(){
        var myIP = this.server.getClientIP();
		$("#block-example-result").append("<p>Your IP is " + myIP);
		document.getElementById("block-example-result").scrollTop = 99999;
    },
	
	md5: function(){
		var v = $("#edtExampleValue").val();
		var md5 = this.server.Encryption.getMd5(v);
		$("#block-example-result").append("<p>The MD5 of '" + v + "' is <br> " + md5+ "</p>");
		document.getElementById("block-example-result").scrollTop = 99999;
	},
	sha1: function(){
		var v = $("#edtExampleValue").val();
		var sha1 = this.server.Encryption.getSha1(v);
		$("#block-example-result").append("<p>The SHA1 of '" + v + "' is <br> " + sha1+ "</p>");
		document.getElementById("block-example-result").scrollTop = 99999;
	},
	login: function(){
		var u = $("#edtExampleUser").val();
		var p = $("#edtExamplePass").val();
		var r = this.server.__login(u,p);
		
		if (r == DIV_AJAX_MAPPING_LOGIN_FAILED){
			alert("Access denied [" + DIV_AJAX_MAPPING_LOGIN_FAILED + "]");
		} else {
			$("#exampleLoginBox").fadeOut("medium");
			$("#exampleSecurityBox").fadeIn("medium");
		}
		document.getElementById("block-example-result").scrollTop = 99999;
	},
	logout: function(){
		this.server.__logout();
		$("#exampleLoginBox").fadeIn("medium");
		$("#exampleSecurityBox").fadeOut("medium");
	},
	privateData: function(){
		var v = this.server.getPrivateData();
		if (v == DIV_AJAX_MAPPING_ACCESS_DENIED_USER) {
			alert("Access denied [" + DIV_AJAX_MAPPING_ACCESS_DENIED_USER + "]");
		} else {
			$("#block-example-result").append("<p>" + v + "</p>");
		} 
		document.getElementById("block-example-result").scrollTop = 99999;
	}
}
