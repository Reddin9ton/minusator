<?php
$filename = "minus.txt";
$content = file_get_contents($filename);
// Обрезаем BOM и лишние пробелы, если они есть
$content = trim($content, "\xEF\xBB\xBF");
$minusArray = array_filter(array_map('trim', explode("\n", $content)));

// То же самое для $_POST["keys"]
$keys = array_filter(array_map('trim', explode("\n", $_POST["keys"])));

function minusator(array $keys, array $minusArray): array {
    $filteredKeys = [];
    // Считываем существующие ключи из файла
    $existingKeysContent = file_exists("keys.csv") ? file_get_contents("keys.csv") : '';
    $existingKeys = array_filter(array_map('trim', explode("\n", $existingKeysContent)));

    foreach ($keys as $key) {
        $exclude = false;
        foreach ($minusArray as $minusItem) {
            if ($minusItem !== '' && mb_stripos($key, $minusItem) !== false) {
                $exclude = true;
                break;
            }
        }
        // Проверяем, не существует ли уже такой ключ и не был ли он исключен
        if (!$exclude && !in_array($key, $existingKeys)) {
            $filteredKeys[] = $key;
            // Добавляем ключ к существующим, чтобы предотвратить повторную запись
            $existingKeys[] = $key;
        }
    }

    // Запись отфильтрованных ключей в файл, если они есть
    if (!empty($filteredKeys)) {
        $keysFile = fopen("keys.csv", "a");
        if (!empty($existingKeysContent) && substr($existingKeysContent, -1) !== "\n") {
            fwrite($keysFile, "\n");
        };
        foreach ($filteredKeys as $filteredKey) {
            fwrite($keysFile, $filteredKey . "\n"); // Записываем каждый ключ на новой строке
        }
        fclose($keysFile);
    }

    return $filteredKeys;
}

$filteredKeys = minusator($keys, $minusArray); // Вызываем функцию с нужными параметрами

// Вывод результата
echo "<pre>";
print_r($filteredKeys);
echo "</pre>";