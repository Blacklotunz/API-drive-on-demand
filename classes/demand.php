<?
class Demand implements Dao_interface{
  /*
  localhost/conceptAPI/demand/      - POST   add()
  localhost/conceptAPI/demand/      - PUT    update()
  localhost/conceptAPI/demand/      - DELETE remove()
  localhost/conceptAPI/demand/$key  - GET get($key)
  */

   public function get($database, $id){
     if(!isset($id)){
       $toRet = $database->get('demands');
     }
     else{
       $toRet = $database->get('demands', $id);
       if(!$toRet){
         $toRet = "Requested demand not found";
       }
     }

     return json_encode($toRet);
   }

   public function add($database, $input){
     try{
       //data error checking
       if(!isset($input['userID'], $input['end_time'], $input['start_time'],
                 $input['pick_up_location'], $input['drop_off_location'])){
          http_response_code(422);
          return json_encode(array(
            'code' => 422,
            'message' => 'Missing mandatory data'
          ));
       }else{
         //toDO: check if passed userID exist into db
         if( null == $database->get('users', $input['userID'])){
           http_response_code(422);
           return json_encode(array(
             'code' => 422,
             'message' => 'Key constraints violated'
           ));
         }
       }
       //////////
       //if everything fine, then add
       $toAdd = array(
         'userID' =>  $input['userID'],
         'carID' => isset($input['carID']) ? $input['carID'] : null,
         'desired_features' => isset($input['desired_features']) ? $input['desired_features'] : null,
         'start_time' => $input['start_time'],
         'end_time' => $input['end_time'],
         'pick_up_location' => $input['pick_up_location'],
         'drop_off_location' => $input['drop_off_location']
       );
       $toRet = $database->insert('demands', $toAdd);
     }catch(Error $e){
       http_response_code(500);
       $toRet = $e->getMessage();
     }
     return json_encode($toRet);
   }


   public function delete($database, $input){
     try{
       if(isset($input['demandID'])){
         //save to return
         $toRet = $database->delete('demands', $input['demandID']);
       }else{
         $toRet = 'demandID parameter is required';
       }
     }catch(Error $e){
       http_response_code(500);
       $toRet = $e->getMessage();
     }
     return json_encode($toRet);
   }


   public function update($database, $input){
     try{
       //return number of field changed
       $toRet = array('field_changed' => 0);
       if(isset($input['demandID'])){
         $toUpdate = $database->get('demands', $input['demandID']);
         if(isset($toUpdate)){
           foreach ($input as $key => $value) {
             //change value of given key
             if(key_exists($key, $toUpdate) && $key != 'demandID'){
               $toUpdate[$key] = $value;
               $toRet['field_changed']++;
             }
           }
           $res = $database->update('demands', $input['demandID'], $toUpdate);
           $toRet['result'] = $res;
         }
       }else{
         $toRet = 'demandID not found / not defined';
       }
     }catch(Error $e){
       http_response_code(500);
       $toRet = $e->getMessage();
     }
     return json_encode($toRet);
   }

}
