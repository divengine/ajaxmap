# Div PHP Ajax Mapping

Mapping PHP data, functions and methods in JavaScript

An open source library for JavaScript and PHP, that 
allow mapping the PHP functions, static methods of classes and 
arbitrary data when instance a JavaScript class. 

With this class you can call a functions and methods via AJAX. 

For example:

--------------------------------------------
Server side:
--------------------------------------------
```php
<?php

// Include the library 

include "divAjaxMapping.php"; 

// The program 
// Note: the programa can be located in an external file 

function sum($x, $y){
  return $x + $y; 
}

class Enterprise{
  public static function getEmployees(){
      return [
        ["name" => "Thomas Hardy", "salary" => 1500],  
        ["name" => "Christina Berglund", "salary" => 1200] 
      ];  
    } 
}

// Server instance ...

$server = new divAjaxMapping(); 

// ... Add methods ...

$server->addMethod("sum"); 
$server->addClass("Enterprise"); 

// ... and go!
$server->go(); 

```

--------------------------------------------
Client side:
--------------------------------------------
```xhtml
<script type = "text/javascript" src="divAjaxMapping.js"></script>
<script type = "text/javascript">
    var map = new divAjaxMapping("server.php");
    var sum = server.sum(20, 10);
    var employees = map.Enterprise.getEmployees();
    var firstEmployeeName = employees[0]['name'];
</script>
```
