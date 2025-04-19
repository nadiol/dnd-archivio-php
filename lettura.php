<?php 
include_once 'utils.php';
include 'includes/header.php'; 

$cartella_dati = 'dati/';
$opzioni = array_filter(scandir($cartella_dati), function($f) {
    return pathinfo($f, PATHINFO_EXTENSION) === 'json';
});

$scelto = $_GET['file'] ?? '';
$dati = [];

if ($scelto && file_exists($cartella_dati . $scelto)) {
    $dati = json_decode(file_get_contents($cartella_dati . $scelto), true);
}
?>

<h1>Visualizza Dati</h1>
<form method="get">
    <label for="file">Scegli una categoria:</label>
    <select name="file" id="file" onchange="this.form.submit()">
        <option value="">-- Seleziona --</option>
        <?php foreach ($opzioni as $opzione): ?>
            <option value="<?= $opzione ?>" <?= $scelto === $opzione ? 'selected' : '' ?>>
                <?= ucfirst(pathinfo($opzione, PATHINFO_FILENAME)) ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>

<?php if (!empty($dati)): ?>
    <h2>Dati da <?= htmlspecialchars($scelto) ?></h2>
    <?php foreach ($dati as $entry): ?>
        <div style="margin-bottom: 1rem;">
            <h3><?= ripristina_accenti($entry['nome']) ?></h3>
            <p><?= ripristina_accenti($entry['descrizione']) ?></p>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
