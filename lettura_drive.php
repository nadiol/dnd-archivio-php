<?php 
include_once 'utils.php';
include 'includes/header.php'; 

// Carica l'indice dei file da Google Drive (in questo esempio, locale per test)
$index_url = "https://drive.google.com/uc?export=download&id=14QzWfVXu-MPlq0YxTCXJ-NV356hdXU1T"; // drive_index.json
$index_data = json_decode(file_get_contents($index_url), true);
$scelto = $_GET['file'] ?? '';
$dati = [];

// Costruisce la lista dei file disponibili (nome => id)
$map_file_id = [];
foreach ($index_data as $entry) {
    $map_file_id[$entry['nome']] = $entry['id'];
}

if ($scelto && isset($map_file_id[$scelto])) {
    $id = $map_file_id[$scelto];
    $url = "https://drive.google.com/uc?export=download&id=$id";
    $json = file_get_contents($url);
    if ($json) {
        $dati = json_decode($json, true);
    }
}
?>

<h1>Visualizza contenuti da Google Drive</h1>
<form method="get">
    <label for="file">Scegli un file da visualizzare:</label>
    <select name="file" id="file" onchange="this.form.submit()">
        <option value="">-- Seleziona --</option>
        <?php foreach ($map_file_id as $nome => $id): ?>
            <option value="<?= $nome ?>" <?= $scelto === $nome ? 'selected' : '' ?>>
                <?= htmlspecialchars($nome) ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>

<?php if (!empty($dati)): ?>
    <h2>Contenuto di <?= htmlspecialchars($scelto) ?></h2>
    <?php foreach ((array)$dati as $entry): ?>
        <div style="margin-bottom: 1rem;">
            <h3><?= ripristina_accenti($entry['nome'] ?? '---') ?></h3>
            <p><?= ripristina_accenti($entry['descrizione'] ?? '') ?></p>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
