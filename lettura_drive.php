<?php include_once 'utils.php'; ?>
<?php include 'includes/header.php'; ?>

<h1 class="titolo">Visualizza contenuti da Google Drive</h1>

<!-- Menu a tendina per categorie -->
<div class="selettore-categoria">
  <label for="selezionaCategoria">Scegli una categoria:</label>
  <select id="selezionaCategoria" onchange="mostraPopupVociCategoria()">
    <option value="">-- Seleziona --</option>
    <option value="razze">Razze</option>
    <option value="classi">Classi</option>
    <option value="sottoclassi">Sottoclassi</option>
    <option value="incantesimi">Incantesimi</option>
    <option value="oggetti">Oggetti</option>
    <option value="talenti">Talenti</option>
    <option value="competenze">Competenze</option>
  </select>
</div>

<!-- Popup dinamico con elenco cliccabile -->
<div id="popupVociCategoria" class="popup-categoria">
  <strong>Seleziona voce:</strong>
  <div id="listaVociCategoria"></div>
  <button onclick="confermaSelezioneVoce()" class="btn-conferma">✔️ Conferma selezione</button>
</div>

<!-- Campo ricerca -->
<div class="barra-ricerca">
  <input type="text" id="searchInput" placeholder="🔍 Cerca nei dati...">
  <button onclick="filtraContenuto()" class="btn-ricerca">✔️</button>
</div>

<div id="contenutoJSON"></div>

<script>
const driveIndexFile = "drive_index.json";
const vociPerCategoria = {
  razze: [],
  classi: [],
  sottoclassi: [],
  incantesimi: [],
  oggetti: [],
  talenti: [],
  competenze: []
};

async function mostraPopupVociCategoria() {
  const categoria = document.getElementById("selezionaCategoria").value;
  const popup = document.getElementById("popupVociCategoria");
  const lista = document.getElementById("listaVociCategoria");

  if (!categoria) {
    popup.style.display = "none";
    return;
  }

  if (vociPerCategoria[categoria].length === 0) {
    const response = await fetch(`https://drive.google.com/uc?export=download&id=${await getIdForIndex(categoria)}`);
    vociPerCategoria[categoria] = await response.json();
  }

  const elenco = vociPerCategoria[categoria];
  if (categoria === "sottoclassi") {
    lista.innerHTML = elenco.map(voce =>
      `<details><summary><strong>${voce.nome}</strong> (${voce.classe})</summary>` +
      `<p>${voce.descrizione}</p><ul>` +
      Object.entries(voce.tratti).map(([k,v]) => `<li><strong>${k}:</strong> ${v}</li>`).join("") +
      `</ul></details><hr>`
    ).join("");
  } else {
    lista.innerHTML = elenco.map(voce =>
      `<label><input type="checkbox" class="voce-selezionata" value="${voce}"> ${voce}</label><br>`
    ).join("");
  }

  popup.style.display = "block";
}

function confermaSelezioneVoce() {
  const selezionate = Array.from(document.querySelectorAll('.voce-selezionata:checked')).map(cb => cb.value);
  if (selezionate.length > 0) {
    document.getElementById("popupVociCategoria").style.display = "none";
    mostraContenutiMultipli(selezionate);
  }
}

function mostraContenutiMultipli(voci) {
  const contenitore = document.getElementById("contenutoJSON");
  contenitore.innerHTML = voci.map(voce =>
    `<div class="box"><h2 class="titolo-voce">${voce}</h2><p>Dati caricati da JSON (placeholder)...</p></div>`
  ).join('<div class="separatore"></div>');
}

function filtraContenuto() {
  const input = document.getElementById("searchInput").value.toLowerCase();
  const contenuti = document.querySelectorAll("#contenutoJSON .box");
  contenuti.forEach(box => {
    const text = box.innerText.toLowerCase();
    box.style.display = text.includes(input) ? "" : "none";
  });
}

async function getIdForIndex(categoria) {
  const res = await fetch("https://drive.google.com/uc?export=download&id=1_FqDS1q3XmOHeJf46TtqGWgGq69IQik2");
  const json = await res.json();
  const file = json.find(e => e.nome === `${categoria}_index.json`);
  return file?.id;
}
</script>

<?php include 'includes/footer.php'; ?>
