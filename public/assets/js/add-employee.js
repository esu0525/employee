/**
 * Add Employee Module JavaScript
 * Handles form synchronization, image cropping, dynamic document rows, and AJAX submission with progress.
 */

let cropper;
let dobPicker;
let docRowCount = 1;

document.addEventListener('DOMContentLoaded', function () {
    const config = document.getElementById('addEmployeeForm');
    const masterlistUrl = config.getAttribute('data-masterlist-url');

    // Auto-dismiss toasts
    ['successToast', 'errorToast'].forEach(id => {
        const t = document.getElementById(id);
        if (t) setTimeout(() => closeToast(id), 6000);
    });

    // Initialize Flatpickr for Birthday
    if (document.getElementById('date_of_birth_picker')) {
        dobPicker = flatpickr("#date_of_birth_picker", {
            allowInput: true,
            dateFormat: "F j, Y",
            altInput: true,
            altFormat: "F j, Y",
            onChange: function (selectedDates, dateStr, instance) {
                if (selectedDates.length > 0) {
                    const flatDate = selectedDates[0];
                    const year = flatDate.getFullYear();
                    const month = String(flatDate.getMonth() + 1).padStart(2, '0');
                    const day = String(flatDate.getDate()).padStart(2, '0');
                    const dbFormat = `${year}-${month}-${day}`;
                    document.getElementById('date_of_birth').value = dbFormat;
                    calculateAge(flatDate);
                } else {
                    document.getElementById('date_of_birth').value = '';
                    calculateAge(null);
                }
            },
            parseDate: (datestr) => new Date(datestr)
        });

        const trigger = document.getElementById('calendarTrigger');
        if (trigger) {
            trigger.addEventListener('click', () => dobPicker.open());
        }
    }

    // Sync name on load if old values exist
    syncFullName();

    // AJAX Submission with Progress
    const form = document.getElementById('addEmployeeForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            handleFormSubmission(this, masterlistUrl);
        });
    }

    if (typeof lucide !== 'undefined') lucide.createIcons();
});

function syncFullName() {
    const lastName = document.getElementById('last_name');
    const firstName = document.getElementById('first_name');
    const middleName = document.getElementById('middle_name');
    const suffix = document.getElementById('suffix');
    const nameOutput = document.getElementById('name');

    if (!lastName || !firstName || !nameOutput) return;

    const last = lastName.value.trim();
    const first = firstName.value.trim();
    const middle = middleName.value.trim();
    const sfxPart = suffix.value.trim();

    const mi = middle ? ' ' + middle.charAt(0).toUpperCase() + '.' : '';
    const sfxToken = sfxPart ? ' ' + sfxPart : '';

    if (last && first) {
        nameOutput.value = `${last}, ${first}${mi}${sfxToken}`.trim();
    } else {
        nameOutput.value = '';
    }
}

function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            const modal = document.getElementById('cropperModal');
            const image = document.getElementById('cropperImage');
            image.src = e.target.result;
            modal.classList.add('active');

            if (cropper) cropper.destroy();

            cropper = new Cropper(image, {
                aspectRatio: 1,
                viewMode: 1,
                movable: true,
                zoomable: true,
                autoCropArea: 1,
                background: false
            });
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function closeCropper() {
    const modal = document.getElementById('cropperModal');
    if (modal) modal.classList.remove('active');
    if (cropper) cropper.destroy();
    const input = document.getElementById('profile_picture');
    if (input) input.value = "";
}

function applyCrop() {
    if (cropper) {
        const canvas = cropper.getCroppedCanvas({ width: 400, height: 400 });
        const croppedData = canvas.toDataURL('image/jpeg', 0.9);
        document.getElementById('avatarPreview').src = croppedData;
        document.getElementById('croppedImageData').value = croppedData;
        document.getElementById('profile_picture').value = "";
        closeCropper();
    }
}

