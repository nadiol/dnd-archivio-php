// Script gestione-dati.js migliorato con parsing completo per sottorazze e tratti dettagliati

function analizzaTesto() {
  const tipo = document.getElementById("tipoDato").value;
  const input = document.getElementById("inputGenerico").value;

  const lines = input.split('\n').map(l => l.trim()).filter(l => l);
  const output = {
    nome: '',
    incremento_caratteristiche: {},
    tratti: {},
    lingue: [],
    sottorazze: []
  };

  let currentBlock = '';
  let buffer = '';
  let sottorazza = null;

  const flushBuffer = () => {
    if (!currentBlock || !buffer) return;
    const text = buffer.trim();
    const target = sottorazza ? sottorazza.tratti : output.tratti;

    if (currentBlock === 'robustezza' && sottorazza) {
      sottorazza.tratti['Robustezza nanica'] = text;
    } else if (currentBlock === 'lingue') {
      const lingue = text.match(/Comune|Nanico|Elfico|Draconico/gi);
      if (lingue) output.lingue = [...new Set([...output.lingue, ...lingue])];
    } else if (["allineamento", "età", "taglia", "velocità_m"].includes(currentBlock)) {
      output[currentBlock] = text;
    } else if (currentBlock) {
      const key = currentBlock.charAt(0).toUpperCase() + currentBlock.slice(1);
      target[key] = text;
    }
    buffer = '';
  };

  lines.forEach(line => {
    if (/^Nano\s*$/i.test(line)) {
      output.nome = 'Nano';
      return;
    }

    if (/^Nano delle\s+/i.test(line)) {
      flushBuffer();
      if (sottorazza) output.sottorazze.push(sottorazza);
      sottorazza = {
        nome: line.trim(),
        incremento_caratteristiche: {},
        tratti: {}
      };
      currentBlock = '';
      return;
    }

    const keywordMap = {
      'Incremento dei punteggi': 'incremento',
      'Allineamento': 'allineamento',
      'Età': 'età',
      'Taglia': 'taglia',
      'Velocità': 'velocità_m',
      'Scurovisione': 'scurovisione',
      'Resilienza': 'resilienza nanica',
      'Addestramento da combattimento': 'addestramento da combattimento',
      'Competenza negli strumenti': 'competenza negli strumenti',
      'Esperto minatore': 'esperto minatore',
      'Linguaggi': 'lingue',
      'Robustezza': 'robustezza'
    };

    for (const k in keywordMap) {
      if (line.startsWith(k)) {
        flushBuffer();
        currentBlock = keywordMap[k];
        return;
      }
    }

    if (currentBlock === 'incremento') {
      if (/Costituzione.*\d/.test(line)) {
        const descr = line;
        output.incremento_caratteristiche['Costituzione'] = descr;
      } else if (/Saggezza.*\d/.test(line) && sottorazza) {
        const descr = line;
        sottorazza.incremento_caratteristiche['Saggezza'] = descr;
      }
    } else {
      buffer += (buffer ? ' ' : '') + line;
    }
  });

  flushBuffer();
  if (sottorazza) output.sottorazze.push(sottorazza);
  mostraEditor(output);
  aggiornaAnteprima();
}

function mostraEditor(dati) {
  const container = document.getElementById("outputEditor");
  container.innerHTML = '';
  window.currentData = dati;

  const createField = (key, value, prefix = '') => {
    const wrapper = document.createElement("div");
    wrapper.style.marginBottom = "1em";
    const label = document.createElement("label");
    label.textContent = prefix + key;
    label.style.fontWeight = "bold";
    label.style.display = "block";
    const input = document.createElement("input");
    input.name = prefix + key;
    input.value = value;
    input.style.width = '100%';
    input.style.marginTop = '0.25em';
    input.addEventListener('input', aggiornaAnteprima);
    wrapper.appendChild(label);
    wrapper.appendChild(input);
    container.appendChild(wrapper);
  };

  for (const key in dati) {
    const value = dati[key];

    if (typeof value === 'object' && !Array.isArray(value)) {
      const fieldset = document.createElement("fieldset");
      fieldset.style.margin = "1em 0";
      fieldset.style.padding = "1em";
      fieldset.style.border = "1px solid #ccc";
      const legend = document.createElement("legend");
      legend.textContent = key;
      legend.style.fontWeight = "bold";
      fieldset.appendChild(legend);

      for (const sub in value) {
        createField(sub, value[sub], `${key}.`);
      }
      container.appendChild(fieldset);
    } else if (Array.isArray(value)) {
      if (key === 'sottorazze') {
        value.forEach((sotto, index) => {
          const fieldset = document.createElement("fieldset");
          fieldset.style.margin = "1em 0";
          fieldset.style.padding = "1em";
          fieldset.style.border = "1px dashed #999";
          const legend = document.createElement("legend");
          legend.textContent = `Sottorazza ${index + 1}`;
          legend.style.fontWeight = "bold";
          fieldset.appendChild(legend);
          for (const subkey in sotto) {
            if (typeof sotto[subkey] === 'object') {
              for (const k in sotto[subkey]) {
                createField(`${subkey}.${k}`, sotto[subkey][k], `sottorazze[${index}].`);
              }
            } else {
              createField(subkey, sotto[subkey], `sottorazze[${index}].`);
            }
          }
          container.appendChild(fieldset);
        });
      } else {
        createField(key, value.join(', '));
      }
    } else {
      createField(key, value);
    }
  }
  document.getElementById("salvaBtn").classList.remove("hidden");
}

function aggiornaAnteprima() {
  const inputs = document.querySelectorAll("#outputEditor input");
  const dati = window.currentData;
  inputs.forEach(input => {
    const path = input.name.split('.');
    let ref = dati;
    for (let i = 0; i < path.length - 1; i++) {
      if (path[i].includes("[") && path[i].includes("]")) {
        const base = path[i].split('[')[0];
        const idx = parseInt(path[i].match(/\[(\d+)\]/)[1]);
        ref = ref[base][idx];
      } else {
        ref = ref[path[i]];
      }
    }
    ref[path[path.length - 1]] = input.value;
  });
  const output = JSON.stringify(dati, null, 2);
  document.getElementById("anteprimaJson").textContent = output;
}

function salvaJsonFinale() {
  const blob = new Blob([
    document.getElementById("anteprimaJson").textContent
  ], { type: "application/json" });
  const link = document.createElement("a");
  link.href = URL.createObjectURL(blob);
  link.download = "razza_dnd.json";
  link.click();
}
