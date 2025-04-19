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
    echo "<p style='color:red;'>\u26a0\ufe0f Errore: impossibile scaricare il file index da Google Drive.</p>";
} else {
    $index_data = json_decode($json_raw, true);
    if (!$index_data) {
        echo "<p style='color:red;'>\u26a0\ufe0f Errore: il file scaricato non \u00e8 un JSON valido.</p>";
        echo "<pre>" . htmlspecialchars($json_raw) . "</pre>";
    }
}

$scelto = $_GET['file'] ?? '';
$tipoFiltro = $_GET['tipo'] ?? '';
$dati = null;
$map_file_id = [];

if ($index_data) {
    foreach ($index_data as $entry) {
        if (isset($entry['nome']) && isset($entry['id'])) {
            $map_file_id[$entry['nome']] = [
                'id' => $entry['id'],
                'tipo' => $entry['tipo'] ?? 'generico'
            ];
        }
    }

    if ($scelto && isset($map_file_id[$scelto])) {
        $json = scarica_da_drive($map_file_id[$scelto]['id']);
        if ($json) {
            $dati = json_decode($json, true);
        }
    }
}
?>

<h1 style="font-family: sans-serif;">Visualizza contenuti da Google Drive</h1>
<form method="get" style="margin-bottom: 20px;">
    <label for="tipo">Filtra per tipo:</label>
    <select name="tipo" onchange="this.form.submit()">
        <option value="">-- Tutti --</option>
        <?php foreach (array_unique(array_column($map_file_id, 'tipo')) as $tipo): ?>
            <option value="<?= $tipo ?>" <?= $tipoFiltro === $tipo ? 'selected' : '' ?>><?= ucfirst($tipo) ?></option>
        <?php endforeach; ?>
    </select>
</form>

<form method="get">
    <label for="file">Scegli un file da visualizzare:</label>
    <select name="file" id="file" onchange="this.form.submit()">
        <option value="">-- Seleziona --</option>
        <?php foreach ($map_file_id as $nome => $meta): ?>
            <?php if (!$tipoFiltro || $meta['tipo'] === $tipoFiltro): ?>
                <option value="<?= $nome ?>" <?= $scelto === $nome ? 'selected' : '' ?>><?= htmlspecialchars($nome) ?></option>
            <?php endif; ?>
        <?php endforeach; ?>
    </select>
</form>

<?php if (!empty($dati)): ?>
    <h2 style="font-family: sans-serif;">Contenuto di <?= htmlspecialchars($scelto) ?></h2>

    <input type="text" id="searchInput" onkeyup="filtraContenuto()" placeholder="🔍 Cerca nei dati..." style="margin-bottom: 10px; width: 300px; padding: 5px;">

    <div id="contenutoJSON">
    <?php 
        if (isset($dati['nome']) || isset($dati['tratti'])) {
            echo "<div class='box'>";
            echo "<h3>" . ripristina_accenti($dati['nome'] ?? '[nome non presente]') . "</h3>";
            echo "<p><strong>Edizione:</strong> " . ($dati['edizione'] ?? '-') . " | <strong>Fonte:</strong> " . ($dati['fonte'] ?? '-') . "</p>";

            if (!empty($dati['tratti'])) {
                echo "<h4>Tratti</h4><ul>";
                foreach ($dati['tratti'] as $k => $v) {
                    echo "<li><strong>" . ucfirst($k) . ":</strong> " . (is_array($v) ? json_encode($v, JSON_UNESCAPED_UNICODE) : ripristina_accenti($v)) . "</li>";
                }
                echo "</ul>";
            }

            if (!empty($dati['sottorazze'])) {
                echo "<h4>Sottorazze</h4><ul>";
                foreach ($dati['sottorazze'] as $sr) {
                    echo "<li><details><summary><strong>" . ripristina_accenti($sr['nome'] ?? 'Senza nome') . "</strong></summary><pre>" .
                        htmlspecialchars(json_encode($sr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) .
                        "</pre></details></li>";
                }
                echo "</ul>";
            }

            echo "</div>";
        } elseif (is_array($dati)) {
            foreach ($dati as $entry) {
                echo "<div class='box'>";
                echo "<h3>" . ripristina_accenti($entry['nome'] ?? '---') . "</h3>";
                echo "<p>" . ripristina_accenti($entry['descrizione'] ?? '') . "</p>";
                echo "<details><summary>📦 Vedi JSON</summary><pre>" .
                    htmlspecialchars(json_encode($entry, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) .
                    "</pre></details>";
                echo "</div>";
            }
        }
    ?>
    </div>
<?php endif; ?>

<style>
    .box {
        border: 1px solid #ccc;
        padding: 15px;
        border-radius: 8px;
        background: #f9f9f9;
        margin-bottom: 20px;
    }
</style>
<script>
function filtraContenuto() {
    var input = document.getElementById("searchInput").value.toLowerCase();
    var contenuti = document.querySelectorAll("#contenutoJSON .box");
    contenuti.forEach(function(box) {
        var text = box.innerText.toLowerCase();
        box.style.display = text.includes(input) ? "" : "none";
    });
}
</script>

<?php include 'includes/footer.php'; ?>
