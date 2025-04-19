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
    <option value="meccaniche_gioco">Meccaniche di Gioco</option>
  </select>
</div>

<!-- Popup dinamico con elenco cliccabile -->
<div id="popupVociCategoria" class="popup-categoria" style="display: none; position: absolute; background: #fff; border: 1px solid #ccc; padding: 10px; border-radius: 8px; z-index: 1000; max-height: 400px; overflow-y: auto;">
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
const driveIndexFileId = "1_FqDS1q3XmOHeJf46TtqGWgGq69IQik2";
let definizioni = {};
const vociPerCategoria = {
  razze: [], classi: [], sottoclassi: [], incantesimi: [],
  oggetti: [], talenti: [], competenze: [], meccaniche_gioco: []
};
let idMap = {};
let popupAperto = false;

window.addEventListener("click", function(event) {
  const popup = document.getElementById("popupVociCategoria");
  const select = document.getElementById("selezionaCategoria");
  if (popupAperto && !popup.contains(event.target) && event.target !== select) {
    popup.style.display = "none";
    popupAperto = false;
    const selezionate = Array.from(document.querySelectorAll('.voce-selezionata:checked')).map(cb => cb.value);
    if (selezionate.length > 0) {
      mostraContenutiMultipli(selezionate);
    }
  }
});

async function mostraPopupVociCategoria() {
  const categoria = document.getElementById("selezionaCategoria").value;
  const popup = document.getElementById("popupVociCategoria");
  const lista = document.getElementById("listaVociCategoria");

  if (!categoria) {
    popup.style.display = "none";
    return;
  }

  if (vociPerCategoria[categoria].length === 0) {
    const indexId = await getIdForIndex(categoria);
    const response = await fetch(`https://drive.google.com/uc?export=download&id=${indexId}`);
    const json = await response.json();
    vociPerCategoria[categoria] = json;
    json.forEach(el => idMap[el.nome] = el.id);
  }

  const elenco = vociPerCategoria[categoria];
  lista.innerHTML = elenco.map(voce =>
    `<label><input type="checkbox" class="voce-selezionata" value="${voce.nome}"> ` +
    `<span onclick="caricaContenutoSingolo('${voce.nome}')" style="cursor:pointer; text-decoration:underline;">${voce.nome}</span></label><br>`
  ).join("");

  popupAperto = true;
  popup.style.display = "block";
}

function confermaSelezioneVoce() {
  const selezionate = Array.from(document.querySelectorAll('.voce-selezionata:checked')).map(cb => cb.value);
  if (selezionate.length > 0) {
    document.getElementById("popupVociCategoria").style.display = "none";
    popupAperto = false;
    mostraContenutiMultipli(selezionate);
  }
}

async function mostraContenutiMultipli(voci) {
  const contenitore = document.getElementById("contenutoJSON");
  contenitore.innerHTML = "Caricamento...";
  const blocchi = await Promise.all(voci.map(async (voce) => {
    const id = idMap[voce];
    const res = await fetch(`https://drive.google.com/uc?export=download&id=${id}`);
    const json = await res.json();
    return renderizzaVoce(voce, json);
  }));
  contenitore.innerHTML = blocchi.join('<div class="separatore"></div>');
}

async function caricaContenutoSingolo(voce) {
  if (!idMap[voce]) return;
  const contenitore = document.getElementById("contenutoJSON");
  contenitore.innerHTML = "Caricamento...";
  const res = await fetch(`https://drive.google.com/uc?export=download&id=${idMap[voce]}`);
  const json = await res.json();
  contenitore.innerHTML = renderizzaVoce(voce, json);
}

function filtraContenuto() {
  const input = document.getElementById("searchInput").value.toLowerCase();
  const contenuti = document.querySelectorAll("#contenutoJSON .box");
  contenuti.forEach(box => {
    const text = box.innerText.toLowerCase();
    box.style.display = text.includes(input) ? "" : "none";
  });
}

function aggiungiTooltip(chiave) {
  if (definizioni[chiave]) {
    return ` data-tooltip="${definizioni[chiave]}"`;
  }
  return "";
}

function renderizzaVoce(nome, json) {
  const render = (key, value) => {
    if (Array.isArray(value)) {
      return '<ul>' + value.map(v => `<li>${typeof v === 'object' ? render('', v) : v}</li>`).join('') + '</ul>';
    } else if (typeof value === 'object') {
      return '<ul>' + Object.entries(value).map(([k, v]) => `<li><strong${aggiungiTooltip(k)}>${k}:</strong> ${render(k, v)}</li>`).join('') + '</ul>';
    } else {
      return value;
    }
  };
  return `<div class="box"><h2 class="titolo-voce">${nome}</h2>${render('', json)}</div>`;
}

async function getIdForIndex(categoria) {
  const res = await fetch(`https://drive.google.com/uc?export=download&id=${driveIndexFileId}`);
  const json = await res.json();
  const file = json.find(e => e.nome === `${categoria}_index.json` || e.nome === `${categoria}/${categoria}_index.json`);
  return file?.id;
}

(async () => {
  const defFile = await fetch("https://drive.google.com/uc?export=download&id=1uoRwlA5A-Uxe_Uezg2xENVrTWVBiCyC-");
  definizioni = await defFile.json();
})();
</script>

<?php include 'includes/footer.php'; ?>
