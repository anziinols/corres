<?= $this->extend('templates/admin_template') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('admin/correspondences') ?>">Correspondences</a></li>
            <li class="breadcrumb-item active">Register New</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1 fw-bold text-primary">
                                <i class="bi bi-plus-circle me-2"></i>New Correspondence
                            </h2>
                            <p class="text-muted mb-0">Create a new record</p>
                        </div>
                        <div>
                            <a href="<?= base_url('admin/correspondences') ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Form -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form id="createCorrespondenceForm" enctype="multipart/form-data">
                        <?= csrf_field() ?>

                        <!-- 1. Attachment (AI Auto-Fill) -->
                        <h5 class="text-primary border-bottom pb-2 mb-3">Upload Document</h5>
                        <div id="aiDropZone" class="mb-4 p-5 text-center border rounded bg-light" style="border: 2px dashed #0d6efd; cursor: pointer; transition: all 0.2s;">
                            <div id="dropZoneContent">
                                <i class="bi bi-cloud-arrow-up display-4 text-primary mb-3"></i>
                                <h5 class="fw-bold">Drag & Drop Document Here</h5>
                                <p class="text-muted mb-0">Upload PDF or Image to Auto-Fill Form using AI</p>
                                <small class="text-muted">(Supports .pdf, .jpg, .png)</small>
                                <div id="fileNameDisplay" class="mt-2 fw-bold text-success d-none"></div>
                            </div>
                            <div id="dropZoneLoading" class="d-none">
                                <div class="spinner-border text-primary mb-3" role="status"></div>
                                <h5 class="fw-bold animate-pulse">Analyzing Document...</h5>
                                <p class="text-muted" id="aiStatusText">Extracting information...</p>
                            </div>
                        </div>
                        <input type="file" id="correspondence_file" name="correspondence_file" class="d-none" accept=".pdf,.jpg,.jpeg,.png">

                        <!-- 2. Header Details -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="correspondence_number" class="form-label text-uppercase text-secondary fw-bold small">Correspondence Number</label>
                                <input type="text" class="form-control bg-light" id="correspondence_number" name="correspondence_number" 
                                       value="<?= esc($suggested_number) ?>" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="date_received" class="form-label text-uppercase text-secondary fw-bold small">Date Received</label>
                                <input type="date" class="form-control" id="date_received" name="date_received" 
                                       value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label for="correspondence_direction" class="form-label text-uppercase text-secondary fw-bold small">Direction</label>
                                <select class="form-select" id="correspondence_direction" name="correspondence_direction" required>
                                    <option value="INWARD" selected>Inward</option>
                                    <option value="OUTWARD">Outward</option>
                                    <option value="INTERNAL">Internal</option>
                                </select>
                            </div>
                        </div>

                        <!-- 3. Main Content -->
                        <h5 class="text-primary border-bottom pb-2 mb-3 mt-4">Main Content</h5>
                        <div class="mb-3">
                            <label for="subject" class="form-label fw-bold">Subject</label>
                            <input type="text" class="form-control form-control-lg" id="subject" name="subject" placeholder="Headline / Title of the file" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="remarks" class="form-label fw-bold">Brief Description</label>
                                <textarea class="form-control" id="remarks" name="remarks" rows="3" placeholder="Short summary of contents..."></textarea>
                            </div>
                            <div class="col-md-4">
                                <label for="correspondence_type" class="form-label fw-bold">Document Type</label>
                                <select class="form-select" id="correspondence_type" name="correspondence_type" required>
                                    <option value="">Select Type</option>
                                    <?php foreach ($correspondence_types as $type): ?>
                                        <option value="<?= esc($type['type_number']) ?>"><?= esc($type['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- 4. Organization & Group -->
                        <h5 class="text-primary border-bottom pb-2 mb-3 mt-4">Organization & Group</h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="organization_id" class="form-label fw-bold">Organization</label>
                                <select class="form-select" id="organization_id" name="organization_id">
                                    <option value="">Select Organization</option>
                                    <?php foreach ($organizations as $org): ?>
                                        <option value="<?= $org['id'] ?>"><?= esc($org['org_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="group_id" class="form-label fw-bold">Group / Department</label>
                                <select class="form-select" id="group_id" name="group_id">
                                    <option value="">Select Group</option>
                                    <?php foreach ($groups as $group): ?>
                                        <option value="<?= $group['id'] ?>" data-org="<?= $group['organization_id'] ?>"><?= esc($group['group_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- 5. Sender & Status -->
                        <h5 class="text-primary border-bottom pb-2 mb-3 mt-4">Sender & Status</h5>
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="sender_name" class="form-label fw-bold">Sender / Organization</label>
                                <input type="text" class="form-control" id="sender_name" name="sender_name" placeholder="e.g. John Smith - Dakoii Systems">
                            </div>
                            <div class="col-md-4">
                                <label for="priority" class="form-label fw-bold">Priority</label>
                                <select class="form-select" id="priority" name="priority">
                                    <option value="NORMAL" selected>Normal</option>
                                    <option value="URGENT">Urgent</option>
                                    <option value="CONFIDENTIAL">Confidential</option>
                                </select>
                            </div>
                        </div>

                        <!-- 6. Context & Relationships -->
                        <h5 class="text-primary border-bottom pb-2 mb-3 mt-4">Context & Relationships</h5>
                        <div class="mb-3">
                            <label for="linked_correspondence_ids" class="form-label fw-bold">Linked Correspondence</label>
                            <select class="form-select" id="linked_correspondence_ids" name="linked_correspondence_ids[]" multiple="multiple">
                                <?php foreach ($existing_correspondences as $existing): ?>
                                    <option value="<?= $existing['id'] ?>">
                                        <?= esc($existing['correspondence_number']) ?> - <?= esc($existing['subject']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Search for existing files to tag relationships (e.g. replies, related docs).</small>
                        </div>

                        <!-- Hidden Archive Fields (populated via modal if status is ARCHIVED) -->
                        <input type="hidden" id="filing_reference" name="filing_reference" value="">
                        <input type="hidden" id="archive_location" name="archive_location" value="">
                        <input type="hidden" id="archive_date" name="archive_date" value="">

                        <!-- Submit Buttons -->
                        <div class="row mt-4">
                            <div class="col-12 text-end">
                                <a href="<?= base_url('admin/correspondences') ?>" class="btn btn-link text-secondary text-decoration-none me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary btn-lg px-5">
                                    <i class="bi bi-check-circle me-2"></i>Register
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>
<script>
    // AI Configuration
    const AI_API_KEY = "<?= esc($ai_api_key ?? '') ?>";
    const AI_MODEL = "<?= esc($ai_model ?? 'nvidia/nemotron-nano-12b-v2-vl:free') ?>";

    // Initialize PDF.js worker
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';

    document.addEventListener('DOMContentLoaded', function() {
        // Drag & Drop Logic
        const dropZone = document.getElementById('aiDropZone');
        const fileInput = document.getElementById('correspondence_file'); // The form input
        const dropZoneContent = document.getElementById('dropZoneContent');
        const dropZoneLoading = document.getElementById('dropZoneLoading');
        const statusText = document.getElementById('aiStatusText');
        const fileNameDisplay = document.getElementById('fileNameDisplay');

        if (!dropZone) return;

        // Click to upload (Trigger the actual form input)
        dropZone.addEventListener('click', () => fileInput.click());
        
        // Handle Standard File Selection
        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                handleFileSelection(e.target.files[0]);
            }
        });

        // Drag events
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => dropZone.style.borderColor = '#0b5ed7', false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => dropZone.style.borderColor = '#0d6efd', false);
        });

        dropZone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            
            if (files.length > 0) {
                const file = files[0];
                // Manually assign to input for strict file retention
                fileInput.files = files;
                handleFileSelection(file);
            }
        });

        function handleFileSelection(file) {
            // Update UI
            fileNameDisplay.innerText = "Attached: " + file.name;
            fileNameDisplay.classList.remove('d-none');
            
            // Process AI
            processFile(file);
        }

        async function processFile(file) {
            if (!AI_API_KEY || AI_API_KEY === 'your_api_key_here') {
                alert('Please configure the AI_API_KEY in your .env file to use this feature.');
                return;
            }

            // Show Loading
            dropZoneContent.classList.add('d-none');
            dropZoneLoading.classList.remove('d-none');
            statusText.innerText = "Analyzing file type...";

            try {
                let aiContent = null;
                
                if (file.type === 'application/pdf') {
                    statusText.innerText = "Rendering PDF as Image for Vision AI...";
                    const base64Image = await pdfToImage(file);
                     aiContent = { 
                        type: 'image_url', 
                        image_url: { url: base64Image } 
                    };
                } else if (file.type.startsWith('image/')) {
                    statusText.innerText = "Processing image...";
                    const base64 = await toBase64(file);
                    aiContent = { 
                        type: 'image_url', 
                        image_url: { url: base64 } 
                    };
                } else {
                    throw new Error("Unsupported file type. Please upload PDF or Image.");
                }

                statusText.innerText = "Waiting for AI analysis...";
                const result = await callAI(aiContent);
                
                statusText.innerText = "Filling form...";
                autoFillForm(result);
                
                // Reset UI after short delay
                setTimeout(() => {
                    dropZoneContent.classList.remove('d-none');
                    dropZoneLoading.classList.add('d-none');
                }, 1000);

            } catch (error) {
                console.error(error);
                alert("Error: " + error.message);
                dropZoneContent.classList.remove('d-none');
                dropZoneLoading.classList.add('d-none');
            }
        }

        async function pdfToImage(file) {
            const arrayBuffer = await file.arrayBuffer();
            const pdf = await pdfjsLib.getDocument({ data: arrayBuffer }).promise;
            const page = await pdf.getPage(1); // Get first page only
            
            const viewport = page.getViewport({ scale: 1.5 });
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            canvas.height = viewport.height;
            canvas.width = viewport.width;

            await page.render({ canvasContext: context, viewport: viewport }).promise;
            return canvas.toDataURL('image/jpeg', 0.8);
        }

        function toBase64(file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = () => resolve(reader.result);
                reader.onerror = error => reject(error);
            });
        }

        async function callAI(contentPart) {
            const messages = [{
                role: "user",
                content: [
                    { type: "text", text: "Analyze this document image. Extract the following fields and return ONLY a distinct JSON object: sender_name (person and/or organization), subject (headline), description (brief summary of content), original_date (YYYY-MM-DD), priority (NORMAL, URGENT, CONFIDENTIAL)." },
                    contentPart
                ]
            }];

            const response = await fetch('https://openrouter.ai/api/v1/chat/completions', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${AI_API_KEY}`,
                    'Content-Type': 'application/json',
                    'HTTP-Referer': window.location.origin,
                },
                body: JSON.stringify({
                    model: AI_MODEL,
                    messages: messages,
                    temperature: 0.1
                })
            });

            if (!response.ok) {
                const err = await response.json();
                throw new Error(err.error?.message || 'AI API Request Failed');
            }

            const data = await response.json();
            const rawContent = data.choices[0].message.content;
            return parseAIResponse(rawContent);
        }

        function parseAIResponse(text) {
            try {
                // Find JSON object
                const jsonMatch = text.match(/\{[\s\S]*\}/);
                if (!jsonMatch) throw new Error("No JSON found in response");
                return JSON.parse(jsonMatch[0]);
            } catch (e) {
                console.error("Failed to parse AI response:", text);
                throw new Error("Failed to parse AI response");
            }
        }

        function autoFillForm(data) {
            // Helper to set value if exists
            const setVal = (id, val) => {
                const el = document.getElementById(id);
                if (el && val) el.value = val;
            };

            setVal('subject', data.subject);
            setVal('sender_name', data.sender_name); // Mapped to single field "Sender / Organization"
            setVal('remarks', data.description); // Mapped to "Brief Description"
            setVal('original_date', data.original_date);

            // Handle Priority (Dropdown)
            if (data.priority) {
                const prioritySelect = document.getElementById('priority');
                const pVal = data.priority.toUpperCase();
                if (['NORMAL', 'URGENT', 'CONFIDENTIAL'].includes(pVal)) {
                    prioritySelect.value = pVal;
                } else if (pVal === 'HIGH') {
                    prioritySelect.value = 'URGENT';
                }
            }

            // Provide visual feedback
            const inputs = ['subject', 'sender_name', 'remarks', 'original_date', 'priority'];
            inputs.forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.classList.add('bg-success');
                    el.classList.add('bg-opacity-10');
                    setTimeout(() => {
                        el.classList.remove('bg-success');
                        el.classList.remove('bg-opacity-10');
                    }, 2000);
                }
            });
        }
    });
</script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<!-- Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Toastr
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "3000"
    };

    // Initialize Select2
    $('#linked_correspondence_ids').select2({
        theme: 'bootstrap-5',
        placeholder: 'Search for files...',
        allowClear: true
    });

    // Form submission
    $('#createCorrespondenceForm').submit(function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);

        $.ajax({
            url: '<?= base_url('admin/correspondences') ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(response) {
                // Update CSRF token
                $('input[name="<?= csrf_token() ?>"]').val(response.csrf_token_value);
                
                if (response.success) {
                    toastr.success(response.message);
                    setTimeout(() => {
                        window.location.href = '<?= base_url('admin/correspondences') ?>';
                    }, 1000);
                } else {
                    toastr.error('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                var response = xhr.responseJSON;
                if (response && response.csrf_token_value) {
                    $('input[name="<?= csrf_token() ?>"]').val(response.csrf_token_value);
                }
                
                var errorMsg = 'Failed to register correspondence';
                if (response && response.errors) {
                    errorMsg += ':<br>';
                    for (var field in response.errors) {
                        errorMsg += '- ' + response.errors[field] + '<br>';
                    }
                }
                toastr.error(errorMsg);
            }
        });
    });
});
</script>

<?= $this->endSection() ?>

