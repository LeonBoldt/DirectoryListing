<?php
// Der Basis-Verzeichnis-Pfad
$baseDirectory = './';

// Dateiname der Log-Datei
$logFile = 'changelog.json';

// Aktuelles Verzeichnis basierend auf URL-Parametern, damit Navigation funktioniert
$currentDir = isset($_GET['dir']) ? $_GET['dir'] : '';
$directoryPath = realpath($baseDirectory . '/' . $currentDir);

// Verzeichnis prüfen (um Directory Traversal zu verhindern)
if (strpos($directoryPath, realpath($baseDirectory)) !== 0) {
    die('Ungültiger Verzeichnispfad!');
}

// Suchbegriff aus dem Formular (falls vorhanden)
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

// Funktion zum Durchsuchen des Verzeichnisses ohne Suchfilter (für den Changelog)
function listFilesAndDirsWithoutFilter($directoryPath) {
    $items = array();
    if ($handle = opendir($directoryPath)) {
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                $itemPath = $directoryPath . '/' . $entry;
                $items[] = array(
                    'name' => $entry,
                    'path' => $itemPath,
                    'is_dir' => is_dir($itemPath)
                );
            }
        }
        closedir($handle);
    }
    return $items;
}

// Funktion zum Durchsuchen des Verzeichnisses mit Suchfilter (für die Dateianzeige)
function listFilesAndDirs($directoryPath, $searchQuery) {
    $items = array();
    if ($handle = opendir($directoryPath)) {
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                $itemPath = $directoryPath . '/' . $entry;
                if ($searchQuery === '' || stripos($entry, $searchQuery) !== false) {
                    $items[] = array(
                        'name' => $entry,
                        'path' => $itemPath,
                        'is_dir' => is_dir($itemPath)
                    );
                }
            }
        }
        closedir($handle);
    }
    return $items;
}

// Funktion zum Laden des Changelogs
function loadChangelog($logFile) {
    if (file_exists($logFile)) {
        $logData = file_get_contents($logFile);
        return json_decode($logData, true);
    }
    return [];
}

// Funktion zum Speichern des Changelogs
function saveChangelog($logFile, $changelog) {
    file_put_contents($logFile, json_encode($changelog, JSON_PRETTY_PRINT));
}

// Dateien und Ordner ohne Filter für den Changelog auflisten
$allItems = listFilesAndDirsWithoutFilter($directoryPath);

// Dateien und Ordner mit Suchfilter für die Anzeige auflisten
$items = listFilesAndDirs($directoryPath, $searchQuery);

// Aktuellen Stand des Verzeichnisses
$currentFiles = array_map(function($item) {
    return $item['name'];
}, $allItems);

// Changelog laden
$changelog = loadChangelog($logFile);

// Wenn die Changelog-Datei leer ist, speichern wir den aktuellen Zustand
if (empty($changelog)) {
    $changelog['files'] = $currentFiles;
    $changelog['log'] = [];
    saveChangelog($logFile, $changelog);
}

// Vergleiche alte und aktuelle Dateien
$oldFiles = isset($changelog['files']) ? $changelog['files'] : [];
$newFiles = array_diff($currentFiles, $oldFiles);
$deletedFiles = array_diff($oldFiles, $currentFiles);

// Neue Dateien im Changelog hinzufügen
if (!empty($newFiles)) {
    foreach ($newFiles as $newFile) {
        $changelog['log'][] = [
            'type' => 'added',
            'file' => $newFile,
            'time' => date('Y-m-d H:i:s')
        ];
    }
}

// Gelöschte Dateien im Changelog hinzufügen
if (!empty($deletedFiles)) {
    foreach ($deletedFiles as $deletedFile) {
        $changelog['log'][] = [
            'type' => 'deleted',
            'file' => $deletedFile,
            'time' => date('Y-m-d H:i:s')
        ];
    }
}

