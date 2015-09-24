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

header('content-type: application/json; charset=utf-8');
header("access-control-allow-origin: *");

$mainDB = mysqli_connect('localhost','provedor', 'D6pM5PyVuBPnK8PV', 'accidentaqro');
if(mysqli_connect_errno()) {
  finish(mysqli_connect_error());
}

if(!empty($_POST['mes']) && !empty($_POST['causa']) && !empty($_POST['sexo']) 
   && !empty($_POST['alcohol'])  && !empty($_POST['cinturon']) && isset($_POST['consulta'])) {
      
  $SQL = "SELECT `tipo`, `accidentes` FROM `accidentes` WHERE `mes` = ? AND `causa` = ?".
          " AND `sexo` = ? AND `alcohol` = ? AND `cinturon` = ?";
  if (!($stmt = $mainDB->prepare($SQL))) {
    finish(htmlspecialchars($mainDB->error), $mainDB);
  }
  
  $stmt->bind_param("sssss", $_POST['mes'], $_POST['causa'], $_POST['sexo'], $_POST['alcohol'], 
                    $_POST['cinturon']);
  if (!$stmt->execute()) {
    finish(htmlspecialchars($mainDB->error), $mainDB);
  }
  
  $keys  = array();
  $result_raw = array();
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
  
  print json_encode($result);
} else {
  finish("Datos insuficientes", $mainDB);
}

$mainDB->close();
?>