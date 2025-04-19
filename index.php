<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Archivio D&D (PHP)</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <h1>Benvenuto nell'Archivio D&D (PHP)</h1>
  <textarea id="inputGenerico" rows="15" cols="100" placeholder="Incolla qui i dati..."></textarea><br>
  <select id="tipoDato">
    <option value="razza">Razza</option>
    <option value="classe">Classe</option>
  </select>
  <button onclick="analizzaTesto()">📥 Analizza e Scompatta</button>
  <div id="outputEditor"></div>
  <pre id="anteprimaJson"></pre>
  <button id="salvaBtn" class="hidden" onclick="salvaJsonFinale()">💾 Scarica JSON</button>
  <script src="js/gestione-dati.js"></script>
</body>
</html>
