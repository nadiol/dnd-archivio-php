<?php include 'includes/header.php'; ?>
<h1>Gestione contenuti D&D</h1>
<select id="tipoDato">
  <option value="razza">Razza</option>
</select>
<br><br>
<textarea id="inputGenerico" rows="20" cols="100" placeholder="Incolla qui i dati grezzi..."></textarea><br>
<label><input type="checkbox" id="usaAI"> Usa Intelligenza Artificiale (prossimamente)</label><br><br>
<button onclick="analizzaTesto()">📥 Analizza e Scompatta</button>
<div id="outputEditor"></div>
<pre id="anteprimaJson"></pre>
<button onclick="salvaJsonFinale()" id="salvaBtn" class="hidden">💾 Scarica JSON</button>
<?php include 'includes/footer.php'; ?>