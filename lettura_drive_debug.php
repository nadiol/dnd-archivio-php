<?php 
include_once 'utils.php';
include 'includes/header.php'; 

$index_url = "https://drive.google.com/uc?export=download&id=14QzWfVXu-MPlq0YxTCXJ-NV356hdXU1T";
$json_raw = @file_get_contents($index_url);

$index_data = null;
if (!$json_raw) {
    echo "<p style='color:red;'>⚠️ Errore: impossibile scaricare il file index da Google Drive.</p>";
} else {
    $index_data = json_decode($json_raw, true);
    if (!$index_data) {
        echo "<p style='color:red;'>⚠️ Errore: il file scaricato non è un JSON valido.</p>";
        echo "<pre>" . htmlspecialchars($json_raw) . "</pre>";
    }
}

$scelto = $_GET['file'] ?? '';
$dati = [];
$map_file_id = [];

if ($index_data) {
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
}
?>

<h1 style="font-family: sans-serif;">Visualizza contenuti da Google Drive</h1>
<form method="get" style="margin-bottom: 20px;">
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
    <h2 style="font-family: sans-serif;">Contenuto di <?= htmlspecialchars($scelto) ?></h2>
    <div style="display: flex; flex-wrap: wrap; gap: 20px;">
    <?php foreach ((array)$dati as $entry): ?>
        <div style="border: 1px solid #ccc; padding: 15px; border-radius: 8px; width: 300px; background: #f9f9f9;">
            <h3><?= ripristina_accenti($entry['nome'] ?? '---') ?></h3>
            <p><?= ripristina_accenti($entry['descrizione'] ?? '') ?></p>
        </div>
    <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
