<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Gestione contenuti D&D</title>
  <script src="js/gestione-dati.js" defer></script>
  <style>
    body { font-family: sans-serif; margin: 2rem; }
    textarea { width: 100%; font-family: monospace; margin-bottom: 1rem; }
    select, button { font-size: 1rem; margin-right: 0.5rem; }
    #outputEditor label { display: block; margin-top: 0.5rem; }
    fieldset { margin-top: 1rem; }
    pre { background: #eee; padding: 1rem; margin-top: 1rem; white-space: pre-wrap; word-break: break-word; }
  </style>
</head>
<body>
  <h1>Gestione contenuti D&D</h1>

  <label for="tipoDato">Tipo di dato:</label>
  <select id="tipoDato">
    <option value="razza">Razza</option>
    <option value="classe">Classe</option>
    <option value="sottoclasse">Sottoclasse</option>
    <option value="talento">Talento</option>
    <option value="incantesimo">Incantesimo</option>
    <option value="oggetto">Oggetto</option>
  </select>

  <label><input type="checkbox" id="usaAI" disabled> Usa intelligenza artificiale (beta)</label>

  <textarea id="inputGenerico" rows="20" placeholder="Incolla qui il testo da analizzare..."></textarea>
  <br>

  <button onclick="analizzaTesto()">📥 Analizza e Scompatta</button>
  <button onclick="salvaJsonFinale()" id="salvaBtn" class="hidden">💾 Scarica JSON</button>

  <div id="outputEditor"></div>
  <pre id="anteprimaJson"></pre>
</body>
</html>
