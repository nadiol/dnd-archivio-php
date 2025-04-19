<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Gestione Dati D&D</title>
  <link rel="stylesheet" href="css/style.css">
  <script src="js/gestione-dati.js" defer></script>
</head>
<body>
  <h1>Gestione contenuti D&D</h1>
  <label for="tipoDato">Tipo di dato:</label>
  <select id="tipoDato">
    <option value="razza">Razza</option>
    <option value="classe">Classe</option>
    <option value="talento">Talento</option>
    <option value="oggetto">Oggetto</option>
  </select>
  <br><br>
  <textarea id="inputGenerico" rows="20" cols="80" placeholder="Incolla qui il testo dal manuale..."></textarea><br>
  <button onclick="analizzaTesto()">📥 Analizza e Scompatta</button>
  <hr>
  <div id="outputEditor"></div>
  <button id="salvaBtn" class="hidden" onclick="salvaJsonFinale()">💾 Scarica JSON</button>
  <pre id="anteprimaJson"></pre>
</body>
</html>