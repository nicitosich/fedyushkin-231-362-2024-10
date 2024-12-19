<?php
// Получаем введенный текст из формы
$text = isset($_POST['textInput']) ? $_POST['textInput'] : '';

// Функция для подсчета количества символов
function countSymbols($text) {
    return mb_strlen($text, 'UTF-8');
}

// Функция для подсчета количества букв
function countLetters($text) {
    return preg_match_all('/[a-zA-Zа-яА-ЯёЁ]/u', $text);
}

// Функция для подсчета количества строчных и заглавных букв
function countCaseLetters($text) {
    $lowercase = preg_match_all('/[a-zа-яё]/u', $text);
    $uppercase = preg_match_all('/[A-ZА-ЯЁ]/u', $text);
    return ['lower' => $lowercase, 'upper' => $uppercase];
}

// Функция для подсчета количества знаков препинания
function countPunctuation($text) {
    return preg_match_all('/[.,!?;:()-]/u', $text);
}

// Функция для подсчета количества цифр
function countDigits($text) {
    return preg_match_all('/\d/u', $text);
}

// Функция для подсчета количества слов (русские и английские)
function countWords($text) {
    preg_match_all('/[a-zA-Zа-яА-ЯёЁ]+/u', $text, $matches);
    return count($matches[0]);  
}

// Функция для подсчета вхождений каждого символа
function countCharacterOccurrences($text) {
    $count = [];
    $text = mb_strtolower($text, 'UTF-8');
    $characters = mb_str_split($text);
    foreach ($characters as $char) {
        if (!isset($count[$char])) {
            $count[$char] = 0;
        }
        $count[$char]++;
    }
    ksort($count);
    return $count;
}

// Функция для подсчета вхождений каждого слова
function countWordOccurrences($text) {
    $text = mb_strtolower($text, 'UTF-8');
    preg_match_all('/[a-zA-Zа-яА-ЯёЁ]+/u', $text, $matches);
    $words = $matches[0];
    $count = array_count_values($words);
    ksort($count);
    return $count;
}

if (empty($text)) {
    $textMessage = "Нет текста для анализа";
    $analysisResults = null;
} else {
    $textMessage = '<em style="color: blue;">' . htmlspecialchars($text) . '</em>';

    $symbolsCount = countSymbols($text);
    $lettersCount = countLetters($text);
    $caseLettersCount = countCaseLetters($text);
    $punctuationCount = countPunctuation($text);
    $digitsCount = countDigits($text);
    $wordsCount = countWords($text);
    $characterOccurrences = countCharacterOccurrences($text);
    $wordOccurrences = countWordOccurrences($text);

    $analysisResults = [
        'symbolsCount' => $symbolsCount,
        'lettersCount' => $lettersCount,
        'caseLettersCount' => $caseLettersCount,
        'punctuationCount' => $punctuationCount,
        'digitsCount' => $digitsCount,
        'wordsCount' => $wordsCount,
        'characterOccurrences' => $characterOccurrences,
        'wordOccurrences' => $wordOccurrences
    ];
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Результаты анализа</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>Результаты анализа текста</h1>

    <p>Исходный текст:</p>
    <p><?= $textMessage ?></p>

    <?php if ($analysisResults): ?>
        <h3>Информация о тексте:</h3>
        <table>
            <tr>
                <th>Параметр</th>
                <th>Значение</th>
            </tr>
            <tr><td>Количество символов</td><td><?= $analysisResults['symbolsCount'] ?></td></tr>
            <tr><td>Количество букв</td><td><?= $analysisResults['lettersCount'] ?></td></tr>
            <tr><td>Количество строчных букв</td><td><?= $analysisResults['caseLettersCount']['lower'] ?></td></tr>
            <tr><td>Количество заглавных букв</td><td><?= $analysisResults['caseLettersCount']['upper'] ?></td></tr>
            <tr><td>Количество знаков препинания</td><td><?= $analysisResults['punctuationCount'] ?></td></tr>
            <tr><td>Количество цифр</td><td><?= $analysisResults['digitsCount'] ?></td></tr>
            <tr><td>Количество слов</td><td><?= $analysisResults['wordsCount'] ?></td></tr>
        </table>

        <h3>Частота вхождения символов:</h3>
        <table>
            <tr>
                <th>Символ</th>
                <th>Количество</th>
            </tr>
            <?php foreach ($analysisResults['characterOccurrences'] as $char => $count): ?>
                <tr>
                    <td><?= htmlspecialchars($char) ?></td>
                    <td><?= $count ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <h3>Частота вхождения слов:</h3>
        <table>
            <tr>
                <th>Слово</th>
                <th>Количество</th>
            </tr>
            <?php foreach ($analysisResults['wordOccurrences'] as $word => $count): ?>
                <tr>
                    <td><?= htmlspecialchars($word) ?></td>
                    <td><?= $count ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <br>
    <a href="index.html"
