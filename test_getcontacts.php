<?php
// Подключаем файлы Bitrix
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include.php';

CModule::IncludeModule('iblock');

// Подключаем модуль CRM
if (!CModule::IncludeModule('crm')) {
    die('Модуль CRM не найден');
}

$arSelect = ['ID', 'NAME', 'LAST_NAME', 'EMAIL', 'PHONE', 'COMPANY_TITLE'];
$arFilter = ['CHECK_PERMISSIONS' => 'Y'];

// Запрос на получение контактов
$res = CCrmContact::GetListEx(
    ['NAME' => 'ASC'],  // Сортировка
    $arFilter,
    false,
    false,
    $arSelect
);

// Проверка, что запрос вернул данные
if ($res === false)
{
    die('Ошибка при получении данных из CRM.');
}

// Заголовок для CSV файла
$filename = 'contacts_export_' . date('Y-m-d_H-i-s') . '.csv';

// Отправляем заголовки для браузера
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename="' . $filename . '"');

// Открываем поток для записи в CSV
$output = fopen('php://output', 'w');

// Записываем заголовки в CSV файл
fputcsv($output, ['ID', 'Имя', 'Фамилия', 'Email', 'Телефон', 'Компания']);

// Наполнение данными из CRM
while ($contact = $res->Fetch())
{
    fputcsv($output, [
        $contact['ID'],
        $contact['NAME'],
        $contact['LAST_NAME'],
        $contact['EMAIL'],
        $contact['PHONE'],
        $contact['COMPANY_TITLE']
    ]);
}

// Закрытие потока
fclose($output);

exit;



