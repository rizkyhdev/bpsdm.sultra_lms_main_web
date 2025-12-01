/**
 * Modal Operations Handler
 * Handles add/edit/delete operations via modals for modules, submodules, content, and quizzes
 * Following LMS best practices for UX and error handling
 */

// Toast notification system
function showToast(message, type = 'success', duration = 4000) {
    const containerId = 'toast-container';
    let container = document.getElementById(containerId);
    
    if (!container) {
        container = document.createElement('div');
        container.id = containerId;
        container.className = 'position-fixed top-0 end-0 p-3';
        container.style.zIndex = '1080';
        document.body.appendChild(container);
    }

    const toastId = 'toast-' + Date.now();
    const bgClass = type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : 'bg-info';
    const icon = type === 'success' ? 'bi-check-circle' : type === 'error' ? 'bi-exclamation-triangle' : 'bi-info-circle';
    
    const toast = document.createElement('div');
    toast.id = toastId;
    toast.className = `toast align-items-center text-white ${bgClass} border-0 show`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi ${icon} me-2"></i>${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;

    container.appendChild(toast);
    
    const bsToast = new bootstrap.Toast(toast, { autohide: true, delay: duration });
    bsToast.show();

    toast.addEventListener('hidden.bs.toast', function() {
        toast.remove();
    });
}

// Field-level error display
function showFieldError(fieldId, message) {
    const field = document.getElementById(fieldId);
    if (!field) return;
    
    field.classList.add('is-invalid');
    let feedback = field.parentElement.querySelector('.invalid-feedback');
    
    if (!feedback) {
        feedback = document.createElement('div');
        feedback.className = 'invalid-feedback';
        field.parentElement.appendChild(feedback);
    }
    
    feedback.textContent = message;
}

function clearFieldErrors(form) {
    const invalidFields = form.querySelectorAll('.is-invalid');
    invalidFields.forEach(field => {
        field.classList.remove('is-invalid');
        const feedback = field.parentElement.querySelector('.invalid-feedback');
        if (feedback) feedback.remove();
    });
}

// Module Operations
function openModuleModal(courseId, moduleId = null) {
    const modal = new bootstrap.Modal(document.getElementById('moduleModal'));
    const form = document.getElementById('moduleForm');
    const title = document.getElementById('moduleModalLabel');
    const methodInput = document.getElementById('moduleMethod');
    const errorsDiv = document.getElementById('moduleErrors');
    const submitBtn = form.querySelector('button[type="submit"]');
    
    // Reset form state
    errorsDiv.style.display = 'none';
    errorsDiv.innerHTML = '';
    clearFieldErrors(form);
    submitBtn.disabled = false;
    submitBtn.innerHTML = '<i class="bi bi-save me-1"></i>Save';
    
    if (moduleId) {
        // Edit mode
        title.textContent = 'Edit Module';
        methodInput.value = 'PUT';
        form.action = `/instructor/modules/${moduleId}`;
        submitBtn.innerHTML = '<i class="bi bi-save me-1"></i>Update';
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Loading...';
        
        // Fetch module data
        fetch(`/instructor/modules/${moduleId}/json`)
            .then(response => {
                if (!response.ok) throw new Error('Failed to fetch');
                return response.json();
            })
            .then(data => {
                document.getElementById('moduleJudul').value = data.judul || '';
                document.getElementById('moduleDeskripsi').value = data.deskripsi || '';
                document.getElementById('moduleUrutan').value = data.urutan || '';
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-save me-1"></i>Update';
            })
            .catch(error => {
                console.error('Error fetching module:', error);
                showToast('Failed to load module data', 'error');
                modal.hide();
            });
    } else {
        // Add mode
        title.textContent = 'Add Module';
        methodInput.value = 'POST';
        form.action = `/instructor/courses/${courseId}/modules`;
        form.reset();
        submitBtn.innerHTML = '<i class="bi bi-plus-circle me-1"></i>Create';
    }
    
    modal.show();
}

function openModuleDeleteModal(moduleId) {
    const modal = new bootstrap.Modal(document.getElementById('moduleDeleteModal'));
    const form = document.getElementById('moduleDeleteForm');
    form.action = `/instructor/modules/${moduleId}`;
    modal.show();
}

