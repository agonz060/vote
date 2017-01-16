<?php 
    session_start();

    if(!empty($_SESSION['title'])) {
        echo $_SESSION['title'];
    } else { echo 'does not exitst'; }

    $voteData = "";
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        print_r($_POST);
        $voteData = $_POST['voteData'];
        $voteData = json_decode($voteData,true);
        echo 'voteData: '; print_r($voteData);
        echo 'voteData["voteCmt"]: '.$voteData['voteCmt'];
    }
?>