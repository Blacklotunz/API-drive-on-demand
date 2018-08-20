<?php
header('Content-Type: application/json');
require_once 'autoloader.php';
$database = new dbController();

// split the url path by the '/'' into an array in order to have an API sintax like object/action
$request = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

///////INPUT BLOCK//////
//try to read Json
$input = json_decode(file_get_contents('php://input'), true);

if(!$input){
  //fall back -> get parameter passed into request as querystring
  parse_str(file_get_contents('php://input'), $input);
}

$requested_object = isset($request[1]) ? $request[1] : null;
$key = isset($request[2]) ? $request[2] : null;
/////////////////////////


// possible interactive objects
$existing_objects = array(
  'user',
  'demand',
  'car'
);


if (isset($requested_object) && in_array($requested_object, $existing_objects)) {
  // using reflection here to avoid use of switch, which with time can become huuuuge
  $obj = new $requested_object();
  /*
  //by default it will be possible to invoke specific method using url
  if (isset($key) && in_array($key, get_class_methods($requested_object))) {
  // retrieve parametrs toDo
  echo $obj->$key($database, $input);
}
*/

if (isset($_SERVER['REQUEST_METHOD'])) {
  $method = $_SERVER['REQUEST_METHOD'];
  switch ($method) {
    case 'PUT': //used to modify objects
    if (in_array('update', get_class_methods($requested_object))) {
      // retrieve parametrs toDo
      echo $obj->update($database, $input);
    } else {
      echo "requested $method action on $requested_object is not supported yet";
    }
    break;

    case 'DELETE': //delete objects
    if (in_array('delete', get_class_methods($requested_object))) {
      // retrieve parametrs toDo
      echo $obj->delete($database, $input);
    } else {
      echo "requested $method action on $requested_object is not supported yet";
    }
    break;

    case 'POST': //create new objects
    if (in_array('add', get_class_methods($requested_object))) {
      // retrieve parametrs toDo
      echo $obj->add($database, $input);
    } else {
      echo "requested $method action on $requested_object is not supported yet";
    }
    break;

    case 'GET': //read objects
    if (in_array('get', get_class_methods($requested_object))) {
      // retrieve parametrs toDo
      $key = isset($request[2]) ? $request[2] : null;
        echo $obj->get($database, $key);

    } else {
      echo "requested $method action on $requested_object is not supported yet";
    }
    break;

    default:
        echo "requested $method not implemented yet";
    break;
  }
} else {
  echo "missing method into request HEADER";
}
} else {
  echo "requested $requested_object is not supported yet";
}