// SubModule Operations
function openSubModuleModal(moduleId, subModuleId = null) {
    const modal = new bootstrap.Modal(document.getElementById('subModuleModal'));
    const form = document.getElementById('subModuleForm');
    const title = document.getElementById('subModuleModalLabel');
    const methodInput = document.getElementById('subModuleMethod');
    const errorsDiv = document.getElementById('subModuleErrors');
    const submitBtn = form.querySelector('button[type="submit"]');
    
    // Reset form state
    errorsDiv.style.display = 'none';
    errorsDiv.innerHTML = '';
    clearFieldErrors(form);
    submitBtn.disabled = false;
    submitBtn.innerHTML = '<i class="bi bi-save me-1"></i>Save';
    
    if (subModuleId) {
        // Edit mode
        title.textContent = 'Edit Sub-Module';
        methodInput.value = 'PUT';
        form.action = `/instructor/sub-modules/${subModuleId}`;
        submitBtn.innerHTML = '<i class="bi bi-save me-1"></i>Update';
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Loading...';
        
        // Fetch submodule data
        fetch(`/instructor/sub-modules/${subModuleId}/json`)
            .then(response => {
                if (!response.ok) throw new Error('Failed to fetch');
                return response.json();
            })
            .then(data => {
                document.getElementById('subModuleJudul').value = data.judul || '';
                document.getElementById('subModuleDeskripsi').value = data.deskripsi || '';
                document.getElementById('subModuleUrutan').value = data.urutan || '';
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-save me-1"></i>Update';
            })
            .catch(error => {
                console.error('Error fetching submodule:', error);
                showToast('Failed to load submodule data', 'error');
                modal.hide();
            });
    } else {
        // Add mode
        title.textContent = 'Add Sub-Module';
        methodInput.value = 'POST';
        form.action = `/instructor/modules/${moduleId}/sub-modules`;
        form.reset();
        submitBtn.innerHTML = '<i class="bi bi-plus-circle me-1"></i>Create';
    }
    
    modal.show();
}

function openSubModuleDeleteModal(subModuleId) {
    const modal = new bootstrap.Modal(document.getElementById('subModuleDeleteModal'));
    const form = document.getElementById('subModuleDeleteForm');
    form.action = `/instructor/sub-modules/${subModuleId}`;
    modal.show();
}

// Content Operations
function openContentModal(subModuleId, contentId = null) {
    const modal = new bootstrap.Modal(document.getElementById('contentModal'));
    const form = document.getElementById('contentForm');
    const title = document.getElementById('contentModalLabel');
    const methodInput = document.getElementById('contentMethod');
    const errorsDiv = document.getElementById('contentErrors');
    const submitBtn = form.querySelector('button[type="submit"]');
    
    // Reset form state
    errorsDiv.style.display = 'none';
    errorsDiv.innerHTML = '';
    clearFieldErrors(form);
    submitBtn.disabled = false;
    submitBtn.innerHTML = '<i class="bi bi-save me-1"></i>Save';
    
    if (contentId) {
        // Edit mode
        title.textContent = 'Edit Content';
        methodInput.value = 'PUT';
        form.action = `/instructor/contents/${contentId}`;
        submitBtn.innerHTML = '<i class="bi bi-save me-1"></i>Update';
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Loading...';
        
        // Fetch content data
        fetch(`/instructor/contents/${contentId}/json`)
            .then(response => {
                if (!response.ok) throw new Error('Failed to fetch');
                return response.json();
            })
            .then(data => {
                document.getElementById('contentJudul').value = data.judul || '';
                document.getElementById('contentTipe').value = data.tipe || '';
                document.getElementById('contentUrutan').value = data.urutan || '';
                document.getElementById('contentHtmlContent').value = data.html_content || '';
                document.getElementById('contentYoutubeUrl').value = data.youtube_url || '';
                document.getElementById('contentExternalUrl').value = data.external_url || '';
                document.getElementById('contentRequiredDuration').value = data.required_duration || '';
                
                if (data.file_path) {
                    document.getElementById('currentFile').style.display = 'block';
                    document.getElementById('currentFileName').textContent = data.file_path.split('/').pop();
                } else {
                    document.getElementById('currentFile').style.display = 'none';
                }
                
                toggleContentFields();
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-save me-1"></i>Update';
            })
            .catch(error => {
                console.error('Error fetching content:', error);
                showToast('Failed to load content data', 'error');
                modal.hide();
            });
    } else {
        // Add mode
        title.textContent = 'Add Content';
        methodInput.value = 'POST';
        form.action = `/instructor/sub-modules/${subModuleId}/contents`;
        form.reset();
        document.getElementById('currentFile').style.display = 'none';
        document.getElementById('contentUrutan').value = '1';
        toggleContentFields();
        submitBtn.innerHTML = '<i class="bi bi-plus-circle me-1"></i>Create';
    }
    
    modal.show();
}

