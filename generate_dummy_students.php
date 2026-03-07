<?php
require_once __DIR__ . '/vendor/autoload.php';

$firstNamesMale = [
    'Juan', 'Jose', 'Carlo', 'Miguel', 'Angelo', 'Rafael', 'Marco', 'Luis', 'Christian', 'Francis',
    'Gabriel', 'Aaron', 'Mark', 'John', 'Paul', 'James', 'Kevin', 'Ryan', 'Daniel', 'Nathan',
    'Adrian', 'Andrei', 'Jared', 'Darren', 'Patrick', 'Dominic', 'Felix', 'Ivan', 'Leon', 'Manuel',
    'Jerome', 'Renz', 'Aldrin', 'Arvin', 'Brent', 'Cedric', 'Dennis', 'Edgar', 'Freddie', 'Gilbert',
    'Harold', 'Ian', 'Jason', 'Kenneth', 'Lance', 'Mario', 'Neil', 'Oscar', 'Perry', 'Quentin',
    'Ronnie', 'Samuel', 'Tristan', 'Ulric', 'Victor', 'Warren', 'Xavier', 'Yvan', 'Zachary', 'Allan',
    'Benedict', 'Crisanto', 'Darwin', 'Edmund', 'Ferdinand', 'Gregorio', 'Hernando', 'Ignacio', 'Jovito', 'Karl',
];

$firstNamesFemale = [
    'Maria', 'Ana', 'Sophia', 'Isabella', 'Gabriela', 'Camille', 'Jasmine', 'Reina', 'Liza', 'Patricia',
    'Andrea', 'Bianca', 'Carla', 'Diana', 'Elena', 'Faith', 'Grace', 'Hannah', 'Irene', 'Julia',
    'Karen', 'Lara', 'Mia', 'Nina', 'Olivia', 'Pamela', 'Queenie', 'Rose', 'Sandra', 'Trina',
    'Ursula', 'Vanessa', 'Wendy', 'Xyza', 'Yvonne', 'Zara', 'Abigail', 'Beatrice', 'Cristina', 'Danielle',
    'Elaine', 'Fiona', 'Geraldine', 'Hazel', 'Ivy', 'Jenny', 'Katherine', 'Lorraine', 'Michelle', 'Natalie',
    'Odette', 'Priscilla', 'Rachel', 'Sheila', 'Theresa', 'Uma', 'Valerie', 'Wilma', 'Xenia', 'Yolanda',
    'Angelica', 'Bernadette', 'Corazon', 'Dalisay', 'Esmeralda', 'Florinda', 'Glenda', 'Herminia', 'Imelda', 'Josefina',
];

$lastNames = [
    'Santos', 'Reyes', 'Cruz', 'Bautista', 'Ocampo', 'Garcia', 'Mendoza', 'Torres', 'Castillo', 'Flores',
    'Ramos', 'Gonzales', 'Aquino', 'Diaz', 'Lopez', 'Morales', 'Hernandez', 'Rivera', 'Villanueva', 'Perez',
    'Dela Cruz', 'De Guzman', 'Fernandez', 'Pascual', 'Santiago', 'Soriano', 'Mercado', 'Navarro', 'Tolentino', 'Aguilar',
    'Abad', 'Acosta', 'Alvarez', 'Andrade', 'Arcilla', 'Arellano', 'Arias', 'Arroyo', 'Asuncion', 'Atienza',
    'Baluyot', 'Barrera', 'Batungbakal', 'Baun', 'Belen', 'Bernardo', 'Buenaventura', 'Bueno', 'Bustamante', 'Cabanilla',
    'Cabrera', 'Caguioa', 'Calanog', 'Camacho', 'Campos', 'Capistrano', 'Cariño', 'Casimiro', 'Cayabyab', 'Celis',
    'Cinco', 'Clemente', 'Coloma', 'Concepcion', 'Constantino', 'Contreras', 'Corpuz', 'Cortez', 'Cristobal', 'Cueto',
    'Cunanan', 'Datu', 'David', 'Dayrit', 'De Leon', 'De Villa', 'Del Rosario', 'Delgado', 'Dimaculangan', 'Domingo',
    'Duenas', 'Dumaguit', 'Dumlao', 'Duque', 'Dy', 'Enriquez', 'Escudero', 'Espinosa', 'Estrada', 'Evangelista',
    'Faustino', 'Felipe', 'Ferreras', 'Figueroa', 'Francisco', 'Fuentebella', 'Galang', 'Galvez', 'Gamboa', 'Garces',
];

$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$cols = ['A', 'B', 'C', 'D', 'E'];

// Header row
$headers = ['First Name', 'Last Name', 'LRN', 'Gender', 'Birth Date'];
foreach ($headers as $i => $header) {
    $sheet->setCellValue($cols[$i] . '1', $header);
}
$sheet->getStyle('A1:E1')->getFont()->setBold(true);

// Format LRN and Birth Date columns as Text
$sheet->getStyle('C')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
$sheet->getStyle('E')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

$usedLrns = [];
$row = 2;
$count = 0;

while ($count < 1000) {
    $isMale  = rand(0, 1) === 1;
    $gender  = $isMale ? 'Male' : 'Female';
    $fname   = $isMale
        ? $firstNamesMale[array_rand($firstNamesMale)]
        : $firstNamesFemale[array_rand($firstNamesFemale)];
    $lname   = $lastNames[array_rand($lastNames)];

    // Generate unique 12-digit LRN
    do {
        $lrn = sprintf('%012d', rand(100000000000, 999999999999));
    } while (isset($usedLrns[$lrn]));
    $usedLrns[$lrn] = true;

    // Birth date: elementary students roughly 6–12 years old (born 2013–2019)
    $year  = rand(2013, 2019);
    $month = rand(1, 12);
    $day   = rand(1, cal_days_in_month(CAL_GREGORIAN, $month, $year));
    $bdate = sprintf('%04d-%02d-%02d', $year, $month, $day);

    $sheet->setCellValue('A' . $row, $fname);
    $sheet->setCellValue('B' . $row, $lname);
    $sheet->setCellValue('C' . $row, $lrn);
    $sheet->setCellValue('D' . $row, $gender);
    $sheet->setCellValue('E' . $row, $bdate);

    $row++;
    $count++;
}

// Auto-fit columns
foreach (range('A', 'E') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

$outputPath = __DIR__ . '/dummy_students.xlsx';
$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save($outputPath);

echo "Done! Generated 1000 students -> {$outputPath}\n";
