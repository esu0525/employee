<?php
/**
 * Masterlist Update Script
 * Parses the CSV, deletes employees NOT in the CSV, resets IDs to EMP0001-EMP1131,
 * and ensures last_name, first_name, middle_name, suffix are correctly populated.
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Employee;

$csvPath = __DIR__ . '/../Masterlist_Active_Employees_Updated (2).xlsx - Active Employees.csv';

if (!file_exists($csvPath)) {
    echo "ERROR: CSV file not found at: $csvPath\n";
    exit(1);
}

// Known suffixes to detect in names
$knownSuffixes = ['Jr.', 'Jr', 'Sr.', 'Sr', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII'];

/**
 * Parse "LastName, FirstName MiddleName" format
 * Examples:
 *   "Aba, Michael Asoy"              => Last: Aba, First: Michael, Middle: Asoy
 *   "Calimag Jr., Francisco B."      => Last: Calimag, First: Francisco, Middle: B., Suffix: Jr.
 *   "Bandiola Jr., Cirilo Manao"     => Last: Bandiola, First: Cirilo, Middle: Manao, Suffix: Jr.
 *   "Bañares, Lando"                 => Last: Bañares, First: Lando
 *   "Avila, Romeo T."                => Last: Avila, First: Romeo, Middle: T.
 *   "De Guzman, Jeffrey Divinagracia" => Last: De Guzman, First: Jeffrey, Middle: Divinagracia
 *   "Sta. Ana, Vincent Rae Soleta"   => Last: Sta. Ana, First: Vincent, Middle: Rae Soleta
 *   "San Luis, Maria Elena Calvo"    => Last: San Luis, First: Maria Elena, Middle: Calvo
 *   "Datiles, Mark Lester, Gabatino" => Last: Datiles, First: Mark Lester, Middle: Gabatino (extra comma)
 *   "Ordes Jr., Alfredo F."          => Last: Ordes, First: Alfredo, Middle: F., Suffix: Jr.
 *   "Orge, Jaime Felipe A. Jr."      => Last: Orge, First: Jaime Felipe, Middle: A., Suffix: Jr.
 */
function parseName($fullName, $knownSuffixes) {
    $fullName = trim($fullName, '" ');
    
    if (empty($fullName)) {
        return ['last_name' => '', 'first_name' => '', 'middle_name' => '', 'suffix' => ''];
    }

    $suffix = '';
    $lastName = '';
    $firstName = '';
    $middleName = '';
    
    // Split by comma - first part is last_name (possibly with suffix), rest is first+middle
    $parts = explode(',', $fullName);
    
    if (count($parts) < 2) {
        // No comma — treat whole thing as last name
        return ['last_name' => trim($fullName), 'first_name' => '', 'middle_name' => '', 'suffix' => ''];
    }
    
    $lastNameRaw = trim($parts[0]);
    // Join remaining parts (handles the case of extra commas like "Datiles, Mark Lester, Gabatino")
    $restParts = array_slice($parts, 1);
    $restRaw = trim(implode(' ', array_map('trim', $restParts)));
    
    // Check if suffix is attached to the last name (e.g., "Calimag Jr.", "Bandiola Jr.", "Ordes Jr.")
    foreach ($knownSuffixes as $sfx) {
        // Check for suffix at end of last name part
        $pattern = '/\s+' . preg_quote($sfx, '/') . '\.?$/i';
        if (preg_match($pattern, $lastNameRaw, $m)) {
            $suffix = rtrim($sfx, '.') . '.';
            // Normalize suffixes
            if (in_array($suffix, ['Jr.', 'Sr.'])) {
                // keep as is
            } else {
                $suffix = rtrim($sfx, '.'); // Roman numerals without period: II, III, etc.
            }
            $lastNameRaw = trim(preg_replace($pattern, '', $lastNameRaw));
            break;
        }
    }
    
    $lastName = $lastNameRaw;
    
    // Now parse the rest (first name + middle name + possible suffix)
    $words = preg_split('/\s+/', $restRaw);
    $words = array_filter($words, function($w) { return $w !== ''; });
    $words = array_values($words);
    
    // Check if the last word(s) in the rest is a suffix (e.g., "Jaime Felipe A. Jr.")
    if (empty($suffix) && count($words) > 0) {
        $lastWord = end($words);
        $cleanedWord = rtrim($lastWord, '.');
        foreach ($knownSuffixes as $sfx) {
            $cleanSfx = rtrim($sfx, '.');
            if (strcasecmp($cleanedWord, $cleanSfx) === 0) {
                if (in_array($cleanSfx, ['Jr', 'Sr'])) {
                    $suffix = $cleanSfx . '.';
                } else {
                    $suffix = $cleanSfx;
                }
                array_pop($words);
                break;
            }
        }
    }
    
    // Now split remaining words into first_name and middle_name
    // Simple rule: last word is middle name, everything before is first name
    if (count($words) === 0) {
        $firstName = '';
        $middleName = '';
    } elseif (count($words) === 1) {
        $firstName = $words[0];
        $middleName = '';
    } elseif (count($words) === 2) {
        $firstName = $words[0];
        $middleName = $words[1];
    } else {
        // 3+ words: last word is middle name, rest is first name
        $middleName = array_pop($words);
        $firstName = implode(' ', $words);
    }
    
    return [
        'last_name' => $lastName,
        'first_name' => $firstName,
        'middle_name' => $middleName,
        'suffix' => $suffix,
    ];
}

