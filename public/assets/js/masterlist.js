/**
 * Master List Module JavaScript
 * Handles employee search, dynamic modal interactions for adding employees,
 * and real-time status updates with logic for transfer locations.
 */

document.addEventListener('DOMContentLoaded', function () {
    if (typeof lucide !== 'undefined') lucide.createIcons();

    // Success Toast Auto-dismissal
    const toast = document.getElementById('successToast');
    if (toast) {
        setTimeout(() => {
            closeToast();
        }, 4000);
    }
    
    // Pagination Listeners
    document.addEventListener('click', function(e) {
        const target = e.target.closest('.pagination-ajax');
        if (target) {
            e.preventDefault();
            const page = target.getAttribute('data-page');
            window.fetchData(page);
        }
    });
});

window.openAddEmployeeModal = function() {
    const modal = document.getElementById('addEmployeeModal');
    if (modal) {
        modal.classList.add('active');
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }
}

window.closeAddEmployeeModal = function() {
    const modal = document.getElementById('addEmployeeModal');
    if (modal) modal.classList.remove('active');
}

window.syncFullName = function() {
    const last = document.getElementById('last_name').value;
    const first = document.getElementById('first_name').value;
    const middle = document.getElementById('middle_name').value;
    const mi = middle ? middle.charAt(0).toUpperCase() + '.' : '';
    
    const nameField = document.getElementById('name');
    if (nameField) {
        nameField.value = `${first}${mi ? ' ' + mi : ''} ${last}`.trim();
    }
}

window.calculateAge = function() {
    const birthday = document.getElementById('date_of_birth').value;
    const ageField = document.getElementById('age');
    
    if (birthday && ageField) {
        const birthDate = new Date(birthday);
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();
        
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        
        ageField.value = age + ' years old';
    } else if (ageField) {
        ageField.value = '';
    }
}

window.closeToast = function() {
    const toast = document.getElementById('successToast');
    if (toast) {
        toast.style.animation = 'toastSlideOut 0.3s ease-in forwards';
        setTimeout(() => toast.remove(), 300);
    }
}

// ─── Status Modal Logic ───
window.openStatusModal = function(id, name, currentStatus = 'active') {
    const modal = document.getElementById('statusModal');
    const form = document.getElementById('statusForm');
    const nameDisplay = document.getElementById('statusEmployeeName');
    
    if (modal && form && nameDisplay) {
        form.action = `/employee/update-status/${id}`;
        nameDisplay.textContent = `Employee: ${name}`;
        modal.style.display = 'flex';
        setTimeout(() => modal.classList.add('active'), 10);
        
        // Handle pre-selection if needed
        const select = document.getElementById('statusSelect');
        if (select) {
            select.value = currentStatus !== 'active' ? currentStatus : '';
            window.handleStatusFields();
        }
        
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }
}

window.closeStatusModal = function() {
    const modal = document.getElementById('statusModal');
    if (modal) {
        modal.classList.remove('active');
        setTimeout(() => modal.style.display = 'none', 300);
    }
}

window.handleStatusFields = function() {
    const status = document.getElementById('statusSelect').value;
    const fields = ['transferFields', 'othersFields', 'commonHistoryFields'];
    
    // Hide all first
    fields.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.style.display = 'none';
    });

    // Show relevant
    if (status === 'transfer') {
        document.getElementById('transferFields').style.display = 'block';
        document.getElementById('commonHistoryFields').style.display = 'block';
    } else if (status === 'retired') {
        document.getElementById('othersFields').style.display = 'block';
        document.querySelector('#othersFields label').textContent = 'Retirement Details';
        document.getElementById('status_specify').placeholder = 'e.g. RA 8291 / Optional';
        document.getElementById('commonHistoryFields').style.display = 'block';
    } else if (status === 'others') {
        document.getElementById('othersFields').style.display = 'block';
        document.querySelector('#othersFields label').textContent = 'Please Specify';
        document.getElementById('status_specify').placeholder = 'Specify status details...';
        document.getElementById('commonHistoryFields').style.display = 'block';
    } else if (status === 'resign') {
        document.getElementById('commonHistoryFields').style.display = 'block';
    }
}

