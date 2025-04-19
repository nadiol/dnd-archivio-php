<?php
function ripristina_accenti($testo) {
    $sostituzioni = [
        "eta" => "età",
        "maturita" => "maturità",
        "abilita" => "abilità",
        "punteggi" => "punteggi",
        "piu" => "più",
        "linguaggio" => "linguaggio",
        "immunita" => "immunità",
        "resistenza" => "resistenza",
        "percezione" => "percezione",
        "affascinato" => "affascinato",
        "velocita" => "velocità",
        "costituzione" => "Costituzione",
        "intelligenza" => "Intelligenza",
        "saggezza" => "Saggezza",
        "forza" => "Forza",
        "carisma" => "Carisma",
        "destrezza" => "Destrezza"
    ];

    foreach ($sostituzioni as $originale => $accentato) {
        $testo = str_replace($originale, $accentato, $testo);
    }

    return $testo;
}
?>
