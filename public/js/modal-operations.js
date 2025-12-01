/**
 * Modal Operations Handler
 * Handles add/edit/delete operations via modals for modules, submodules, content, and quizzes
 */

// Module Operations
function openModuleModal(courseId, moduleId = null) {
    const modal = new bootstrap.Modal(document.getElementById('moduleModal'));
    const form = document.getElementById('moduleForm');
    const title = document.getElementById('moduleModalLabel');
    const methodInput = document.getElementById('moduleMethod');
    const errorsDiv = document.getElementById('moduleErrors');
    
    errorsDiv.style.display = 'none';
    errorsDiv.innerHTML = '';
    
    if (moduleId) {
        // Edit mode
        title.textContent = 'Edit Module';
        methodInput.value = 'PUT';
        form.action = `/instructor/modules/${moduleId}`;
        
        // Fetch module data
        fetch(`/instructor/modules/${moduleId}/json`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('moduleJudul').value = data.judul || '';
                document.getElementById('moduleDeskripsi').value = data.deskripsi || '';
                document.getElementById('moduleUrutan').value = data.urutan || '';
            })
            .catch(error => {
                console.error('Error fetching module:', error);
                alert('Failed to load module data');
            });
    } else {
        // Add mode
        title.textContent = 'Add Module';
        methodInput.value = 'POST';
        form.action = `/instructor/courses/${courseId}/modules`;
        form.reset();
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
    
    errorsDiv.style.display = 'none';
    errorsDiv.innerHTML = '';
    
    if (subModuleId) {
        // Edit mode
        title.textContent = 'Edit Sub-Module';
        methodInput.value = 'PUT';
        form.action = `/instructor/sub-modules/${subModuleId}`;
        
        // Fetch submodule data
        fetch(`/instructor/sub-modules/${subModuleId}/json`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('subModuleJudul').value = data.judul || '';
                document.getElementById('subModuleDeskripsi').value = data.deskripsi || '';
                document.getElementById('subModuleUrutan').value = data.urutan || '';
            })
            .catch(error => {
                console.error('Error fetching submodule:', error);
                alert('Failed to load submodule data');
            });
    } else {
        // Add mode
        title.textContent = 'Add Sub-Module';
        methodInput.value = 'POST';
        form.action = `/instructor/modules/${moduleId}/sub-modules`;
        form.reset();
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
    
    errorsDiv.style.display = 'none';
    errorsDiv.innerHTML = '';
    
    if (contentId) {
        // Edit mode
        title.textContent = 'Edit Content';
        methodInput.value = 'PUT';
        form.action = `/instructor/contents/${contentId}`;
        
        // Fetch content data
        fetch(`/instructor/contents/${contentId}/json`)
            .then(response => response.json())
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
                }
                
                toggleContentFields();
            })
            .catch(error => {
                console.error('Error fetching content:', error);
                alert('Failed to load content data');
            });
    } else {
        // Add mode
        title.textContent = 'Add Content';
        methodInput.value = 'POST';
        form.action = `/instructor/sub-modules/${subModuleId}/contents`;
        form.reset();
        document.getElementById('currentFile').style.display = 'none';
        toggleContentFields();
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
    
    errorsDiv.style.display = 'none';
    errorsDiv.innerHTML = '';
    
    if (quizId) {
        // Edit mode
        title.textContent = 'Edit Quiz';
        methodInput.value = 'PUT';
        form.action = `/instructor/quizzes/${quizId}`;
        
        // Fetch quiz data
        fetch(`/instructor/quizzes/${quizId}/json`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('quizJudul').value = data.judul || '';
                document.getElementById('quizDeskripsi').value = data.deskripsi || '';
                document.getElementById('quizNilaiMinimum').value = data.nilai_minimum || 60;
                document.getElementById('quizMaxAttempts').value = data.max_attempts || 3;
            })
            .catch(error => {
                console.error('Error fetching quiz:', error);
                alert('Failed to load quiz data');
            });
    } else {
        // Add mode
        title.textContent = 'Add Quiz';
        methodInput.value = 'POST';
        form.action = `/instructor/sub-modules/${subModuleId}/quizzes`;
        form.reset();
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
    const formData = new FormData(form);
    const method = formData.get('_method') || 'POST';
    const url = form.action;
    const errorsDiv = document.getElementById(`${type}Errors`);
    
    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = 'Saving...';
    errorsDiv.style.display = 'none';
    
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
        });
    })
    .then(({ status, data }) => {
        if (status === 200 || status === 201) {
            // Success
            if (data.success !== false) {
                const modal = bootstrap.Modal.getInstance(form.closest('.modal'));
                modal.hide();
                location.reload();
            } else {
                // Show errors even if status is 200 but success is false
                showFormErrors(data, errorsDiv);
            }
        } else if (status === 422) {
            // Validation errors
            showFormErrors(data, errorsDiv);
        } else {
            // Other errors
            showFormErrors(data, errorsDiv);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        errorsDiv.innerHTML = '<ul class="mb-0"><li>An error occurred. Please try again.</li></ul>';
        errorsDiv.style.display = 'block';
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    });
}

function showFormErrors(data, errorsDiv) {
    let errorHtml = '<ul class="mb-0">';
    if (data.errors) {
        Object.keys(data.errors).forEach(key => {
            data.errors[key].forEach(error => {
                errorHtml += `<li>${error}</li>`;
            });
        });
    } else if (data.message) {
        errorHtml += `<li>${data.message}</li>`;
    } else {
        errorHtml += '<li>An error occurred. Please try again.</li>';
    }
    errorHtml += '</ul>';
    errorsDiv.innerHTML = errorHtml;
    errorsDiv.style.display = 'block';
    
    // Scroll to errors
    errorsDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function handleDeleteSubmit(form) {
    const url = form.action;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    
    submitBtn.disabled = true;
    submitBtn.textContent = 'Deleting...';
    
    fetch(url, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const modal = bootstrap.Modal.getInstance(form.closest('.modal'));
            modal.hide();
            location.reload();
        } else {
            alert(data.message || 'Failed to delete');
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    });
}

