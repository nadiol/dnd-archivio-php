// Script gestione-dati.js aggiornato con parsing sottorazze, anteprima live e salvataggio

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
  let sottorazza = null;

  lines.forEach(line => {
    if (/^Nano\s*$/i.test(line)) {
      if (!output.nome) output.nome = line.trim();
      return;
    }
    if (/^Nano delle\s+/i.test(line)) {
      if (sottorazza) output.sottorazze.push(sottorazza);
      sottorazza = {
        nome: line.trim(),
        incremento_caratteristiche: {},
        tratti: {}
      };
      return;
    }

    if (/incremento dei punteggi/i.test(line)) currentBlock = 'caratteristiche';
    else if (/allineamento/i.test(line)) currentBlock = 'allineamento';
    else if (/età/i.test(line)) currentBlock = 'età';
    else if (/taglia/i.test(line)) currentBlock = 'taglia';
    else if (/velocità/i.test(line)) currentBlock = 'velocità';
    else if (/scurovisione/i.test(line)) currentBlock = 'scurovisione';
    else if (/resilienza/i.test(line)) currentBlock = 'resilienza';
    else if (/addestramento da combattimento/i.test(line)) currentBlock = 'addestramento';
    else if (/strumenti/i.test(line)) currentBlock = 'strumenti';
    else if (/minatore/i.test(line)) currentBlock = 'minatore';
    else if (/linguaggi/i.test(line)) currentBlock = 'lingue';
    else if (/robustezza/i.test(line)) currentBlock = 'robustezza';

    switch (currentBlock) {
      case 'caratteristiche':
        if (/Costituzione.*\d/.test(line)) {
          const match = line.match(/Costituzione.*?(\d+)/);
          if (match) output.incremento_caratteristiche["Costituzione"] = parseInt(match[1]);
        } else if (/Saggezza.*\d/.test(line)) {
          const match = line.match(/Saggezza.*?(\d+)/);
          if (match && sottorazza) sottorazza.incremento_caratteristiche["Saggezza"] = parseInt(match[1]);
        }
        break;
      case 'allineamento': output.allineamento = line; break;
      case 'età': output.età = line; break;
      case 'taglia': output.taglia = line; break;
      case 'velocità': output.velocità_m = 7.5; break;
      case 'scurovisione': aggiungiTrattoGenerico("Scurovisione", line, sottorazza, output); break;
      case 'resilienza': aggiungiTrattoGenerico("Resilienza nanica", line, sottorazza, output); break;
      case 'addestramento': aggiungiTrattoGenerico("Addestramento da combattimento", line, sottorazza, output); break;
      case 'strumenti': aggiungiTrattoGenerico("Competenza negli strumenti", line, sottorazza, output); break;
      case 'minatore': aggiungiTrattoGenerico("Esperto minatore", line, sottorazza, output); break;
      case 'robustezza': if (sottorazza) sottorazza.tratti["Robustezza nanica"] = line; break;
      case 'lingue':
        const lingue = line.match(/Comune|Nanico|Elfico|Draconico/gi);
        if (lingue) output.lingue.push(...lingue);
        break;
    }
  });

  if (sottorazza) output.sottorazze.push(sottorazza);
  mostraEditor(output);
  aggiornaAnteprima();
}

function aggiungiTrattoGenerico(nome, descrizione, sottorazza, output) {
  if (sottorazza) {
    sottorazza.tratti[nome] = descrizione;
  } else {
    output.tratti[nome] = descrizione;
  }
}

function mostraEditor(dati) {
  const container = document.getElementById("outputEditor");
  container.innerHTML = '';
  window.currentData = dati;
  for (const key in dati) {
    if (typeof dati[key] === 'object' && !Array.isArray(dati[key])) {
      const fieldset = document.createElement("fieldset");
      const legend = document.createElement("legend");
      legend.textContent = key;
      fieldset.appendChild(legend);
      for (const sub in dati[key]) {
        const label = document.createElement("label");
        label.textContent = sub;
        const input = document.createElement("input");
        input.name = `${key}.${sub}`;
        input.value = dati[key][sub];
        input.style.width = '100%';
        input.addEventListener('input', aggiornaAnteprima);
        fieldset.appendChild(label);
        fieldset.appendChild(document.createElement("br"));
        fieldset.appendChild(input);
        fieldset.appendChild(document.createElement("br"));
      }
      container.appendChild(fieldset);
    } else if (Array.isArray(dati[key])) {
      const label = document.createElement("label");
      label.textContent = key;
      const input = document.createElement("input");
      input.name = key;
      input.value = dati[key].join(', ');
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
      input.value = dati[key];
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
