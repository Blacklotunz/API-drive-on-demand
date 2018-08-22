<?
class User implements Dao_interface{
  /*
  localhost/conceptAPI/user/add.php
  localhost/conceptAPI/user/update.php
  localhost/conceptAPI/user/remove.php
  */

  public function get($database, $id){
    if(!isset($id)){
      $toRet = $database->get('users');
    }
    else{
      $toRet = $database->get('users', $id);
      if(!$toRet){
        $toRet = "Requested user not found";
      }
    }

    return json_encode($toRet, JSON_FORCE_OBJECT);
  }

  public function add($database, $input){
    try{
      //data error checking
      if(empty($input['name']) || empty($input['password']) || empty($input['mail']) ||
          (!empty($input['age']) && !is_integer($input['age'])) ||
          empty($input['actual_location']) ||
          (empty($input['actual_location']) && !is_numeric($input['actual_location']))){
        http_response_code(422);
        return json_encode(array(
          'code' => 422,
          'message' => 'Missing mandatory data or wrong data type'
        ), JSON_FORCE_OBJECT);
      }
      //////////
      //if everithing fine, then add
      $toAdd = array(
        'name' => $input['name'],
        'password' => $input['password'],
        'mail' => $input['mail'],
        'age' => isset($input['age']) ? isset($input['age']) : null,
        'gender' => isset($input['gender']) ? isset($input['gender']): null,
        'phone_number' => isset($input['phone_number']) ? isset($input['phone_number']): null,
        'actual_location' => isset($input['actual_location']) ? isset($input['actual_location']) : null
      );
      $toRet = $database->insert('users', $toAdd);
    }catch(Error $e){
      http_response_code(500);
      $toRet = $e->getMessage();
    }
    return json_encode($toRet, JSON_FORCE_OBJECT);
  }


  public function delete($database, $input){
    try{
      if(isset($input['userID'])){
        //If we would use a relational db, it would remove all external references to keep DB consistency
        //Here, simply, we don't allow to remove a user if a demand for him exists
        $demands = $database->get('demands');
        foreach ($demands as $demand_id => $demand) {
          if($demand['userID'] == $input['userID']){
            http_response_code(500);
            $toRet = json_encode(array(
              'code' => 500,
              'message' => 'Cannot remove this userID'
            ), JSON_FORCE_OBJECT);
            return $toRet;
            //$database->delete('demands', $demand_id);
          }
        }
        //save to return
        $toRet = $database->delete('users', $input['userID']);
      }else{
        http_response_code(422);
        $toRet = json_encode(array(
          'code' => 422,
          'message' => 'Missing userID'
        ), JSON_FORCE_OBJECT);
      }
    }catch(Error $e){
      http_response_code(500);
      $toRet = $e->getMessage();
    }
    return json_encode($toRet, JSON_FORCE_OBJECT);
  }


  public function update($database, $input){
    try{
      //return number of field changed
      $toRet = array('field_changed' => 0);
      if(isset($input['userID'])){
        $toUpdate = $database->get('users', $input['userID']);
        if(isset($toUpdate)){
          foreach ($input as $key => $value) {
            //change value of given key
            if(key_exists($key, $toUpdate) && $key != 'userID'){
              $toUpdate[$key] = $value;
              $toRet['field_changed']++;
            }
          }
          $res = $database->update('users', $input['userID'], $toUpdate);
          $toRet['result'] = $res;
        }else{
          http_response_code(404);
          $toRet = json_encode(array(
            'code' => 404,
            'message' => 'userID not found'
          ), JSON_FORCE_OBJECT);
        }
      }else{
        http_response_code(404);
        $toRet = json_encode(array(
          'code' => 404,
          'message' => 'userID not found'
        ), JSON_FORCE_OBJECT);
      }
    }catch(Error $e){
      http_response_code(500);
      $toRet = $e->getMessage();
    }
    return json_encode($toRet, JSON_FORCE_OBJECT);
  }

}
