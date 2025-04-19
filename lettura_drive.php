<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Visualizza contenuti da Google Drive</title>
  <link rel="stylesheet" href="css/lettura.css">
</head>
<body>
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

<script src="js/lettura_drive.js"></script>

<?php include 'includes/footer.php'; ?>
</body>
</html>
