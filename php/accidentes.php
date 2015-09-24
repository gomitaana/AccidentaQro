<?php
function finish($msg, $db = null) {
  error_log (htmlspecialchars($mainDB->error));
  header('HTTP/1.1 500 Internal Server Error');
  if(db != null) {
    $db->close();
  }
  exit;
}

$data = array('mes' => 'T', 'causa' => 'T', 'sexo' => 'T', 'alcohol' => 'T', 'cinturon' => 'T');

$mainDB = mysqli_connect('localhost','provedor', 'D6pM5PyVuBPnK8PV', 'accidentaqro');
if(mysqli_connect_errno()) {
  finish(mysqli_connect_error());
}

if(!empty($data['mes']) && !empty($data['causa']) && !empty($data['sexo']) 
   && !empty($data['alcohol'])  && !empty($data['cinturon'])) {
      
  $SQL = "SELECT `tipo`, `accidentes` FROM `accidentes` WHERE `mes` = ? AND `causa` = ?".
          " AND `sexo` = ? AND `alcohol` = ? AND `cinturon` = ?";
  if (!($stmt = $mainDB->prepare($SQL))) {
    finish(htmlspecialchars($mainDB->error), $mainDB);
  }
  
  $stmt->bind_param("sssss", $data['mes'], $data['causa'], $data['sexo'], $data['alcohol'], $data['cinturon']);
  if (!$stmt->execute()) {
    finish(htmlspecialchars($mainDB->error), $mainDB);
  }
  
  $res = array();
  $stmt->bind_result($tipo, $accidentes);
  while($stmt->fetch()) {
    $res[] = array('tipo' => $tipo, 'accidentes' => $accidentes);
  }
  
  echo json_encode($res);
} else {
  finish("Datos insuficientes", $mainDB);
}

$mainDB->close();
?>