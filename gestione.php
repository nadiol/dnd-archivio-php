<?php
// gestione.php - Pagina PHP per inserimento e analisi dei dati da manuale
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8" />
  <title>Gestione contenuti D&D</title>
  <link rel="stylesheet" href="css/style.css" />
  <script src="js/gestione-dati.js" defer></script>
</head>
<body>
  <h1>Gestione contenuti D&D</h1>

  <label for="tipoDato">Tipo di dato: </label>
  <select id="tipoDato">
    <option value="razza">Razza</option>
    <option value="classe">Classe</option>
    <option value="sottoclasse">Sottoclasse</option>
    <option value="talento">Talento</option>
    <option value="abilità">Abilità</option>
    <option value="oggetto">Oggetto</option>
    <option value="arma">Arma</option>
    <option value="incantesimo">Incantesimo</option>
    <option value="patrono">Patrono</option>
  </select>

  <br><br>
  <textarea id="inputGenerico" rows="20" cols="100" placeholder="Incolla qui il testo..."></textarea>
  <br>
  <button onclick="analizzaTesto()">📥 Analizza e Scompatta</button>

  <h2>Dati Estratti (Modificabili)</h2>
  <div id="outputEditor"></div>

  <button id="salvaBtn" class="hidden" onclick="salvaJsonFinale()">💾 Scarica JSON</button>

  <h2>Anteprima del JSON Finale</h2>
  <pre id="anteprimaJson">{}</pre>
</body>
</html>
