
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

    // Parsing caratteristiche
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

// [Funzioni mostraEditor, aggiornaAnteprima, salvaJsonFinale identiche]
