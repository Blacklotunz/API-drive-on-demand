<?
class Car implements Dao_interface{
  /*
  localhost/conceptAPI/car/      - POST   add()
  localhost/conceptAPI/car/      - PUT    update()
  localhost/conceptAPI/car/      - DELETE remove()
  localhost/conceptAPI/car/$key  - GET get($key)
  */

  public function get($database, $id){
    if(!isset($id)){
      $toRet = $database->get('cars');
    }
    else{
      $toRet = $database->get('cars', $id);
      //"$id not found / not defined on table $table"
    }

    return json_encode($toRet, JSON_FORCE_OBJECT);
  }

  public function add($database, $input){
    try{
      //data error checking
      if(!isset($input['model'], $input['number_of_seats'],
      $input['plate_number'], $input['number_of_doors'],
      $input['engine'], $input['current_location'], $input['fuel_type']) ){

        http_response_code(422);
        return json_encode(array(
          'code' => 422,
          'message' => 'Missing mandatory data'
        ), JSON_FORCE_OBJECT);
      }
      //if passed data are ok then add new car
      $toAdd = array(
        'model' =>  $input['model'],
        'engine' => $input['engine'],
        'current_location' => $input['current_location'],
        'infotainment_system' => @$input['infotainment_system'],
        'interior_design' => @$input['interior_design'],
        'number_of_seats' => $input['number_of_seats'],
        'number_of_doors' => $input['number_of_doors'],
        'fuel_type' => $input['fuel_type'],
        'plate_number' => $input['plate_number']
      );
      $toRet = $database->insert('cars', $toAdd);

    }catch(Error $e){
      http_response_code(500);
      $toRet = $e->getMessage();
    }
    return json_encode($toRet, JSON_FORCE_OBJECT);
  }


  public function delete($database, $input){
    try{
      if(isset($input['carID'])){
        //save to return
        $toRet = $database->delete('cars', $input['carID']);

        //AND remove all external references to keep DB consistency (not very performing using memory db)
        //an alternative could be to make Bean classes that stores reference to all demands that reference a given car
        //...but it goes a little bit over the requests.
        $demands = $database->get('demands');
        foreach ($demands as $demand_id => $demand) {
          if($demand['carID'] == $input['carID']){
            $database->delete('demands', $demand_id);
          }
        }
        /*
        $equipments = $database->get('equipments');
        foreach ($equipments as $equipment_id => $equipment) {
          if($equipment['carID'] == $input['carID']){
            $database->delete('equipments', $equipment_id);
          }
        }
        */
        ////////////////////////////////////////////////////////////////////////
        //commit changes
      }else{
        $toRet = 'carID parameter is required';
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
      if(isset($input['carID'])){
        $toUpdate = $database->get('cars', $input['carID']);
        if(isset($toUpdate)){
          foreach ($input as $key => $value) {
            //change value of given key
            if(key_exists($key, $toUpdate) && $key != 'carID'){
              $toUpdate[$key] = $value;
              $toRet['field_changed']++;
            }
          }
          $res = $database->update('cars', $input['carID'], $toUpdate);
          $toRet['result'] = $res;
        }else{
          http_response_code(404);
          return json_encode(array(
            'code' => 404,
            'message' => 'carID not found'
          ), JSON_FORCE_OBJECT);
        }
      }else{
        http_response_code(422);
        $toRet = json_encode(array(
          'code' => 422,
          'message' => 'Missing carID'
        ), JSON_FORCE_OBJECT);
      }
    }catch(Error $e){
      http_response_code(500);
      $toRet = $e->getMessage();
    }
    return json_encode($toRet, JSON_FORCE_OBJECT);
  }

}
