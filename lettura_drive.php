<?php 
include_once 'utils.php';
include 'includes/header.php'; 

function scarica_da_drive($id) {
    $url = "https://drive.google.com/uc?export=download&id=" . $id;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

$json_raw = scarica_da_drive("1_FqDS1q3XmOHeJf46TtqGWgGq69IQik2");

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
$dati = null;
$map_file_id = [];

if ($index_data) {
    foreach ($index_data as $entry) {
        $map_file_id[$entry['file']] = $entry['file'];
    }

    if ($scelto && isset($map_file_id[$scelto])) {
        $json = scarica_da_drive($map_file_id[$scelto]);
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
    <div style="border: 1px solid #ccc; padding: 15px; background: #f9f9f9; border-radius: 8px;">

        <h3><?= ripristina_accenti($dati['nome'] ?? '[nome non presente]') ?></h3>
        <p><strong>Edizione:</strong> <?= $dati['edizione'] ?? '-' ?> | <strong>Fonte:</strong> <?= $dati['fonte'] ?? '-' ?></p>

        <?php if (!empty($dati['tratti'])): ?>
            <h4>Tratti</h4>
            <ul>
                <?php foreach ($dati['tratti'] as $k => $v): ?>
                    <li><strong><?= ucfirst($k) ?>:</strong> <?= is_array($v) ? json_encode($v, JSON_UNESCAPED_UNICODE) : ripristina_accenti($v) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if (!empty($dati['sottorazze'])): ?>
            <h4>Sottorazze</h4>
            <ul>
                <?php foreach ($dati['sottorazze'] as $sr): ?>
                    <li><strong><?= ripristina_accenti($sr['nome'] ?? 'Senza nome') ?>:</strong>
                        <pre><?= htmlspecialchars(json_encode($sr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <details>
        <summary>📦 JSON completo</summary>
        <pre><?= htmlspecialchars(json_encode($dati, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre>
    </details>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
