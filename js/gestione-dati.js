
// Script gestione-dati.js migliorato con parsing completo per sottorazze e tratti dettagliati + layout ordinato

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

// Altre funzioni (mostraEditor, aggiornaAnteprima, salvaJsonFinale) escluse per brevità
