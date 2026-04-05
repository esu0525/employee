/**
 * Employee Details Module JavaScript
 * Handles profile management, tab switching, and advanced document viewing.
 */

let currentIndex = -1;
let cropper;
let documentsList = [];

document.addEventListener('DOMContentLoaded', () => {
    // Handle tab persistence from URL
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get('tab');
    if (activeTab) {
        const tabBtn = Array.from(document.querySelectorAll('.tab-btn')).find(btn =>
            btn.getAttribute('onclick')?.includes(`'${activeTab}'`)
        );
        if (tabBtn) {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.info-tab-pane').forEach(p => p.classList.remove('active'));

            tabBtn.classList.add('active');
            const pane = document.getElementById(activeTab + 'Tab');
            if (pane) pane.classList.add('active');
        }
    }

    if (typeof lucide !== 'undefined') lucide.createIcons();

    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
        const docsTab = document.getElementById('documentsTab');
        if (docsTab && docsTab.classList.contains('active')) {
            if (e.key === 'ArrowRight') { e.preventDefault(); nextDoc(); }
            if (e.key === 'ArrowLeft') { e.preventDefault(); prevDoc(); }
        }
    });

    // Initialize documents list for keys
    refreshDocList();
});

// Tab Management
window.switchTab = function(btn, tabId) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.info-tab-pane').forEach(pane => pane.classList.remove('active'));

    btn.classList.add('active');
    const pane = document.getElementById(tabId + 'Tab');
    if (pane) pane.classList.add('active');

    const url = new URL(window.location);
    url.searchParams.set('tab', tabId);
    window.history.replaceState({}, '', url);
}

// Avatar Management
window.initCropper = function(input) {
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
        }
        reader.readAsDataURL(input.files[0]);
    }
}

window.closeCropper = function() {
    const modal = document.getElementById('cropperModal');
    if (modal) modal.classList.remove('active');
    if (cropper) cropper.destroy();
    const input = document.getElementById('avatarInput');
    if (input) input.value = "";
}

window.applyCrop = function() {
    if (cropper) {
        const canvas = cropper.getCroppedCanvas({ width: 400, height: 400 });
        const croppedData = canvas.toDataURL('image/jpeg', 0.9);
        document.getElementById('croppedImageData').value = croppedData;

        // Clear the file input
        document.getElementById('avatarInput').value = "";

        // Show loading state
        const saveBtn = document.querySelector('#cropperModal .btn-save');
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i data-lucide="loader" class="animate-spin" style="width:16px;height:16px;"></i> Uploading...';
        if (typeof lucide !== 'undefined') lucide.createIcons();

        document.getElementById('avatarForm').submit();
    }
}

window.viewPhoto = function() {
    const img = document.getElementById('mainAvatarImage');
    if (img) {
        document.getElementById('viewingImage').src = img.src;
        document.getElementById('viewPhotoModal').classList.add('active');
    }
}

window.closePhotoView = function() {
    const modal = document.getElementById('viewPhotoModal');
    if (modal) modal.classList.remove('active');
}

// Document Management
window.refreshDocList = function() {
    const items = Array.from(document.querySelectorAll('.mini-doc-item:not([style*="display: none"])'));
    documentsList = items.map(item => ({
        id: item.id.replace('doc-item-', ''),
        url: item.dataset.url,
        name: item.dataset.name
    }));
}

window.toggleCategory = function(header) {
    const group = header.parentElement;
    const content = group.querySelector('.category-content');
    header.classList.toggle('active');
    content.classList.toggle('active');
}

window.triggerImport = function() {
    const category = document.getElementById('importCategory');
    if (category) category.value = 'GENERAL';
    const input = document.getElementById('importFileInput');
    if (input) input.click();
}

window.triggerCategoryUpload = function(cat) {
    const category = document.getElementById('importCategory');
    if (category) category.value = cat;
    const input = document.getElementById('importFileInput');
    if (input) input.click();
}

