
// js/lettura_drive.js

let definizioni = {};
const vociPerCategoria = {
  razze: [], classi: [], sottoclassi: [], incantesimi: [],
  oggetti: [], talenti: [], competenze: [], meccaniche_gioco: [], tabelle_slot: []
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
    const response = await fetch(`proxy.php?id=${indexId}`);
    const json = await response.json();
    vociPerCategoria[categoria] = json;
    json.forEach(el => idMap[el.nome] = el.id);
  }

  const elenco = vociPerCategoria[categoria];
  lista.innerHTML = elenco.map(voce =>
    `<label><input type="checkbox" class="voce-selezionata" value="${voce.nome}"> ` +
    `<span onclick="caricaContenutoSingolo('${voce.nome}')" style="cursor:pointer; text-decoration:underline;">${voce.nome}</span></label><br>`
  ).join("");

  popup.style.display = "block";
  popupAperto = true;
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
    const res = await fetch(`proxy.php?id=${id}`);
    const json = await res.json();
    return renderizzaVoce(voce, json);
  }));
  contenitore.innerHTML = blocchi.join('<div class="separatore"></div>');
}

async function caricaContenutoSingolo(voce) {
  if (!idMap[voce]) return;
  const contenitore = document.getElementById("contenutoJSON");
  contenitore.innerHTML = "Caricamento...";
  const res = await fetch(`proxy.php?id=${idMap[voce]}`);
  const json = await res.json();
  contenitore.innerHTML = await renderizzaVoce(voce, json);
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

async function renderizzaVoce(nome, json) {
  const render = async (key, value) => {
    if (Array.isArray(value)) {
      return `<strong>${key}:</strong> <ul>` + value.map(v => `<li>${v}</li>`).join("") + "</ul>";
    }

    // Supporto per tabelle slot esterne
    if (typeof value === "string" && value.startsWith("inserire_tabella_slot_livelli_")) {
      const classe = value.replace("inserire_tabella_slot_livelli_", "");
      const fileKey = "tabella_slot/tabella_slot_livelli_" + classe + ".json";
      const id = idMap[fileKey];

      if (id) {
        const res = await fetch(`proxy.php?id=${id}`);
        const data = await res.json();
        return `<strong>${key}:</strong><pre>${JSON.stringify(data.slot_incantesimi_per_livello, null, 2)}</pre>`;
      } else {
        return `<strong>${key}:</strong> <em>[tabella slot non trovata per ${classe}]</em>`;
      }
    }

    if (typeof value === "object" && value !== null) {
      return `<strong>${key}:</strong><pre>${JSON.stringify(value, null, 2)}</pre>`;
    }

    return `<strong>${key}:</strong> ${value}`;
  };

  let html = `<div class="box"><h2>${nome}</h2>`;
  for (const [key, value] of Object.entries(json)) {
    html += "<div class='campo-json'>" + await render(key, value) + "</div>";
  }
  html += "</div>";
  return html;
}