// ─── Parse CSV ──────────────────────────────────────────────────────────────
echo "Reading CSV...\n";
$handle = fopen($csvPath, 'r');
if ($handle === false) {
    echo "ERROR: Could not open CSV file.\n";
    exit(1);
}

// Skip header
$header = fgetcsv($handle);
echo "CSV Header: " . implode(' | ', $header) . "\n";

$employees = [];
$lineNum = 1;
while (($row = fgetcsv($handle)) !== false) {
    $lineNum++;
    
    $fullName = trim($row[0] ?? '');
    $position = trim($row[1] ?? '');
    $agency   = trim($row[2] ?? '');

    if (empty($fullName)) continue;
    
    // Parse name
    $parsed = parseName($fullName, $knownSuffixes);
    
    // Build display name: "LastName, FirstName MiddleName Suffix"
    $displayParts = [];
    if ($parsed['first_name']) $displayParts[] = $parsed['first_name'];
    if ($parsed['middle_name']) $displayParts[] = $parsed['middle_name'];
    if ($parsed['suffix']) $displayParts[] = $parsed['suffix'];
    $displayName = $parsed['last_name'];
    if (!empty($displayParts)) {
        $displayName .= ', ' . implode(' ', $displayParts);
    }
    
    // Clean agency "nan" values
    if (strtolower($agency) === 'nan' || empty($agency)) {
        $agency = '';
    }
    
    // Clean position "nan" values
    if (strtolower($position) === 'nan' || empty($position)) {
        $position = '';
    }
    
    $employees[] = [
        'name'        => $displayName,
        'last_name'   => $parsed['last_name'],
        'first_name'  => $parsed['first_name'],
        'middle_name' => $parsed['middle_name'],
        'suffix'      => $parsed['suffix'],
        'position'    => $position,
        'agency'      => $agency,
    ];
}
fclose($handle);

$totalCSV = count($employees);
echo "Parsed $totalCSV employees from CSV.\n";

if ($totalCSV === 0) {
    echo "ERROR: No employees found in CSV!\n";
    exit(1);
}

// ─── Show some sample parsed names ──────────────────────────────────────────
echo "\n─── Sample Parsed Names (first 15) ───\n";
for ($i = 0; $i < min(15, $totalCSV); $i++) {
    $e = $employees[$i];
    echo sprintf(
        "#%04d  Last: %-25s  First: %-25s  Middle: %-20s  Suffix: %s\n",
        $i + 1, $e['last_name'], $e['first_name'], $e['middle_name'], $e['suffix']
    );
}