// ─── Live Search & Filters ───
let searchTimer;
window.liveSearch = function(query) {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        window.fetchData(1);
    }, 400);
}

window.toggleSort = function() {
    const actionBar = document.querySelector('.action-bar');
    let currentSort = actionBar.getAttribute('data-initial-sort');
    currentSort = (currentSort === 'name') ? 'position' : 'name';
    actionBar.setAttribute('data-initial-sort', currentSort);
    
    // Update button visual
    const sortBtn = document.getElementById('sortBtn');
    if (sortBtn) {
        sortBtn.innerHTML = currentSort === 'position' 
            ? '<i data-lucide="briefcase"></i> Sort by Position' 
            : '<i data-lucide="sort-asc"></i> Sort by Name';
        sortBtn.className = currentSort === 'position' ? 'btn btn-primary' : 'btn btn-outline';
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }
    
    window.fetchData(1);
}

// ─── Data Fetching ───
window.fetchData = function(page = 1) {
    const query = document.getElementById('searchInput').value;
    const sort = document.querySelector('.action-bar').getAttribute('data-initial-sort');
    const container = document.getElementById('tableContainer');
    
    if (container) container.style.opacity = '0.5';

    let url = `/masterlist?page=${page}&search=${encodeURIComponent(query)}&sort=${sort}`;

    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.text())
    .then(html => {
        if (container) {
            container.innerHTML = html;
            container.style.opacity = '1';
            window.scrollTo({ top: 0, behavior: 'smooth' });
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
    })
    .catch(error => {
        console.error('Error fetching data:', error);
        if (container) container.style.opacity = '1';
    });
}

// ─── Success Modal Logic ───
window.closeSuccessModal = function() {
    const modal = document.getElementById('successModal');
    if (modal) {
        modal.classList.remove('active');
        setTimeout(() => modal.remove(), 300);
    }
}

// ─── Export Logic ───
window.toggleExportMenu = function(e) {
    e.stopPropagation();
    const menu = document.getElementById('exportMenu');
    if (menu) {
        menu.classList.toggle('active');
    }
}

window.exportData = function(format) {
    const query = document.getElementById('searchInput').value;
    const sort = document.querySelector('.action-bar').getAttribute('data-initial-sort');
    const exportUrl = document.querySelector('.action-bar').getAttribute('data-export-url');
    
    // Auto-close menu
    const menu = document.getElementById('exportMenu');
    if (menu) menu.classList.remove('active');

    // Notify user (optional: add a "Generating..." toast)
    const btn = document.querySelector(`.export-dropdown button`);
    if (btn) btn.style.opacity = '0.5';

    let url = `${exportUrl}?search=${encodeURIComponent(query)}&sort=${sort}`;

    fetch(url)
    .then(response => response.json())
    .then(data => {
        if (btn) btn.style.opacity = '1';
        
        if (format === 'excel') {
            generateExcel(data);
        } else if (format === 'pdf') {
            generatePDF(data);
        } else if (format === 'docs') {
            generateDocsSimple(data);
        }
    })
    .catch(error => {
        console.error('Export failed:', error);
        if (btn) btn.style.opacity = '1';
        alert('Failed to generate export file. Please try again.');
    });
}

