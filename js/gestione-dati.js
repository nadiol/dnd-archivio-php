
// Script gestione-dati.js migliorato con parsing completo delle caratteristiche

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
  let currentText = '';
  let sottorazza = null;

  const pushCurrentBlock = () => {
    if (currentBlock && currentText) {
      switch (currentBlock) {
        case 'allineamento': output.allineamento = currentText; break;
        case 'età': output.età = currentText; break;
        case 'taglia': output.taglia = currentText; break;
        case 'velocità': output.velocità_m = 7.5; break;
        case 'lingue':
          const lingue = currentText.match(/Comune|Nanico|Elfico|Draconico/gi);
          if (lingue) output.lingue.push(...lingue);
          break;
        default:
          const target = sottorazza ? sottorazza.tratti : output.tratti;
          target[currentBlock.charAt(0).toUpperCase() + currentBlock.slice(1)] = currentText;
          break;
      }
    }
    currentText = '';
  };

  lines.forEach(line => {
    if (/^Nano\s*$/i.test(line)) {
      if (!output.nome) output.nome = line.trim();
      return;
    }
    if (/^Nano delle\s+/i.test(line)) {
      pushCurrentBlock();
      if (sottorazza) output.sottorazze.push(sottorazza);
      sottorazza = {
        nome: line.trim(),
        incremento_caratteristiche: {},
        tratti: {}
      };
      currentBlock = '';
      return;
    }

    if (/^Incremento dei punteggi/i.test(line)) {
      pushCurrentBlock();
      currentBlock = 'caratteristiche';
      return;
    }
    if (/^Allineamento/i.test(line)) {
      pushCurrentBlock();
      currentBlock = 'allineamento';
      return;
    }
    if (/^Età/i.test(line)) {
      pushCurrentBlock();
      currentBlock = 'età';
      return;
    }
    if (/^Taglia/i.test(line)) {
      pushCurrentBlock();
      currentBlock = 'taglia';
      return;
    }
    if (/^Velocità/i.test(line)) {
      pushCurrentBlock();
      currentBlock = 'velocità';
      return;
    }
    if (/^Scurovisione/i.test(line)) {
      pushCurrentBlock();
      currentBlock = 'scurovisione';
      return;
    }
    if (/^Resilienza/i.test(line)) {
      pushCurrentBlock();
      currentBlock = 'resilienza nanica';
      return;
    }
    if (/^Addestramento da combattimento/i.test(line)) {
      pushCurrentBlock();
      currentBlock = 'addestramento da combattimento';
      return;
    }
    if (/^Competenza negli strumenti/i.test(line)) {
      pushCurrentBlock();
      currentBlock = 'competenza negli strumenti';
      return;
    }
    if (/^Esperto minatore/i.test(line)) {
      pushCurrentBlock();
      currentBlock = 'esperto minatore';
      return;
    }
    if (/^Linguaggi/i.test(line)) {
      pushCurrentBlock();
      currentBlock = 'lingue';
      return;
    }
    if (/^Robustezza/i.test(line)) {
      pushCurrentBlock();
      currentBlock = 'robustezza nanica';
      return;
    }

    if (currentBlock === 'caratteristiche') {
      if (/Costituzione.*\d/.test(line)) {
        const match = line.match(/Costituzione.*?\b(\d+)/);
        if (match) output.incremento_caratteristiche["Costituzione"] = line;
      } else if (/Saggezza.*\d/.test(line)) {
        const match = line.match(/Saggezza.*?\b(\d+)/);
        if (match && sottorazza) sottorazza.incremento_caratteristiche["Saggezza"] = line;
      }
    } else {
      currentText += (currentText ? ' ' : '') + line;
    }
  });

  pushCurrentBlock();
  if (sottorazza) output.sottorazze.push(sottorazza);

  mostraEditor(output);
  aggiornaAnteprima();
}

function mostraEditor(dati) {
  const container = document.getElementById("outputEditor");
  container.innerHTML = '';
  window.currentData = dati;
  for (const key in dati) {
    const value = dati[key];
    if (typeof value === 'object' && !Array.isArray(value)) {
      const fieldset = document.createElement("fieldset");
      const legend = document.createElement("legend");
      legend.textContent = key;
      fieldset.appendChild(legend);
      for (const sub in value) {
        const label = document.createElement("label");
        label.textContent = sub;
        const input = document.createElement("input");
        input.name = `${key}.${sub}`;
        input.value = value[sub];
        input.style.width = '100%';
        input.addEventListener('input', aggiornaAnteprima);
        fieldset.appendChild(label);
        fieldset.appendChild(document.createElement("br"));
        fieldset.appendChild(input);
        fieldset.appendChild(document.createElement("br"));
      }
      container.appendChild(fieldset);
    } else if (Array.isArray(value)) {
      const label = document.createElement("label");
      label.textContent = key;
      const input = document.createElement("input");
      input.name = key;
      input.value = value.join(', ');
      input.style.width = '100%';
      input.addEventListener('input', aggiornaAnteprima);
      container.appendChild(label);
      container.appendChild(document.createElement("br"));
      container.appendChild(input);
      container.appendChild(document.createElement("br"));
    } else {
      const label = document.createElement("label");
      label.textContent = key;
      const input = document.createElement("input");
      input.name = key;
      input.value = value;
      input.style.width = '100%';
      input.addEventListener('input', aggiornaAnteprima);
      container.appendChild(label);
      container.appendChild(document.createElement("br"));
      container.appendChild(input);
      container.appendChild(document.createElement("br"));
    }
  }
  document.getElementById("salvaBtn").classList.remove("hidden");
}

function aggiornaAnteprima() {
  const inputs = document.querySelectorAll("#outputEditor input");
  const dati = window.currentData;
  inputs.forEach(input => {
    const [group, subkey] = input.name.split('.');
    if (subkey) {
      if (dati[group] && typeof dati[group] === 'object') {
        dati[group][subkey] = input.value;
      }
    } else if (Array.isArray(dati[input.name])) {
      dati[input.name] = input.value.split(',').map(v => v.trim());
    } else {
      dati[input.name] = input.value;
    }
  });
  const output = JSON.stringify(dati, null, 2);
  document.getElementById("anteprimaJson").textContent = output;
}

function salvaJsonFinale() {
  const blob = new Blob([document.getElementById("anteprimaJson").textContent], { type: "application/json" });
  const link = document.createElement("a");
  link.href = URL.createObjectURL(blob);
  link.download = "scheda_dnd.json";
  link.click();
}
