<?
// ///////////////////////////////////////////////////////////////////
/*
* In order to give a fast feedback, I decided to use
* a memory database that stores persistent information
* on file. Ofc this is NOT to be intended as final
* implementation. To reset data, delete the data/database.dat file
*/
class dbController{

  private $database;

  public function __construct(){
    if (! file_exists(DB_LOCATION)) {
      // db init if not exists
      $empty_db = array(
        'users' => array(),
        'cars' => array(),
        'demands' => array(),
        'features' => array()
      );
      file_put_contents(DB_LOCATION, serialize($empty_db));
    }
    // loading db
    $this->database = unserialize(file_get_contents(DB_LOCATION));
    // ///////////////////////////////////////////////////////////////////
  }

  public function insert($table, $toInsert){
    try{
      if(!empty($this->database[$table])){
        //get last index-key
        $nextIndex = array_keys($this->database[$table])[count($this->database[$table])-1];
        $nextIndex++;
      }else{
        $nextIndex = 0;
      }
      $this->database[$table][$nextIndex] = $toInsert;
      $toRet = $nextIndex;
      //commit changes
      $this->commit();
    }catch(Error $e){
      $toRet = $e;
    }

    return $toRet;
  }

  public function delete($table, $id = null){
    if(!isset($id)){
      //delete all table
      unset($this->database[$table]);
    }else if(key_exists($id, $this->database[$table])){
      $toRet = $this->database[$table][$id];
      //then remove
      unset($this->database[$table][$id]);
    }else{
      $toRet = "$id not found in $table table";
    }

    $this->commit();
    return $toRet;
  }

  public function update($table, $id, $toUpdate){
    //return number of row changed
    if(isset($id) && isset($this->database[$table][$id])){
        $this->database[$table][$id] = $toUpdate;
        $toRet = 1;
        $this->commit();
    }else{
      $toRet = 0;
    }
    return $toRet;
  }

  public function commit(){
    //commit changes
    file_put_contents(DB_LOCATION, serialize($this->database));
  }

  public function get($table, $id = null){
    if(!isset($id)){
      return $this->database[$table];
    }
    if(key_exists($id,$this->database[$table])){
      return $this->database[$table][$id];
    }else{
      return null;
    }
  }


}