function openContentDeleteModal(contentId) {
    const modal = new bootstrap.Modal(document.getElementById('contentDeleteModal'));
    const form = document.getElementById('contentDeleteForm');
    form.action = `/instructor/contents/${contentId}`;
    modal.show();
}

// Quiz Operations
function openQuizModal(subModuleId, quizId = null) {
    const modal = new bootstrap.Modal(document.getElementById('quizModal'));
    const form = document.getElementById('quizForm');
    const title = document.getElementById('quizModalLabel');
    const methodInput = document.getElementById('quizMethod');
    const errorsDiv = document.getElementById('quizErrors');
    const submitBtn = form.querySelector('button[type="submit"]');
    
    // Reset form state
    errorsDiv.style.display = 'none';
    errorsDiv.innerHTML = '';
    clearFieldErrors(form);
    submitBtn.disabled = false;
    submitBtn.innerHTML = '<i class="bi bi-save me-1"></i>Save';
    
    if (quizId) {
        // Edit mode
        title.textContent = 'Edit Quiz';
        methodInput.value = 'PUT';
        form.action = `/instructor/quizzes/${quizId}`;
        submitBtn.innerHTML = '<i class="bi bi-save me-1"></i>Update';
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Loading...';
        
        // Fetch quiz data
        fetch(`/instructor/quizzes/${quizId}/json`)
            .then(response => {
                if (!response.ok) throw new Error('Failed to fetch');
                return response.json();
            })
            .then(data => {
                document.getElementById('quizJudul').value = data.judul || '';
                document.getElementById('quizDeskripsi').value = data.deskripsi || '';
                document.getElementById('quizNilaiMinimum').value = data.nilai_minimum || 60;
                document.getElementById('quizMaxAttempts').value = data.max_attempts || 3;
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-save me-1"></i>Update';
            })
            .catch(error => {
                console.error('Error fetching quiz:', error);
                showToast('Failed to load quiz data', 'error');
                modal.hide();
            });
    } else {
        // Add mode
        title.textContent = 'Add Quiz';
        methodInput.value = 'POST';
        form.action = `/instructor/sub-modules/${subModuleId}/quizzes`;
        form.reset();
        document.getElementById('quizNilaiMinimum').value = '60';
        document.getElementById('quizMaxAttempts').value = '3';
        submitBtn.innerHTML = '<i class="bi bi-plus-circle me-1"></i>Create';
    }
    
    modal.show();
}

function openQuizDeleteModal(quizId) {
    const modal = new bootstrap.Modal(document.getElementById('quizDeleteModal'));
    const form = document.getElementById('quizDeleteForm');
    form.action = `/instructor/quizzes/${quizId}`;
    modal.show();
}

// Handle form submissions via AJAX
document.addEventListener('DOMContentLoaded', function() {
    // Module form
    const moduleForm = document.getElementById('moduleForm');
    if (moduleForm) {
        moduleForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleFormSubmit(this, 'module');
        });
    }
    
    // SubModule form
    const subModuleForm = document.getElementById('subModuleForm');
    if (subModuleForm) {
        subModuleForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleFormSubmit(this, 'subModule');
        });
    }
    
    // Content form
    const contentForm = document.getElementById('contentForm');
    if (contentForm) {
        contentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleFormSubmit(this, 'content');
        });
    }
    
    // Quiz form
    const quizForm = document.getElementById('quizForm');
    if (quizForm) {
        quizForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleFormSubmit(this, 'quiz');
        });
    }
    
    // Delete forms
    const deleteForms = ['moduleDeleteForm', 'subModuleDeleteForm', 'contentDeleteForm', 'quizDeleteForm'];
    deleteForms.forEach(formId => {
        const form = document.getElementById(formId);
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                handleDeleteSubmit(this);
            });
        }
    });
});