window.handleBatchUpload = function(input) {
    if (!input.files || input.files.length === 0) return;

    const files = input.files;
    const form = document.getElementById('importForm');
    const employeeId = form.getAttribute('data-employee-id');
    const uploadUrlBase = form.getAttribute('data-upload-url-base');
    const csrfToken = form.querySelector('input[name="_token"]').value;
    const category = document.getElementById('importCategory').value;
    
    const compressImage = (file, quality = 0.6) => {
        return new Promise((resolve) => {
            if (!file.type || !file.type.startsWith('image/')) { resolve(file); return; }
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

    const processUpload = async () => {
        const formData = new FormData();
        formData.append('_token', csrfToken);
        formData.append('category', category);

        const modal = document.getElementById('uploadProgressModal');
        const progressBar = document.getElementById('progressBar');
        const progressText = document.getElementById('progressText');
        const progressFiles = document.getElementById('progressFiles');
        const statusText = document.getElementById('uploadStatusText');

        modal.style.display = 'flex';
        modal.classList.add('active');
        statusText.textContent = 'Processing and compressing files...';
        progressFiles.textContent = `0/${files.length} Files`;

        for (let i = 0; i < files.length; i++) {
            let fileToUpload = files[i];
            if (fileToUpload.type && fileToUpload.type.startsWith('image/') && fileToUpload.size > 500 * 1024) {
                fileToUpload = await compressImage(fileToUpload, 0.7);
            }
            formData.append('documents[]', fileToUpload);
        }

        statusText.textContent = 'Uploading files to server...';

        const xhr = new XMLHttpRequest();
        xhr.open('POST', `${uploadUrlBase}/${employeeId}`, true);

        xhr.upload.onprogress = function (e) {
            if (e.lengthComputable) {
                const percent = Math.round((e.loaded / e.total) * 100);
                progressBar.style.width = percent + '%';
                progressText.textContent = percent + '% Complete';

                const filesUploaded = Math.floor((e.loaded / e.total) * files.length);
                progressFiles.textContent = `${filesUploaded}/${files.length} Files`;

                if (percent === 100) {
                    statusText.textContent = 'Finalizing and saving to database...';
                    const alertEl = document.getElementById('uploadAlert');
                    if (alertEl) alertEl.textContent = 'Almost there! Do not refresh.';
                    progressFiles.textContent = `${files.length}/${files.length} Files`;
                }
            }
        };

        xhr.onload = function () {
            if (xhr.status === 200) {
                progressBar.style.width = '100%';
                progressText.textContent = '100% Complete';
                progressFiles.textContent = `${files.length}/${files.length} Files`;
                document.getElementById('uploadStatusTitle').textContent = 'Upload Successful!';
                statusText.textContent = 'All documents have been saved.';
                const alertEl = document.getElementById('uploadAlert');
                if (alertEl) {
                    alertEl.style.color = '#10b981';
                    alertEl.textContent = 'Refreshing...';
                }

                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                let errorMsg = 'An error occurred during upload.';
                try {
                    const response = JSON.parse(xhr.responseText);
                    errorMsg = response.message || errorMsg;
                } catch (e) { }

                alert('Upload Failed: ' + errorMsg);
                modal.style.display = 'none';
            }
        };

        xhr.onerror = function () {
            alert('A network error occurred.');
            modal.style.display = 'none';
        };

        xhr.send(formData);
    };

    processUpload();
}

window.previewDocById = function(id, url, name) {
    let frame = document.getElementById('pdfFrame');
    const noPreview = document.getElementById('noPreview');
    const controls = document.getElementById('previewControls');

    document.querySelectorAll('.mini-doc-item').forEach(item => item.classList.remove('active'));
    const activeItem = document.getElementById('doc-item-' + id);
    if (activeItem) activeItem.classList.add('active');

    // To prevent "Leave site?" prompt from the native PDF viewer's unsaved state
    const parent = frame.parentElement;
    const newFrame = frame.cloneNode(false);
    frame.onbeforeunload = null;
    frame.remove();
    parent.appendChild(newFrame);
    frame = newFrame;

    let extension = 'unknown';
    if (name && name.includes('.')) {
        extension = name.split('.').pop().toLowerCase();
    } else if (url && !url.includes('display-file') && url.includes('.')) {
        extension = url.split('.').pop().toLowerCase();
    }

    noPreview.innerHTML = '';
    noPreview.style.display = 'none';
    frame.style.display = 'none';
    frame.src = 'about:blank';

    if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension)) {
        noPreview.innerHTML = `<img src="${url}" style="max-width: 90%; max-height: 90%; object-fit: contain; box-shadow: 0 20px 50px rgba(0,0,0,0.15); border-radius: 12px; border: 4px solid white;">`;
        noPreview.style.display = 'flex';
    } else if (extension === 'docx') {
        noPreview.style.display = 'block';
        noPreview.innerHTML = `
            <div id="docx-loader" style="position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; background: white; z-index: 10;">
                <div class="animate-spin" style="width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #3498db; border-radius: 50%; margin-bottom: 1rem;"></div>
                <p style="font-family: 'Outfit'; font-weight: 600; color: #64748b;">Rendering Document...</p>
            </div>
            <div id="docx-container" style="width: 100%; height: 100%; overflow: auto; background: #525659; position: relative;"></div>
        `;

        fetch(url).then(res => res.blob()).then(blob => {
            if (typeof docx !== 'undefined') {
                docx.renderAsync(blob, document.getElementById("docx-container"), null, {
                    inWrapper: true,
                    ignoreWidth: false,
                    ignoreHeight: false
                }).then(() => {
                    document.getElementById('docx-loader').style.display = 'none';
                    const docxWrapper = document.querySelector('.docx-wrapper');
                    if (docxWrapper) {
                        docxWrapper.style.padding = '3rem 2rem';
                        docxWrapper.style.background = 'transparent';
                        docxWrapper.style.minWidth = 'max-content';
                        const docxContent = docxWrapper.querySelector('.docx');
                        if (docxContent) {
                            docxContent.style.boxShadow = '0 15px 35px rgba(0,0,0,0.4)';
                            docxContent.style.margin = '0 auto';
                            docxContent.style.overflowX = 'auto';
                        }
                    }
                });
            } else {
                document.getElementById('docx-loader').innerHTML = '<p class="text-danger">docx-preview library not loaded.</p>';
            }
        }).catch(() => {
            document.getElementById('docx-loader').innerHTML = '<p class="text-danger">Failed to load document content.</p>';
        });
    } else if (extension === 'pdf') {
        frame.src = url;
        frame.style.display = 'block';
    } else if (['doc', 'xls', 'xlsx', 'ppt', 'pptx'].includes(extension)) {
        noPreview.style.display = 'flex';
        const isLocal = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';

        noPreview.innerHTML = `
            <div style="text-align: center; width: 100%; height: 100%; display: flex; flex-direction: column; background: #525659;">
                <div style="padding: 0.75rem; background: #323639; border-bottom: 1px solid #4a4d51; display: flex; justify-content: center; gap: 1rem; align-items: center;">
                    <span style="color: white; font-size: 0.85rem; font-weight: 600; margin-right: auto; padding-left: 1rem;">${name}</span>
                    <button onclick="window.open('https://docs.google.com/viewer?url=${encodeURIComponent(url)}', '_blank')" class="btn-save" style="font-size: 0.75rem; padding: 0.4rem 0.8rem; background: #3b82f6;">
                        <i data-lucide="external-link"></i> Open in Google Viewer
                    </button>
                    <a href="${url}" download style="font-size: 0.75rem; padding: 0.4rem 0.8rem; text-decoration: none; display: flex; align-items: center; gap: 0.3rem; color: white !important; border: 1px solid rgba(255,255,255,0.25); border-radius: 8px; background: rgba(255,255,255,0.05); transition: 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.15)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">
                        <i data-lucide="download"></i> Download
                    </a>
                </div>
                ${isLocal ? `
                <div style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; color: white; padding: 2rem;">
                    <div style="width: 80px; height: 80px; background: rgba(255,255,255,0.1); border-radius: 20px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem;">
                        <i data-lucide="shield-alert" style="width: 40px; height: 40px; color: #f59e0b;"></i>
                    </div>
                    <h3 style="font-family: 'Outfit'; margin-bottom: 0.5rem;">Local Preview Restricted</h3>
                    <p style="color: #94a3b8; max-width: 400px; font-size: 0.9rem; line-height: 1.5;">
                        Google Viewer cannot access files on <b>localhost</b>. This preview will work automatically once the system is hosted on a public server.
                    </p>
                    <div style="margin-top: 2rem; display: flex; gap: 1rem;">
                        <a href="${url}" download class="btn-save" style="background: #3b82f6;">Download to View</a>
                    </div>
                </div>
                ` : `
                <iframe src="https://docs.google.com/viewer?url=${encodeURIComponent(url)}&embedded=true" style="flex: 1; width: 100%; border: none;"></iframe>
                `}
            </div>
        `;
        if (window.lucide) lucide.createIcons();
    } else {
        if (url.includes('display-file')) {
            frame.src = url;
            frame.style.display = 'block';
        } else {
            let icon = (['xlsx', 'xls'].includes(extension)) ? 'file-spreadsheet' : 'file-text';
            noPreview.style.display = 'flex';
            noPreview.innerHTML = `
                <div style="text-align: center; padding: 3rem; background: #525659; color: white; width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                    <div style="width: 80px; height: 80px; background: rgba(255,255,255,0.1); border-radius: 20px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; color: #3b82f6;">
                        <i data-lucide="${icon}" style="width: 40px; height: 40px;"></i>
                    </div>
                    <h3 style="margin-bottom: 0.5rem; font-family: 'Outfit';">Preview not available for .${extension}</h3>
                    <p style="color: #94a3b8; margin-bottom: 2rem;">Please download the file to view it on your device.</p>
                    <a href="${url}" download class="btn-save" style="display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none; color: white !important; background: #3b82f6;">
                        <i data-lucide="download"></i> Download & Open File
                    </a>
                </div>
            `;
            if (window.lucide) lucide.createIcons();
        }
    }

    controls.style.display = 'flex';
    refreshDocList();
    currentIndex = documentsList.findIndex(d => d.url === url);
    updateControls();
}

window.filterDocuments = function() {
    const query = document.getElementById('docSearch').value.toLowerCase();
    document.querySelectorAll('.mini-doc-item').forEach(item => {
        const matches = item.dataset.name.toLowerCase().includes(query);
        item.style.display = matches ? 'flex' : 'none';
    });

    document.querySelectorAll('.doc-category-group').forEach(group => {
        const items = group.querySelectorAll('.mini-doc-item');
        let hasVisible = false;
        items.forEach(i => { if (i.style.display !== 'none') hasVisible = true; });

        const content = group.querySelector('.category-content');
        const header = group.querySelector('.category-header');

        if (hasVisible && query !== '') {
            group.style.display = 'block';
            content.classList.add('active');
            header.classList.add('active');
        } else if (query === '') {
            group.style.display = 'block';
        } else {
            group.style.display = 'none';
        }
    });
    refreshDocList();
    updateControls();
}

window.updateControls = function() {
    const btnPrev = document.getElementById('btnPrev');
    const btnNext = document.getElementById('btnNext');
    const counter = document.getElementById('docCounter');
    if (!btnPrev || !btnNext || !counter) return;
    btnPrev.disabled = currentIndex <= 0;
    btnNext.disabled = currentIndex >= documentsList.length - 1 || currentIndex === -1;
    counter.textContent = documentsList.length === 0 ? '0/0' : `${currentIndex + 1}/${documentsList.length}`;
}

window.nextDoc = function() { if (currentIndex < documentsList.length - 1) { const d = documentsList[currentIndex + 1]; previewDocById(d.id, d.url, d.name); } }
window.prevDoc = function() { if (currentIndex > 0) { const d = documentsList[currentIndex - 1]; previewDocById(d.id, d.url, d.name); } }

window.printPreview = function() {
    const frame = document.getElementById('pdfFrame');
    if (frame.src) frame.contentWindow.print();
}

window.openExternal = function() {
    if (currentIndex !== -1 && documentsList[currentIndex]) {
        window.open(documentsList[currentIndex].url, '_blank');
    }
}

window.fullscreenDoc = function() {
    const viewer = document.querySelector('.preview-mini-card');
    if (!document.fullscreenElement) {
        viewer.requestFullscreen().catch(err => {
            console.error(`Error attempting to enable full-screen mode: ${err.message}`);
        });
        viewer.classList.add('is-fullscreen');
    } else {
        document.exitFullscreen();
    }
}

document.addEventListener('fullscreenchange', () => {
    const viewer = document.querySelector('.preview-mini-card');
    if (!document.fullscreenElement) {
        viewer.classList.remove('is-fullscreen');
    }
});

// Modal Management
window.openEditModal = function(type) { 
    const modal = document.getElementById(type + 'EditModal');
    if (modal) modal.classList.add('active'); 
}
window.closeEditModal = function(type) { 
    const modal = document.getElementById(type + 'EditModal');
    if (modal) modal.classList.remove('active'); 
}

window.confirmDeleteDocument = async function(event, button) {
    event.preventDefault();
    event.stopPropagation();

    const confirmed = await window.confirmAction({
        title: 'Delete Document?',
        message: 'Are you sure you want to delete this document? This action cannot be undone.',
        confirmText: 'Delete',
        cancelText: 'Cancel',
        type: 'danger'
    });

    if (confirmed) {
        button.closest('form').submit();
    }
}

window.onclick = function (event) {
    if (event.target.classList.contains('modal-modern')) event.target.classList.remove('active');
}