function calculateAge(birthday) {
    const ageField = document.getElementById('age');
    if (!ageField) return;

    if (birthday) {
        const birthDate = new Date(birthday);
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const m = today.getMonth() - birthDate.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) age--;
        ageField.value = age + (age === 1 ? ' year old' : ' years old');
    } else {
        ageField.value = '';
    }
}

function addDocRow() {
    const container = document.getElementById('docRowsContainer');
    const newRow = document.createElement('div');
    newRow.className = 'doc-row';
    newRow.id = `docRow_${docRowCount}`;
    newRow.innerHTML = `
        <div class="add-emp-grid">
            <div class="form-group">
                <label class="form-label">Classification</label>
                <select name="doc_items[${docRowCount}][classification]" class="form-input">
                    <option value="UNCATEGORIZED">Select Classification</option>
                    <option value="APPOINTMENT">Appointment</option>
                    <option value="SERVICE RECORD">Service Record</option>
                    <option value="LEAVE / DTR">Leave / DTR</option>
                    <option value="PERSONAL DATA SHEET">Personal Data Sheet</option>
                    <option value="CLEARANCES">Clearances</option>
                    <option value="OTHERS">Others</option>
                </select>
            </div>
            <div class="form-group" style="grid-column: span 2; position: relative;">
                <label class="form-label">Upload Files</label>
                <div style="display: flex; gap: 0.75rem; align-items: center;">
                    <input type="file" name="doc_items[${docRowCount}][files][]" class="form-input" multiple acceptance=".pdf,.doc,.docx,.xlsx,.png,.jpg,.jpeg">
                    <button type="button" class="btn btn-outline btn-sm" onclick="removeDocRow(${docRowCount})" style="background: #fee2e2; border-color: #fca5a5; color: #b91c1c; min-width: 40px; padding: 0.5rem;">
                        <i data-lucide="trash-2" style="width: 16px; height: 16px;"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    container.appendChild(newRow);
    if (typeof lucide !== 'undefined') lucide.createIcons();
    docRowCount++;
}

function removeDocRow(id) {
    const row = document.getElementById(`docRow_${id}`);
    if (row) row.remove();
}

function resetForm() {
    const form = document.getElementById('addEmployeeForm');
    if (!form) return;
    form.reset();
    document.getElementById('age').value = '';
    document.getElementById('name').value = '';
    document.getElementById('avatarPreview').src = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 24 24' fill='none' stroke='%23cbd5e1' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2'%3E%3C/path%3E%3Ccircle cx='12' cy='7' r='4'%3E%3C/circle%3E%3C/svg%3E";
    
    if (dobPicker) dobPicker.clear();

    const container = document.getElementById('docRowsContainer');
    container.innerHTML = `
        <div class="doc-row" id="docRow_0">
            <div class="add-emp-grid">
                <div class="form-group">
                    <label class="form-label">Classification</label>
                    <select name="doc_items[0][classification]" class="form-input">
                        <option value="UNCATEGORIZED">Select Classification</option>
                        <option value="APPOINTMENT">Appointment</option>
                        <option value="SERVICE RECORD">Service Record</option>
                        <option value="LEAVE / DTR">Leave / DTR</option>
                        <option value="PERSONAL DATA SHEET">Personal Data Sheet</option>
                        <option value="CLEARANCES">Clearances</option>
                        <option value="OTHERS">Others</option>
                    </select>
                </div>
                <div class="form-group" style="grid-column: span 2; position: relative;">
                    <label class="form-label">Upload Files</label>
                    <div style="display: flex; gap: 0.75rem; align-items: center;">
                        <input type="file" name="doc_items[0][files][]" class="form-input" multiple acceptance=".pdf,.doc,.docx,.xlsx,.png,.jpg,.jpeg">
                    </div>
                </div>
            </div>
        </div>
    `;
    docRowCount = 1;
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function closeToast(id) {
    const toast = document.getElementById(id || 'successToast');
    if (toast) {
        toast.style.animation = 'toastSlideOut 0.3s ease-in forwards';
        setTimeout(() => toast.remove(), 300);
    }
}

async function handleFormSubmission(form, masterlistUrl) {
    syncFullName();

    const pickerVal = document.getElementById('date_of_birth_picker').value;
    const hiddenVal = document.getElementById('date_of_birth').value;
    if (pickerVal && !hiddenVal) {
        try {
            const date = new Date(pickerVal);
            if (!isNaN(date)) {
                document.getElementById('date_of_birth').value = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
            }
        } catch (err) { }
    }

    const submitBtn = document.getElementById('submitBtn');
    const modal = document.getElementById('uploadProgressModal');
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    const statusText = document.getElementById('uploadStatusText');
    const progressRateText = document.getElementById('progressRate');

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i data-lucide="loader" style="width:16px;height:16px;" class="animate-spin"></i> Processing...';
    modal.style.display = 'flex';
    if (typeof lucide !== 'undefined') lucide.createIcons();

    let startTime = Date.now();

    const compressImage = (file, quality = 0.6) => {
        return new Promise((resolve) => {
            if (!file || !file.type || !file.type.startsWith('image/')) { resolve(file); return; }
            const reader = new FileReader();
            reader.onload = (e) => {
                const img = new Image();
                img.onload = () => {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    let width = img.width;
                    let height = img.height;
                    const maxSide = 1600;
                    if (width > height && width > maxSide) { height *= maxSide / width; width = maxSide; }
                    else if (height > width && height > maxSide) { width *= maxSide / height; height = maxSide; }
                    canvas.width = width;
                    canvas.height = height;
                    ctx.drawImage(img, 0, 0, width, height);
                    canvas.toBlob((blob) => {
                        resolve(new File([blob], file.name, { type: 'image/jpeg' }));
                    }, 'image/jpeg', quality);
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        });
    };

    const finalFormData = new FormData();
    const originalFormData = new FormData(form);

    statusText.textContent = 'Compressing images... This makes it upload faster!';

    for (const [key, value] of originalFormData.entries()) {
        if (value instanceof File && value.type && value.type.startsWith('image/')) {
            if (value.size > 500 * 1024) {
                const compressed = await compressImage(value, 0.7);
                finalFormData.append(key, compressed);
            } else {
                finalFormData.append(key, value);
            }
        } else {
            finalFormData.append(key, value);
        }
    }

    statusText.textContent = 'Uploading files... Please wait.';

    const xhr = new XMLHttpRequest();
    xhr.open('POST', form.action, true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

    xhr.upload.onprogress = function (e) {
        if (e.lengthComputable) {
            const percent = Math.round((e.loaded / e.total) * 100);
            progressBar.style.width = percent + '%';
            progressText.textContent = percent + '% Complete';

            const duration = (Date.now() - startTime) / 1000;
            if (duration > 0) {
                const kbps = (e.loaded / 1024 / duration).toFixed(1);
                progressRateText.textContent = kbps + ' KB/s';
            }

            if (percent === 100) {
                statusText.textContent = 'Processing and finalizing record... Almost there!';
            }
        }
    };

    xhr.onload = function () {
        if (xhr.status === 200) {
            progressBar.style.width = '100%';
            progressText.textContent = '100% Complete';
            document.getElementById('uploadStatusTitle').textContent = 'Registration Successful!';
            statusText.textContent = 'Employee record has been saved.';
            const alertEl = document.getElementById('uploadAlert');
            alertEl.style.color = '#10b981';
            alertEl.textContent = 'Refreshing...';

            setTimeout(() => {
                const response = JSON.parse(xhr.responseText);
                window.location.href = response.redirect || masterlistUrl;
            }, 1000);
        } else {
            let errorMsg = 'Failed to save employee record.';
            try {
                const response = JSON.parse(xhr.responseText);
                errorMsg = response.message || errorMsg;
            } catch (err) { }
            alert('Error: ' + errorMsg);
            modal.style.display = 'none';
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Save Employee';
        }
        if (typeof lucide !== 'undefined') lucide.createIcons();
    };

    xhr.onerror = function () {
        alert('A network error occurred.');
        modal.style.display = 'none';
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Save Employee';
    };

    xhr.send(finalFormData);
}
