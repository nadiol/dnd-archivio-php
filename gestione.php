<?php include 'includes/header.php'; ?>
include_once 'utils.php';
<h1>Gestione contenuti D&D</h1>
<select id="tipoDato">
  <option value="razza">Razza</option>
</select><br><br>
<textarea id="inputGenerico" rows="20" cols="100" placeholder="Incolla qui il testo da analizzare..."></textarea><br>
<button onclick="analizzaTesto()">📥 Analizza e Scompatta</button><br><br>
<div id="outputEditor"></div>
<pre id="anteprimaJson"></pre>
<button onclick="salvaJsonFinale()" id="salvaBtn" class="hidden">💾 Scarica JSON</button>
<script src="js/gestione-dati.js" defer></script>
<?php include 'includes/footer.php'; ?>