// Changelog speichern und den aktuellen Zustand der Dateien festhalten
$changelog['files'] = $currentFiles;
saveChangelog($logFile, $changelog);
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Directory Listing</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom Styles -->
    <style>
        body {
            padding: 40px;
            background-color: #f8f9fa;
        }
        header {
            text-align: center;
            margin-bottom: 30px;
        }
        img {
            width: 150px;
            margin-bottom: 20px;
        }
        .file-list {
            margin-top: 20px;
        }
        .file-list .file-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            margin-bottom: 10px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .file-list .file-item .btn {
            margin-left: 10px;
        }
        .folder-icon {
            margin-right: 10px;
        }
        .changelog {
            margin-top: 20px;
        }
        .changelog .log-item {
            padding: 10px;
            background-color: #fff;
            margin-bottom: 10px;
            border-radius: 5px;
            border-left: 4px solid #28a745;
        }
        .changelog .log-item.deleted {
            border-left: 4px solid #dc3545;
        }
    </style>
</head>
<body>

    <header>
        <img src="logo.svg" alt="Logo">
        <h1 class="text-primary">Directory Listing</h1>
    </header>

    <!-- Changelog -->
    <div class="container changelog">
        <h2 class="text-success">Changelog (letzte 5 Änderungen)</h2>
        <?php 
        // Zeige nur die letzten 5 Änderungen an
        $latestLogItems = array_slice(array_reverse($changelog['log']), 0, 5);
        if (!empty($latestLogItems)): ?>
            <?php foreach ($latestLogItems as $logItem): ?>
                <div class="log-item <?php echo $logItem['type'] == 'deleted' ? 'deleted' : 'added'; ?>">
                    <strong><?php echo ucfirst($logItem['type']); ?>:</strong> <?php echo htmlspecialchars($logItem['file']); ?>
                    <br>
                    <small><?php echo htmlspecialchars($logItem['time']); ?></small>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Keine Änderungen protokolliert.</p>
        <?php endif; ?>
    </div>

    <div class="container">
        <!-- Suchformular -->
        <form method="get" action="" class="input-group mb-3">
            <input type="text" name="search" class="form-control" placeholder="Datei oder Ordner suchen..." value="<?php echo htmlspecialchars($searchQuery); ?>">
            <input type="hidden" name="dir" value="<?php echo htmlspecialchars($currentDir); ?>">
            <button class="btn btn-primary" type="submit">Suchen</button>
        </form>

        <!-- Verzeichnis-Navigation -->
        <?php if ($currentDir): ?>
            <p><a href="?dir=<?php echo urlencode(dirname($currentDir)); ?>" class="btn btn-secondary">Zurück</a></p>
        <?php endif; ?>

        <ul class="file-list">
            <?php if (count($items) > 0): ?>
                <?php foreach ($items as $item): ?>
                    <li class="file-item">
                        <?php if ($item['is_dir']): ?>
                            <!-- Anzeige für Ordner -->
                            <span>
                                <i class="bi bi-folder-fill folder-icon"></i>
                                <a href="?dir=<?php echo urlencode($currentDir . '/' . $item['name']); ?>"><?php echo htmlspecialchars($item['name']); ?></a>
                            </span>
                        <?php else: ?>
                            <!-- Anzeige für Dateien -->
                            <span><?php echo htmlspecialchars($item['name']); ?></span>
                            <div>
                                <!-- Button zum Herunterladen -->
                                <a href="<?php echo $currentDir . '/' . $item['name']; ?>" class="btn btn-success" download>Herunterladen</a>
                                <!-- Button zum Kopieren des Direktlinks -->
                                <button class="btn btn-info" onclick="copyToClipboard('<?php echo $currentDir . '/' . $item['name']; ?>')">Direktlink kopieren</button>
                            </div>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li class="alert alert-warning">Keine Dateien oder Ordner gefunden.</li>
            <?php endif; ?>
        </ul>
    </div>

    <!-- Bootstrap JS and dependencies (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Funktion zum Kopieren des Direktlinks in die Zwischenablage
        function copyToClipboard(text) {
            var tempInput = document.createElement("input");
            tempInput.style.position = "absolute";
            tempInput.style.left = "-9999px";
            tempInput.value = window.location.origin + '/' + text;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand("copy");
            document.body.removeChild(tempInput);
            alert("Direktlink kopiert: " + tempInput.value);
        }
    </script>

</body>
</html>