function handleFormSubmit(form, type) {
    // Client-side validation
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const formData = new FormData(form);
    const method = formData.get('_method') || 'POST';
    const url = form.action;
    const errorsDiv = document.getElementById(`${type}Errors`);
    
    // Clear previous errors
    errorsDiv.style.display = 'none';
    errorsDiv.innerHTML = '';
    clearFieldErrors(form);
    
    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalHtml = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving...';
    
    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => {
        return response.json().then(data => {
            return { status: response.status, data: data };
        }).catch(() => {
            return { status: response.status, data: { message: 'An error occurred' } };
        });
    })
    .then(({ status, data }) => {
        if (status === 200 || status === 201) {
            // Success
            if (data.success !== false) {
                const modal = bootstrap.Modal.getInstance(form.closest('.modal'));
                const action = method === 'PUT' ? 'updated' : 'created';
                const entityName = type === 'module' ? 'Module' : 
                                  type === 'subModule' ? 'Sub-module' :
                                  type === 'content' ? 'Content' : 'Quiz';
                
                modal.hide();
                showToast(`${entityName} ${action} successfully!`, 'success');
                
                // Reload after a short delay to show toast
                setTimeout(() => {
                    location.reload();
                }, 500);
            } else {
                // Show errors even if status is 200 but success is false
                showFormErrors(data, errorsDiv, form, type);
            }
        } else if (status === 422) {
            // Validation errors
            showFormErrors(data, errorsDiv, form, type);
        } else {
            // Other errors
            showFormErrors(data, errorsDiv, form, type);
            showToast(data.message || 'An error occurred. Please try again.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Network error. Please check your connection and try again.', 'error');
        errorsDiv.innerHTML = '<ul class="mb-0"><li>An error occurred. Please try again.</li></ul>';
        errorsDiv.style.display = 'block';
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalHtml;
    });
}

function showFormErrors(data, errorsDiv, form, type) {
    let errorHtml = '<ul class="mb-0">';
    let hasFieldErrors = false;
    
    if (data.errors) {
        Object.keys(data.errors).forEach(key => {
            const fieldId = getFieldId(key, type);
            const errorMessages = data.errors[key];
            
            // Show field-level errors
            if (fieldId && errorMessages.length > 0) {
                showFieldError(fieldId, errorMessages[0]);
                hasFieldErrors = true;
            }
            
            // Add to general error list
            errorMessages.forEach(error => {
                errorHtml += `<li>${error}</li>`;
            });
        });
    } else if (data.message) {
        errorHtml += `<li>${data.message}</li>`;
    } else {
        errorHtml += '<li>An error occurred. Please try again.</li>';
    }
    
    errorHtml += '</ul>';
    
    // Only show general error div if there are non-field errors or no field mapping
    if (!hasFieldErrors || errorHtml.includes('<li>')) {
        errorsDiv.innerHTML = errorHtml;
        errorsDiv.style.display = 'block';
        errorsDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}

function getFieldId(key, type) {
    const fieldMap = {
        'module': {
            'judul': 'moduleJudul',
            'deskripsi': 'moduleDeskripsi',
            'urutan': 'moduleUrutan'
        },
        'subModule': {
            'judul': 'subModuleJudul',
            'deskripsi': 'subModuleDeskripsi',
            'urutan': 'subModuleUrutan'
        },
        'content': {
            'judul': 'contentJudul',
            'tipe': 'contentTipe',
            'urutan': 'contentUrutan',
            'html_content': 'contentHtmlContent',
            'youtube_url': 'contentYoutubeUrl',
            'external_url': 'contentExternalUrl',
            'file_path': 'contentFilePath',
            'required_duration': 'contentRequiredDuration'
        },
        'quiz': {
            'judul': 'quizJudul',
            'deskripsi': 'quizDeskripsi',
            'nilai_minimum': 'quizNilaiMinimum',
            'max_attempts': 'quizMaxAttempts'
        }
    };
    
    return fieldMap[type] && fieldMap[type][key] ? fieldMap[type][key] : null;
}

function handleDeleteSubmit(form) {
    const url = form.action;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalHtml = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Deleting...';
    
    fetch(url, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(response => {
        return response.json().then(data => {
            return { status: response.status, data: data };
        }).catch(() => {
            return { status: response.status, data: { message: 'An error occurred' } };
        });
    })
    .then(({ status, data }) => {
        if (status === 200 && data.success) {
            const modal = bootstrap.Modal.getInstance(form.closest('.modal'));
            modal.hide();
            showToast('Item deleted successfully!', 'success');
            
            // Reload after a short delay to show toast
            setTimeout(() => {
                location.reload();
            }, 500);
        } else {
            showToast(data.message || 'Failed to delete item', 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalHtml;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Network error. Please check your connection and try again.', 'error');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalHtml;
    });
}

