<?php include 'includes/header.php'; ?>
<main>
  <h2>Gestione Dati (PHP + JS)</h2>
  <label for="tipoDato">Tipo di contenuto:</label>
  <select id="tipoDato">
    <option value="razza">Razza</option>
    <option value="classe">Classe</option>
    <option value="sottoclasse">Sottoclasse</option>
    <option value="talento">Talento</option>
    <option value="abilità">Abilità</option>
    <option value="oggetto">Oggetto</option>
    <option value="arma">Arma</option>
    <option value="patrono">Patrono</option>
    <option value="incantesimo">Incantesimo</option>
  </select>
  <br><br>
  <textarea id="inputGenerico" rows="15" cols="100" placeholder="Incolla qui il testo grezzo dal manuale..."></textarea><br>
  <button onclick="analizzaTesto()">📥 Analizza e Scompatta</button>

  <section>
    <h3>Dati Estratti (Modificabili)</h3>
    <div id="outputEditor"></div>
    <button id="salvaBtn" class="hidden" onclick="salvaJsonFinale()">💾 Salva come JSON</button>
  </section>

  <section>
    <h3>Anteprima del JSON Finale</h3>
    <pre id="anteprimaJson" style="background:#f4f4f4; padding:1em; overflow-x:auto;"></pre>
  </section>
</main>
<script src="js/gestione-dati.js"></script>
<?php include 'includes/footer.php'; ?>