function generateExcel(employees) {
    if (typeof ExcelJS === 'undefined') return;
    const workbook = new ExcelJS.Workbook();
    const sheet = workbook.addWorksheet('Masterlist');

    // Title Row
    const titleRow = sheet.addRow(['EMPLOYEE MASTERLIST']);
    sheet.mergeCells(1, 1, 1, 7);
    titleRow.eachCell(c => {
        c.font = { size: 14, bold: true, name: 'Arial Narrow' };
        c.alignment = { horizontal: 'center' };
    });

    sheet.addRow([`Report Generated: ${new Date().toLocaleDateString()}`]);
    sheet.mergeCells(2, 1, 2, 7);
    sheet.getRow(2).eachCell(c => {
        c.font = { italic: true, size: 10, name: 'Arial Narrow' };
        c.alignment = { horizontal: 'center' };
    });

    sheet.addRow([]);

    // Headers
    const headers = ['NAME', 'POSITION', 'AGENCY', 'CATEGORY', 'STATUS', 'S.G.', 'LEVEL'];
    const headerRow = sheet.addRow(headers);
    headerRow.eachCell((c, i) => {
        c.font = { bold: true, color: { argb: 'FFFFFFFF' }, size: 10, name: 'Arial Narrow' };
        c.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF3B82F6' } };
        c.alignment = { horizontal: 'center', vertical: 'middle' };
        c.border = { top: { style: 'thin' }, left: { style: 'thin' }, bottom: { style: 'thin' }, right: { style: 'thin' } };
    });

    // Body
    employees.forEach((e, i) => {
        const row = sheet.addRow([
            e.name,
            e.position,
            e.agency,
            e.category || '-',
            e.employment_status || '-',
            e.salary_grade || '-',
            e.level_of_position || '-'
        ]);
        row.eachCell(c => {
            c.font = { size: 10, name: 'Arial Narrow' };
            c.alignment = { horizontal: 'center' };
            c.border = { top: { style: 'thin' }, left: { style: 'thin' }, bottom: { style: 'thin' }, right: { style: 'thin' } };
        });
    });

    // Column widths
    sheet.getColumn(1).width = 40;
    sheet.getColumn(2).width = 30;
    sheet.getColumn(3).width = 25;
    sheet.getColumn(4).width = 15;
    sheet.getColumn(5).width = 15;
    sheet.getColumn(6).width = 8;
    sheet.getColumn(7).width = 12;

    workbook.xlsx.writeBuffer().then(buffer => {
        saveAs(new Blob([buffer]), `Masterlist_Report_${new Date().toISOString().slice(0,10)}.xlsx`);
    });
}

function generatePDF(employees) {
    if (typeof jspdf === 'undefined') return;
    const { jsPDF } = jspdf;
    const doc = new jsPDF('l', 'mm', 'a4'); // Landscape for Masterlist
    
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(16);
    doc.text('EMPLOYEE MASTERLIST', 148, 15, { align: 'center' });
    
    doc.setFontSize(9);
    doc.setFont('helvetica', 'italic');
    doc.text(`Generated on ${new Date().toLocaleString()}`, 148, 20, { align: 'center' });

    const tableBody = employees.map((e, i) => [
        e.name,
        e.position,
        e.agency,
        e.employment_status || '-',
        e.salary_grade || '-',
        e.level_of_position || '-'
    ]);

    doc.autoTable({
        head: [['NAME', 'POSITION', 'AGENCY', 'STATUS', 'S.G.', 'LEVEL']],
        body: tableBody,
        startY: 28,
        theme: 'grid',
        styles: { fontSize: 8, cellPadding: 2, font: 'helvetica' },
        headStyles: { fillColor: [59, 130, 246], textColor: [255, 255, 255], fontStyle: 'bold', halign: 'center' },
        bodyStyles: { valign: 'middle', halign: 'center' },
        columnStyles: {
            0: { halign: 'left' },
            1: { halign: 'left' },
            2: { halign: 'left' }
        }
    });

    doc.save(`Masterlist_Report_${new Date().toISOString().slice(0,10)}.pdf`);
}

function generateDocsSimple(employees) {
    // Simple CSV-based trick for "Word" or just use table html
    let content = "NAME,POSITION,AGENCY,STATUS,S.G.,LEVEL\n";
    employees.forEach((e, i) => {
        content += `"${e.name}","${e.position}","${e.agency}","${e.employment_status || '-'}","${e.salary_grade || '-'}","${e.level_of_position || '-'}"\n`;
    });
    
    saveAs(new Blob([content], { type: "text/csv;charset=utf-8" }), `Masterlist_Data_${new Date().toISOString().slice(0,10)}.csv`);
}

// Global window event for outside clicks
window.addEventListener('click', function(e) {
    const addModal = document.getElementById('addEmployeeModal');
    const statusModal = document.getElementById('statusModal');
    const exportMenu = document.getElementById('exportMenu');
    
    if (e.target === addModal) window.closeAddEmployeeModal();
    if (e.target === statusModal) window.closeStatusModal();
    
    if (exportMenu && !exportMenu.contains(e.target)) {
        exportMenu.classList.remove('active');
    }
});

