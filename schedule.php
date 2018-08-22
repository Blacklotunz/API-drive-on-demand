<?
header('Content-Type: application/json');
require_once('autoloader.php');

$debug=false;
$database = new dbController();

$schedule;
$unscheduled;

$demands = $database->get('demands');

foreach ($demands as $dkey => $demand) {
  if($debug){
    echo("\n-------DEMAND---------\n");
    echo($dkey."\n");
    print_r($demand);
  }

  $cars = $database->get('cars');
  $featureRequired = isset($demand['desired_features']);

  $eligibleCars = array();

  foreach ($cars as $ckey => $car) {
    if(!isset($schedule[$ckey])){
      $schedule[$ckey] = array();
    }
    $eligibleCar = true;

    if($featureRequired){
      //pick all cars that satisfy demand's feature requirements
      $desired_features = $demand['desired_features'];
      foreach ($desired_features as $feature) {
        $fkey = key($feature);
        if(key_exists($fkey, $car) && $feature[$fkey] == $car[$fkey]){
          $eligibleCar = true;
        }else{
          $eligibleCar = false;
          break;
        }
      }
    }
    if(!$eligibleCar){
      if($debug) echo('Feature missing'."on carID $ckey \n");
      continue;
    }
    //check if start demand time is GT last demand end time, otherwise car is busy
    $latestDemand = array('start_time' => 0,
    'end_time'   => 0);

    if(count($schedule[$ckey]) > 0){ //if a schedule exists for the given car
      if($debug){
        echo ('Find schedule for vehicle '."$ckey print schedule and checking time\n");
        var_dump($schedule[$ckey]);
      }
      $index = count($schedule[$ckey])-1;
      if(!empty($schedule[$ckey][$index])){
        $latestDemand = $schedule[$ckey][$index]; //then get the latest scheduled demand
      }
      if($debug){
        print_r("latest demand for carID $ckey \n");
        var_dump($latestDemand);
      }
    }

    //then check if demand time intersect with car latest scheduled demand
    //this should work, given an ordinated (by pick-up/drop-off time) list of scheduled demands
    if($demand['start_time'] <= $latestDemand['end_time'] && $demand['end_time'] >= $latestDemand['start_time']){
    if($debug){
      echo("Time mismatch for car $ckey. Check next car\n");
    }
      $eligibleCar = false;
      continue;
    }

    if($eligibleCar){
      $eligibleCars[$ckey] = $car;
    }
  }
  //schedule the given demand for the nearest eligible car at pick-up time
  if($debug){
    echo("------ELIGIBILITY-----\n");
    print_r($eligibleCars);
  }

  if(empty($eligibleCars)){
    if($debug) echo ("no eligible cars found for the demand $dkey\n");
    $unscheduled[$dkey] = $demand;
    continue;
  }
  //this for minimize the overall travelled car distance
  $minDistance = null;
  foreach ($eligibleCars as $ckey => $car) {
    if(count($schedule[$ckey]) > 0){
      $latestDemand = $schedule[$ckey][count($schedule[$ckey])-1];
      $carLocation = $latestDemand['drop_off_location'];
    }else{
      $carLocation = $car['current_location'];
    }
    //calculate distance into a MONO dimentional space
    $distance = abs($demand['pick_up_location'] - $carLocation);

    //if it's first calculated distance, then set it as minDistance, else
    //just check if calculated distance is less than old minimum distance
    if(!isset($minDistance) || $minDistance > $distance){
      $minDistance = $distance;
      $nearestCar = $ckey;
    }
    //$minDistance = !isset($minDistance) ? $distance : $minDistance >  $distance ? $distance : $minDistance;
  }

  //schedule demand for nearest Car
  $schedule[$nearestCar][] = $demand;
  $toUpdate = $database->get('demands', $dkey);
  $toUpdate['carID'] = $nearestCar;
  $database->update('demands', $dkey, $toUpdate);
}

if($debug){
  echo("\n-------SCHEDULE---------\n");
  var_dump($schedule);
}

$toRet = array('schedule'   => $schedule,
               'unscheduled'=> @$unscheduled);
echo(json_encode($toRet));
