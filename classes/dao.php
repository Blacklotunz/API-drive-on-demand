<?

interface Dao_interface{
  public function update($database, $input);
  public function delete($database, $input);
  public function add($database, $input);
  public function get($database, $id);
}