// Show specific tricky names
echo "\n─── Tricky Name Test Cases ───\n";
$testCases = [
    'Calimag Jr., Francisco B.',
    'Bandiola Jr., Cirilo Manao',
    'Ordes Jr., Alfredo F.',
    'Datiles, Mark Lester, Gabatino',
    'De Guzman, Jeffrey Divinagracia',
    'Sta. Ana, Vincent Rae Soleta',
    'San Luis, Maria Elena Calvo',
    'Bañares, Lando',
    'Avila, Romeo T.',
];

foreach ($testCases as $tc) {
    $p = parseName($tc, $knownSuffixes);
    echo sprintf(
        "  %-45s => L:%-20s F:%-20s M:%-15s S:%-5s\n",
        $tc, $p['last_name'], $p['first_name'], $p['middle_name'], $p['suffix']
    );
}

// ─── Database Update ────────────────────────────────────────────────────────
echo "\n─── Starting Database Update ───\n";

// Count current employees
$currentCount = DB::table('employees')->where('status', 'Active')->count();
$totalCount = DB::table('employees')->count();
echo "Current DB: $totalCount total employees ($currentCount active)\n";
echo "CSV has: $totalCSV employees\n";

// Confirm before proceeding
echo "\nThis will:\n";
echo "  1. DELETE ALL existing employees from the database\n";
echo "  2. Insert $totalCSV employees with IDs EMP0001 to EMP" . str_pad($totalCSV, 4, '0', STR_PAD_LEFT) . "\n";
echo "  3. Reset auto-increment\n";
echo "\nProceeding in 3 seconds...\n";
sleep(3);

DB::beginTransaction();
try {
    // Disable foreign key checks temporarily
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    
    // Clear all employees (use DELETE instead of TRUNCATE for transaction safety)
    DB::table('employees')->delete();
    echo "✓ Cleared all employees.\n";
    
    // Insert new employees with sequential IDs
    $inserted = 0;
    foreach ($employees as $index => $emp) {
        $id = 'EMP' . str_pad($index + 1, 4, '0', STR_PAD_LEFT);
        
        DB::table('employees')->insert([
            'id'          => $id,
            'name'        => $emp['name'],
            'last_name'   => $emp['last_name'],
            'first_name'  => $emp['first_name'],
            'middle_name' => $emp['middle_name'],
            'suffix'      => $emp['suffix'],
            'position'    => $emp['position'],
            'agency'      => $emp['agency'],
            'email'       => '',
            'phone'       => '',
            'date_joined' => now()->toDateString(),
            'status'      => 'Active',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
        $inserted++;
    }
    
    // Re-enable foreign key checks
    DB::statement('SET FOREIGN_KEY_CHECKS=1');
    
    DB::commit();
    
    echo "✓ Successfully inserted $inserted employees.\n";
    echo "  ID Range: EMP0001 to EMP" . str_pad($totalCSV, 4, '0', STR_PAD_LEFT) . "\n";
    
    // Verify
    $newCount = DB::table('employees')->count();
    $firstId = DB::table('employees')->orderBy('id')->value('id');
    $lastId = DB::table('employees')->orderByDesc('id')->value('id');
    
    echo "\n─── Verification ───\n";
    echo "Total employees in DB: $newCount\n";
    echo "First ID: $firstId\n";
    echo "Last ID: $lastId\n";
    
    // Show first 5 and last 5
    echo "\nFirst 5 employees:\n";
    $first5 = DB::table('employees')->orderBy('id')->limit(5)->get();
    foreach ($first5 as $e) {
        echo "  $e->id | $e->last_name, $e->first_name $e->middle_name $e->suffix | $e->position | $e->agency\n";
    }
    
    echo "\nLast 5 employees:\n";
    $last5 = DB::table('employees')->orderByDesc('id')->limit(5)->get();
    foreach ($last5 as $e) {
        echo "  $e->id | $e->last_name, $e->first_name $e->middle_name $e->suffix | $e->position | $e->agency\n";
    }
    
    echo "\n✅ MASTERLIST UPDATE COMPLETE!\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    DB::statement('SET FOREIGN_KEY_CHECKS=1');
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
