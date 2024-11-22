<?php
/**
 * name: codi_helper
 * version: 1.0.0
 * author: codi
 * link: GitHub link
 */

 if (!function_exists('writeLog')) {
    // Функція для запису даних у лог-файл
    function writeLog($data, $filename = "exchange_log.txt")
    {
        // Визначаємо шлях до папки з логами
        $logsPath = $_SERVER['DOCUMENT_ROOT'] . '/codi/logs/';
        $fullPath = $logsPath . $filename;

        // Перевіряємо існування директорії
        if (!is_dir($logsPath)) {
            // Створюємо директорію з правами доступу 0777, якщо вона не існує
            mkdir($logsPath, 0777, true);
        }

        // Перевіряємо існування файла
        if (!file_exists($fullPath)) {
            // Створюємо файл, якщо він не існує
            $file = fopen($fullPath, 'w');
        } else {
            // Відкриваємо файл для додавання, якщо він вже існує
            $file = fopen($fullPath, 'a');
        }

        // Якщо $data є масивом, використовуємо print_r для отримання рядка
        if (is_array($data)) {
            $data = print_r($data, true);
        }

        // Записуємо дані у файл
        fwrite($file, $data . PHP_EOL);
        // Закриваємо файл
        fclose($file);
    }
}


if (!function_exists('log_WRITE')) {
    function log_WRITE()
    {
        // // Записываем данные из глобальных массивов
        if (isset_data($_GET)) writeLog("GET Data:\n" . print_r($_GET, true));
        if (isset_data($_POST)) writeLog("POST Data:\n" . print_r($_POST, true));
        if (isset_data($_FILES)) writeLog("FILES Data:\n" . print_r($_FILES, true));
        $inputData = file_get_contents('php://input');
        if (isset_data($inputData)) writeLog("php://input Data:\n" . $inputData);
        writeLog(getallheaders());
        writeLog("\n**********" . print_r("**********\n", true));
    }
}

if (!function_exists('dump')) {
    function dump(...$vars)
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
        $file = $backtrace[0]['file'] ?? 'невідомий файл';
        $line = $backtrace[0]['line'] ?? 'невідома лінія';

        echo '<div style="background-color: #f3f4f6; border-left: 5px solid #9ca3af; padding: 20px; margin: 20px 0; font-family: Consolas, Monaco, monospace; font-size: 14px;">';
        echo '<pre style="margin: 0; padding: 10px; background-color: #1e293b; color: #e5e7eb; border-radius: 5px;">';
        echo "Викликано з файлу: " . $file . " на рядку: " . $line . "\n";

        foreach ($vars as $var) {
            var_dump($var);
            echo "\n";
        }
        echo '</pre>';
        echo '</div>';
    }
}

if (!function_exists('dd')) {
    function dd(...$vars)
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
        $file = $backtrace[0]['file'] ?? 'невідомий файл';
        $line = $backtrace[0]['line'] ?? 'невідома лінія';

        echo '<div style="background-color: #f3f4f6; border-left: 5px solid #9ca3af; padding: 20px; margin: 20px 0; font-family: Consolas, Monaco, monospace; font-size: 14px;">';
        echo '<pre style="margin: 0; padding: 10px; background-color: #1e293b; color: #e5e7eb; border-radius: 5px;">';
        echo "Викликано з файлу: " . $file . " на рядку: " . $line . "\n";

        foreach ($vars as $var) {
            var_dump($var);
            echo "\n";
        }

        echo '</pre>';
        echo '</div>';
        die();
    }
}



if (!function_exists('show_error')) {
    function show_error()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    }
}

if (!function_exists("read_csv")) {
    function read_csv($fileContent)
    {
        if (($handle = fopen($fileContent, "r")) !== FALSE) {
            $headers = fgetcsv($handle, 1000, ","); // Читаємо заголовки

            $allData = []; // Масив для зберігання всіх даних
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if ($data === $headers) {
                    continue; // Пропускаємо заголовки
                }

                $allData[] = array_combine($headers, $data); // Створюємо асоціативний масив
            }
            fclose($handle);
        }
        return $allData;
    }
}

// Перевірка, чи функція mysqli вже існує
if (!function_exists("mysqli")) {
    // Оголошення функції mysqli
    function mysqli($host, $user, $password, $data) {
        // Повернення результату виклику функції mysqli_connect
        return mysqli_connect(
            $host, $user, $password, $data
        );        
    }
}


// Перевірка, чи функція query вже існує
if(!function_exists("query")){
    // Оголошення функції query
    function query($mysql, $sql){
        // Виконання SQL запиту
        $result = mysqli_query($mysql, $sql);
        // Виведення результату
        return $result;
    }
}

// Перевірка, чи функція fetch вже існує
if(!function_exists("fetch")){
    // Оголошення функції fetch
    function fetch($mysql, $sql){
        // Виконання запиту і отримання результату
        $result = query($mysql, $sql);
        // Перевірка, чи є результат
        if ($result && $result->num_rows > 0) {
            // Повернення асоціативного масиву одного рядка
            return $result->fetch_assoc();
        } else {
            // Повернення false у випадку відсутності результату
            return false;
        }
    }
}

// Перевірка, чи функція fetch_row вже існує
if(!function_exists("fetch_row")){
    // Оголошення функції fetch_row
    function fetch_row($mysql, $sql){
        // Виконання запиту і отримання результату
        $result = query($mysql, $sql);
        // Перевірка, чи є результат
        if ($result && $result->num_rows > 0) {
            // Ініціалізація порожнього масиву для зберігання всіх рядків
            $rows = [];
            // Перебір результатів
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            // Повернення масиву всіх рядків
            return $rows;
        } else {
            // Повернення false у випадку відсутності результату
            return false;
        }
    }
}

