
// Script gestione-dati.js con parsing AI (via API OpenAI opzionale in futuro)

function analizzaTesto() {
  const tipo = document.getElementById("tipoDato").value;
  const input = document.getElementById("inputGenerico").value;

  // 🚧 Punto di integrazione futura con AI
  if (document.getElementById("usaAI")?.checked) {
    alert("Parsing AI non ancora attivo in questa versione. Verrà integrato prossimamente.");
    return;
  }

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

    switch (currentBlock) {
      case 'allineamento': output.allineamento = text; break;
      case 'età': output.età = text; break;
      case 'taglia': output.taglia = text; break;
      case 'velocità': output.velocità_m = '7.5'; break;
      case 'lingue':
        const lingue = text.match(/Comune|Nanico|Elfico|Draconico/gi);
        if (lingue) output.lingue = [...new Set([...output.lingue, ...lingue])];
        break;
      case 'robustezza':
        if (sottorazza) sottorazza.tratti['Robustezza nanica'] = text;
        break;
      default:
        if (currentBlock in output.incremento_caratteristiche) {
          output.incremento_caratteristiche[currentBlock] = text;
        } else {
          target[currentBlock.charAt(0).toUpperCase() + currentBlock.slice(1)] = text;
        }
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

    if (/^Incremento dei punteggi/i.test(line)) { flushBuffer(); currentBlock = 'incremento'; return; }
    if (/^Allineamento/i.test(line)) { flushBuffer(); currentBlock = 'allineamento'; return; }
    if (/^Età/i.test(line)) { flushBuffer(); currentBlock = 'età'; return; }
    if (/^Taglia/i.test(line)) { flushBuffer(); currentBlock = 'taglia'; return; }
    if (/^Velocità/i.test(line)) { flushBuffer(); currentBlock = 'velocità'; return; }
    if (/^Scurovisione/i.test(line)) { flushBuffer(); currentBlock = 'scurovisione'; return; }
    if (/^Resilienza/i.test(line)) { flushBuffer(); currentBlock = 'resilienza nanica'; return; }
    if (/^Addestramento da combattimento/i.test(line)) { flushBuffer(); currentBlock = 'addestramento da combattimento'; return; }
    if (/^Competenza negli strumenti/i.test(line)) { flushBuffer(); currentBlock = 'competenza negli strumenti'; return; }
    if (/^Esperto minatore/i.test(line)) { flushBuffer(); currentBlock = 'esperto minatore'; return; }
    if (/^Linguaggi/i.test(line)) { flushBuffer(); currentBlock = 'lingue'; return; }
    if (/^Robustezza/i.test(line)) { flushBuffer(); currentBlock = 'robustezza'; return; }

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

// ... resta invariato tutto il resto del file ...
