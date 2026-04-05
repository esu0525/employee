/**
 * Online Request Portal JavaScript
 * Handles multi-step form interaction, dynamic purpose selection, 
 * supportive documents management, and AJAX-based file uploads with progress tracking.
 */

document.addEventListener('DOMContentLoaded', function () {
    if (typeof lucide !== 'undefined') lucide.createIcons();

    // Portal Form Submission with Progress
    const portalForm = document.getElementById('portalRequestForm');
    if (portalForm) {
        portalForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const formData = new FormData(form);
            const modal = document.getElementById('uploadProgressModal');
            const progressBar = document.getElementById('progressBar');
            const progressText = document.getElementById('progressText');
            const statusText = document.getElementById('uploadStatusText');
            const progressRate = document.getElementById('progressRate');
            
            if (modal) modal.style.display = 'flex';
            let startTime = Date.now();
            
            const xhr = new XMLHttpRequest();
            xhr.open('POST', form.action, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            
            xhr.upload.onprogress = function(e) {
                if (e.lengthComputable) {
                    const percent = Math.round((e.loaded / e.total) * 100);
                    if (progressBar) progressBar.style.width = percent + '%';
                    if (progressText) progressText.textContent = percent + '% Complete';
                    
                    // Calculate upload rate
                    const duration = (Date.now() - startTime) / 1000;
                    if (duration > 0 && progressRate) {
                        const kbps = (e.loaded / 1024 / duration).toFixed(1);
                        progressRate.textContent = kbps + ' KB/s';
                    }
                    
                    if (percent === 100 && statusText) {
                        statusText.textContent = 'Processing request... Please don\'t leave.';
                    }
                }
            };
            
            xhr.onload = function() {
                if (xhr.status === 200) {
                    if (progressBar) progressBar.style.width = '100%';
                    if (progressText) progressText.textContent = '100% Complete';
                    const title = document.getElementById('uploadStatusTitle');
                    if (title) title.textContent = 'Submission Success!';
                    if (statusText) statusText.textContent = 'Your request has been filed successfully.';
                    
                    // Redirect or reload
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    let errorMsg = 'An error occurred during submission.';
                    try {
                        const response = JSON.parse(xhr.responseText);
                        errorMsg = response.message || errorMsg;
                    } catch(err) {}
                    alert('Submission Failed: ' + errorMsg);
                    if (modal) modal.style.display = 'none';
                }
            };
            
            xhr.onerror = function() {
                alert('A network error occurred.');
                if (modal) modal.style.display = 'none';
            };
            
            xhr.send(formData);
        });
    }
});

window.handleToggle = function(checkbox) {
    const checkboxes = document.querySelectorAll('input[name="purpose"]');
    const isChecked = checkbox.checked;

    // Reset all checkboxes except the current one
    checkboxes.forEach(cb => {
        if (cb !== checkbox) cb.checked = false;
    });

    const otherInput = document.getElementById('purpose_other_input');
    if (otherInput) otherInput.style.display = 'none';

    document.querySelectorAll('.purpose-radio-box').forEach(box => {
        box.style.borderColor = '#f1f5f9';
        box.style.background = 'white';
        box.style.color = '#64748b';
        box.style.transform = 'none';
    });

    if (isChecked) {
        const box = checkbox.nextElementSibling;
        if (box && box.classList.contains('purpose-radio-box')) {
            box.style.borderColor = '#4f46e5';
            box.style.background = '#eef2ff';
            box.style.color = '#4f46e5';
            box.style.transform = 'translateY(-2px)';
        }
        
        if (checkbox.value === 'OTHERS' && otherInput) {
            otherInput.style.display = 'block';
            otherInput.focus();
        }
    }
}

window.addFileInput = function() {
    const container = document.getElementById('fileInputsContainer');
    if (!container) return;
    const wrapper = document.createElement('div');
    wrapper.className = 'file-input-wrapper';
    wrapper.style.marginBottom = '1rem';
    wrapper.style.display = 'flex';
    wrapper.style.gap = '0.75rem';
    wrapper.style.alignItems = 'center';
    wrapper.style.animation = 'fadeInUp 0.3s ease-out';
    
    wrapper.innerHTML = `
        <div style="flex: 1; position: relative;">
            <input type="file" name="requirements_files[]" class="portal-input" style="background: white; border: 1px solid #e2e8f0; padding-top: 0.625rem; padding-bottom: 0.625rem;">
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="btn-portal-secondary" style="height: 3.25rem; width: 3.25rem; display: flex; align-items: center; justify-content: center; border-radius: 14px; padding: 0; color: #ef4444; border-color: #fecaca;">
            <i data-lucide="trash-2" style="width: 20px; height: 20px;"></i>
        </button>
    `;
    
    container.appendChild(wrapper);
    if (typeof lucide !== 'undefined') lucide.createIcons();
}
