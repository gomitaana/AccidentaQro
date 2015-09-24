<?php
function finish($msg, $db = null) {
  error_log (htmlspecialchars($mainDB->error));
  header('HTTP/1.1 500 Internal Server Error');
  if(db != null) {
    $db->close();
  }
  exit;
}

function nombreTipo($category) {
  switch ($category) {
    case 'T':
      return 'Total';
    case 'FA':
      return 'Fatal';
    case 'NF':
      return 'No fatal';
    case 'SD':
      return 'Solo daños';
  }
  return 'Hay un error';
}

function nombreCausa($category) {
  switch ($category) {
    case 'CV':
      return 'Colisión con vehículo automotor';
    case 'CO':
      return 'Colisión con objeto fijo';
    case 'CC':
      return 'Colisión con ciclista';
    case 'CF':
      return 'Colisión con ferrocarril';
    case 'CN':
      return 'Colisión con animal';
    case 'V':
      return 'Volcadura';
    case 'CP':
      return 'Caída de pasajero';
    case 'CA':
      return 'Colisión con peatón (atropellamiento)';
    case 'SC':
      return 'Salida del camino';
    case 'IN':
      return 'Incendio';
    case 'NF':
      return 'Colisión con motocicleta';
    case 'OT':
      return 'Otro';
  }
  return 'Hay un error';
}

header('content-type: application/json; charset=utf-8');
header("access-control-allow-origin: *");

$mainDB = mysqli_connect('localhost','provedor', 'D6pM5PyVuBPnK8PV', 'accidentaqro');
if(mysqli_connect_errno()) {
  finish(mysqli_connect_error());
}

if(!empty($_POST['mes']) && !empty($_POST['causa']) && !empty($_POST['sexo']) 
   && !empty($_POST['alcohol'])  && !empty($_POST['cinturon']) && isset($_POST['consulta'])) {
  switch($_POST['consulta']) {
    case 1:
        $SQL = "SELECT `tipo`, `accidentes` FROM `accidentes` WHERE `mes` = ? AND `causa` = ?".
         " AND `sexo` = ? AND `alcohol` = ? AND `cinturon` = ?";
      break;
    case 2:
      $SQL = "SELECT `tipo`, `accidentes` FROM `accidentes` WHERE `mes` = ? AND `causa` = ?".
         " AND `sexo` = ? AND `alcohol` = ? AND `cinturon` = ? AND `tipo` != 'T'";
      break;
    case 3:
      $SQL = "SELECT `mes`, `accidentes` FROM `accidentes` WHERE `causa` = ?".
         " AND `sexo` = ?  AND `alcohol` = ? AND `cinturon` = ? AND `tipo` = 'T'";
      break;
    case 4:
      $SQL = "SELECT `sexo`, `tipo`, `accidentes`, `causa` FROM `accidentes` WHERE ".
          " `mes` = ?  AND `alcohol` = ? AND `cinturon` = ? AND `tipo` = 'T' AND `causa` != 'T'";
      break;
  }
  if (!($stmt = $mainDB->prepare($SQL))) {
    finish(htmlspecialchars($mainDB->error), $mainDB);
  }
  
  $keys  = array();
  $result_raw = array();
  switch($_POST['consulta']) {
    case 1:
    case 2:
      $stmt->bind_param("sssss", $_POST['mes'], $_POST['causa'], $_POST['sexo'], $_POST['alcohol'], 
                    $_POST['cinturon']);
      if (!$stmt->execute()) {
      finish(htmlspecialchars($mainDB->error), $mainDB);
      }
    
      $stmt->bind_result($tipo, $accidentes);
      while($stmt->fetch()) {
      $keys[] = $tipo;
      $result_raw[] = array(nombreTipo($tipo), $accidentes);
      }
      
      $items = array('T', 'FA', 'NF', 'SD'); // Esto es para poner en ceros los datos que no encuentre
      $result = array();
      for($i = 0; $i < 4; $i++) {
      $result[] = (($j = array_search($items[$i], $keys)) !== false)? $result_raw[$j] : 
                                      array(nombreTipo($items[$i]),  0);
      }
      break;
    case 3:
      $stmt->bind_param("ssss", $_POST['causa'], $_POST['sexo'], $_POST['alcohol'], 
                    $_POST['cinturon']);
      if (!$stmt->execute()) {
      finish(htmlspecialchars($mainDB->error), $mainDB);
      }
    
      $stmt->bind_result($mes, $accidentes);
      while($stmt->fetch()) {
      $keys[] = $mes;
      $result_raw[] = array($mes, $accidentes);
      }
      
      $items = array('EN', 'FE', 'MA', 'AB', 'MY', 'JU', 'JL', 'AG', 'SE', 'OC', 'NO', 'DI');
      $result = array();
      for($i = 0; $i < 12; $i++) {
      $result[] = (($j = array_search($items[$i], $keys)) !== false)? $result_raw[$j] : 
                                      array($items[$i],  0);
      }
      break;
    case 4:
        $stmt->bind_param("sss", $_POST['mes'], $_POST['alcohol'], $_POST['cinturon']);
      if (!$stmt->execute()) {
      finish(htmlspecialchars($mainDB->error), $mainDB);
      }
      
      $stmt->bind_result($category, $tipo, $accidentes, $causa);
      $gender = array('SF', 'M', 'H', 'T');
      while($stmt->fetch()) {
      $keys[] = $tipo;
      $result_raw[] = array(' ', array_search($category, $gender) + 1, $accidentes, nombreCausa($causa), $accidentes);
      }
      
      /*$items = array( 'FA', 'NF', 'SD'); // Esto es para poner en ceros los datos que no encuentre
      $result = array();
      for($i = 0; $i < 3; $i++) {
      $result[] = (($j = array_search($items[$i], $keys)) !== false)? $result_raw[$j] : 
                                      array(nombreTipo($items[$i]), 0, 0);
      }*/
      $result = $result_raw;
      break;
  }
  
  print json_encode($result);
} else {
  finish("Datos insuficientes", $mainDB);
}

$mainDB->close();
?>