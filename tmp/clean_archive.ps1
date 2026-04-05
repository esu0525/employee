$path = 'c:\xampp\htdocs\php-employee-system\resources\views\archive.blade.php'
$content = Get-Content $path
$startLine = -1
$endLine = $content.Count

# Find the LAST instance of @push('scripts')
for ($i = $content.Count - 1; $i -ge 0; $i--) {
    if ($content[$i] -match "@push\('scripts'\)") {
        $startLine = $i
        break
    }
}

if ($startLine -ne -1) {
    $newContent = $content[0..($startLine - 1)]
    $newContent += "@push('scripts')"
    $newContent += '<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>'
    $newContent += '<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>'
    $newContent += '<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>'
    $newContent += '<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>'
    $newContent += '<script src="{{ asset(''assets/js/archive.js'') }}"></script>'
    $newContent += "@endpush"
    $newContent | Set-Content $path
} else {
    Write-Error "Could not find @push('scripts')"
}
