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
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1">
                                <i class="bi bi-plus-circle me-2"></i>Register New Correspondence
                            </h2>
                            <p class="text-muted mb-0">Fill in the details to register a new correspondence</p>
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
            <div class="card">
                <div class="card-body">
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

                    <form id="createCorrespondenceForm" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <input type="file" id="correspondence_file" name="correspondence_file" class="d-none" accept=".pdf,.jpg,.jpeg,.png">
                        
                        <!-- Basic Information -->
                        <h5 class="mb-3"><i class="bi bi-info-circle me-2"></i>Basic Information</h5>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="correspondence_number" class="form-label">Correspondence Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="correspondence_number" name="correspondence_number" 
                                       value="<?= esc($suggested_number) ?>" required>
                                <small class="text-muted">Auto-generated or enter manually</small>
                            </div>
                            <div class="col-md-4">
                                <label for="reference_number" class="form-label">External Reference Number</label>
                                <input type="text" class="form-control" id="reference_number" name="reference_number">
                            </div>
                            <div class="col-md-4">
                                <label for="department" class="form-label">Department</label>
                                <input type="text" class="form-control" id="department" name="department" 
                                       placeholder="e.g., HRM, FIN, IT">
                                <small class="text-muted">For department-based numbering</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="subject" class="form-label">Subject/Particulars <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="subject" name="subject" rows="2" required></textarea>
                            </div>
                        </div>

                        <!-- Direction & Type -->
                        <h5 class="mb-3 mt-4"><i class="bi bi-arrow-left-right me-2"></i>Direction & Type</h5>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="correspondence_direction" class="form-label">Direction <span class="text-danger">*</span></label>
                                <select class="form-select" id="correspondence_direction" name="correspondence_direction" required>
                                    <option value="INWARD" selected>Inward</option>
                                    <option value="OUTWARD">Outward</option>
                                    <option value="INTERNAL">Internal</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="correspondence_type" class="form-label">Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="correspondence_type" name="correspondence_type" required>
                                    <option value="">-- Select Type --</option>
                                    <?php if (!empty($correspondence_types)): ?>
                                        <?php foreach ($correspondence_types as $type): ?>
                                            <option value="<?= esc($type['type_number']) ?>">
                                                <?= esc($type['type_number']) ?> - <?= esc($type['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="" disabled>No types available</option>
                                    <?php endif; ?>
                                </select>
                                <small class="text-muted">
                                    <a href="<?= base_url('dakoii/correspondence-types') ?>" target="_blank" class="text-decoration-none">
                                        <i class="bi bi-plus-circle"></i> Manage Types
                                    </a>
                                </small>
                            </div>
                            <div class="col-md-4">
                                <label for="organization_id" class="form-label">Organization</label>
                                <select class="form-select" id="organization_id" name="organization_id">
                                    <option value="">Select Organization</option>
                                    <?php foreach ($organizations as $org): ?>
                                        <option value="<?= $org['id'] ?>"><?= esc($org['org_name']) ?> (<?= esc($org['org_code']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Dates -->
                        <h5 class="mb-3 mt-4"><i class="bi bi-calendar me-2"></i>Dates</h5>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="date_received" class="form-label">Date Received <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="date_received" name="date_received" 
                                       value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label for="original_date" class="form-label">Original Date (on document)</label>
                                <input type="date" class="form-control" id="original_date" name="original_date">
                            </div>
                            <div class="col-md-4">
                                <label for="date_sent" class="form-label">Date Sent (for outward)</label>
                                <input type="date" class="form-control" id="date_sent" name="date_sent">
                            </div>
                        </div>

                        <!-- Sender Information (for Inward) -->
                        <div id="senderSection">
                            <h5 class="mb-3 mt-4"><i class="bi bi-person-fill me-2"></i>Sender Information</h5>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="sender_name" class="form-label">Sender Name</label>
                                    <input type="text" class="form-control" id="sender_name" name="sender_name">
                                </div>
                                <div class="col-md-6">
                                    <label for="sender_organization" class="form-label">Sender Organization</label>
                                    <input type="text" class="form-control" id="sender_organization" name="sender_organization">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="sender_address" class="form-label">Sender Address</label>
                                    <textarea class="form-control" id="sender_address" name="sender_address" rows="2"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label for="sender_contact" class="form-label">Sender Contact</label>
                                    <input type="text" class="form-control" id="sender_contact" name="sender_contact" 
                                           placeholder="Email or Phone">
                                </div>
                            </div>
                        </div>

                        <!-- Recipient Information (for Outward) -->
                        <div id="recipientSection" style="display: none;">
                            <h5 class="mb-3 mt-4"><i class="bi bi-person-check-fill me-2"></i>Recipient Information</h5>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="recipient_name" class="form-label">Recipient Name</label>
                                    <input type="text" class="form-control" id="recipient_name" name="recipient_name">
                                </div>
                                <div class="col-md-6">
                                    <label for="recipient_organization" class="form-label">Recipient Organization</label>
                                    <input type="text" class="form-control" id="recipient_organization" name="recipient_organization">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="recipient_address" class="form-label">Recipient Address</label>
                                    <textarea class="form-control" id="recipient_address" name="recipient_address" rows="2"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label for="dispatch_method" class="form-label">Dispatch Method</label>
                                    <select class="form-select" id="dispatch_method" name="dispatch_method">
                                        <option value="">Select Method</option>
                                        <option value="Email">Email</option>
                                        <option value="Post">Post</option>
                                        <option value="Courier">Courier</option>
                                        <option value="Hand Delivery">Hand Delivery</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Priority & Status -->
                        <h5 class="mb-3 mt-4"><i class="bi bi-flag-fill me-2"></i>Priority & Status</h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="priority" class="form-label">Priority</label>
                                <select class="form-select" id="priority" name="priority">
                                    <option value="LOW">Low</option>
                                    <option value="NORMAL" selected>Normal</option>
                                    <option value="HIGH">High</option>
                                    <option value="URGENT">Urgent</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="REGISTERED" selected>Registered</option>
                                    <option value="REFERRED">Referred</option>
                                    <option value="IN_PROCESS">In Process</option>
                                    <option value="ACTIONED">Actioned</option>
                                    <option value="COMPLETED">Completed</option>
                                    <option value="ARCHIVED">Archived</option>
                                </select>
                            </div>
                        </div>

                        <!-- Filing & Archive -->
                        <h5 class="mb-3 mt-4"><i class="bi bi-folder-fill me-2"></i>Filing & Archive</h5>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="filing_reference" class="form-label">Filing Reference</label>
                                <input type="text" class="form-control" id="filing_reference" name="filing_reference" 
                                       placeholder="e.g., P/F-FKupiaw/F-HRM">
                            </div>
                            <div class="col-md-4">
                                <label for="archive_location" class="form-label">Archive Location</label>
                                <input type="text" class="form-control" id="archive_location" name="archive_location">
                            </div>
                            <div class="col-md-4">
                                <label for="archive_date" class="form-label">Archive Date</label>
                                <input type="date" class="form-control" id="archive_date" name="archive_date">
                            </div>
                        </div>

                        <!-- Remarks -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="remarks" class="form-label">Remarks</label>
                                <textarea class="form-control" id="remarks" name="remarks" rows="3"></textarea>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i>Register Correspondence
                                </button>
                                <a href="<?= base_url('admin/correspondences') ?>" class="btn btn-secondary">
                                    <i class="bi bi-x-circle me-2"></i>Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

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
        const fileInput = document.getElementById('aiFileInput');
        const dropZoneContent = document.getElementById('dropZoneContent');
        const dropZoneLoading = document.getElementById('dropZoneLoading');
        const statusText = document.getElementById('aiStatusText');

        if (!dropZone) return;

        // Click to upload
        dropZone.addEventListener('click', () => fileInput.click());
        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) processFile(e.target.files[0]);
        });

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
                    { type: "text", text: "Analyze this document image. Extract the following fields and return ONLY a distinct JSON object: sender_name, sender_organization, sender_address, sender_contact, subject (summary), original_date (YYYY-MM-DD), priority (LOW, NORMAL, HIGH, URGENT)." },
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
            setVal('sender_name', data.sender_name);
            setVal('sender_organization', data.sender_organization);
            setVal('sender_address', data.sender_address);
            setVal('sender_contact', data.sender_contact);
            setVal('original_date', data.original_date);

            // Handle Priority (Dropdown)
            if (data.priority) {
                const prioritySelect = document.getElementById('priority');
                const pVal = data.priority.toUpperCase();
                if (['LOW', 'NORMAL', 'HIGH', 'URGENT'].includes(pVal)) {
                    prioritySelect.value = pVal;
                }
            }

            // Provide visual feedback
            const inputs = ['subject', 'sender_name', 'sender_organization', 'sender_address', 'sender_contact', 'original_date', 'priority'];
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
<script>
$(document).ready(function() {
    // Toggle sender/recipient sections based on direction
    $('#correspondence_direction').change(function() {
        if ($(this).val() === 'OUTWARD') {
            $('#senderSection').hide();
            $('#recipientSection').show();
        } else {
            $('#senderSection').show();
            $('#recipientSection').hide();
        }
    });

    // Auto-generate correspondence number when department changes
    $('#department').blur(function() {
        var dept = $(this).val();
        if (dept) {
            $.get('<?= base_url('admin/correspondences/generate-number') ?>', {department: dept}, function(response) {
                if (response.success) {
                    $('#correspondence_number').val(response.number);
                }
            });
        }
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
                    alert(response.message);
                    window.location.href = '<?= base_url('admin/correspondences') ?>';
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                var response = xhr.responseJSON;
                if (response && response.csrf_token_value) {
                    $('input[name="<?= csrf_token() ?>"]').val(response.csrf_token_value);
                }
                
                var errorMsg = 'Failed to register correspondence';
                if (response && response.errors) {
                    errorMsg += ':\n';
                    for (var field in response.errors) {
                        errorMsg += '- ' + response.errors[field] + '\n';
                    }
                }
                alert(errorMsg);
            }
        });
    });
});
</script>

<?= $this->endSection() ?>

