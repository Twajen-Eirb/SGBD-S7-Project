<?php
ob_start();
include "connect.php";
include "header.php";

// Required field names
$required = array('nom', 'prenom', 'adresse', 'num_lic', 'date');

// Loop over field names, make sure each one exists and is not empty
$error = !(isset($_GET['id_joueur']) || isset($_GET['new']));

foreach($required as $field) {
  if (empty($_POST[$field])) {
    $error = true;
  }
}

if ($error) {
  console_log("All fields are required.");
  header("Location: joueurs.php");
  ob_enf_flush();
  exit();
}

$nom = $_POST["nom"];
$prenom = $_POST["prenom"];
$adresse = $_POST["adresse"];
$num_lic = $_POST["num_lic"];
$date = $_POST["date"];
if (isset($_GET['id_joueur'])) {
    $id = $_GET["id_joueur"];

    $requete_jou = "update JOUEUR set NUMERO_LICENCE = ".$num_lic.", DATE_NAISSANCE = \"".$date."\" where ID_JOUEUR = ?;";
                
    $requete_ind = "update INDIVIDU
                set NOM_INDIVIDU = '".$nom."', PRENOM_INDIVIDU = '".$prenom."', ADRESSE = '".$adresse."'
                where ID_INDIVIDU = ?;";
    console_log($requete_jou);
    if($res_jou = $connection->prepare($requete_jou)){
        $res_jou->bind_param('i', $id);
        $res_jou->execute();
        if ( $res_ind = $connection->prepare($requete_ind) ) {
            $res_ind->bind_param('i', $id);
            $res_ind->execute();
        } else {
        console_log("erreur de requete update");
        }
    } else {
        console_log("erreur de requete update");
    }
    
} else if (isset($_GET['new'])) {
    $requete_ind = "insert into INDIVIDU (NOM_INDIVIDU, PRENOM_INDIVIDU, ADRESSE) 
                values (\"".$nom."\", \"".$prenom."\", \"".$adresse."\");";
    
    if($res_ind = $connection->prepare($requete_ind)) {
        $res_ind->execute();
        $new_id = $connection->insert_id;
        $requete_spo = "insert into SPORTIF (ID_SPORTIF) 
                        values (".$new_id."); ";
        $requete_jou = "insert into JOUEUR (ID_JOUEUR, NUMERO_LICENCE, DATE_NAISSANCE) 
                        values (".$new_id.", ".$num_lic.", \"".$date."\");";
    
        if($res_spo = $connection->query($requete_spo) && $res_jou = $connection->query($requete_jou)) {
            console_log("done");
        } else {
            console_log("erreur de requete d\'ajout");
        }
    } else 
        console_log("erreur de requete d\'ajout");

}
$connection->close();
header("Location: joueurs.php");
ob_enf_flush();
exit();
?>
