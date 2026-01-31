# AI Integration Guide: Drag-and-Drop Document Extraction

This guide details how to implement an AI-powered "Drag & Drop to Form Fill" feature. It explains the architecture used in the Correspondence Management System so you can replicate this pattern in other web applications (React, Vue, plain HTML/JS, etc.).

## 1. Architecture Overview

The feature follows a **Client-Side AI Pattern**. Instead of sending files to your own backend server for processing, the browser handles the file logic and communicates directly* with an AI API provider.

**The Workflow:**
1.  **User Action**: Drag & Drop a file (Image or PDF).
2.  **File Processing (Browser)**:
    *   **PDFs**: Use `pdf.js` to extract raw text.
    *   **Images**: Convert to Base64 string for Vision AI.
3.  **AI Request**: Send the text or image to a Large Language Model (LLM) or Vision Language Model (VLM).
4.  **AI Response**: The AI analyzes the content and returns a **Structured JSON** object.
5.  **UI Update**: Map the JSON fields to HTML inputs to auto-fill the form.

*> *Note: In production, you often proxy API calls through your backend to hide your API Key. For internal tools or prototypes, client-side calls are acceptable.*

---

## 2. Implementation Steps

### Step 1: The Drag & Drop Zone (UI)

Create a visual zone that listens for browser drag events. You need a hidden `<input type="file">` for the actual file selection logic.

**HTML Structure:**
```html
<div class="drop-zone" id="dropZone">
    <p>Drag files here or click to upload</p>
</div>
<!-- Hidden input for click-to-upload fallback -->
<input type="file" id="fileInput" class="d-none" accept=".pdf,.jpg,.png">
```

**JavaScript Logic:**
```javascript
const dropZone = document.getElementById('dropZone');

// Prevent default browser behavior (opening the file)
['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

// Add visual cue when dragging over
dropZone.addEventListener('dragover', () => dropZone.classList.add('active'));
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('active'));

// Handle the Drop
dropZone.addEventListener('drop', (e) => {
    const dt = e.dataTransfer;
    const files = dt.files;
    handleFile(files[0]); // Process the first file
});
```

### Step 2: Handling Different File Types

AI models digest information differently. Text models read Strings; Vision models "see" Images.

**Strategy:**
*   **Images**: Do *not* use OCR (Optical Character Recognition) libraries like Tesseract if you have access to a Vision AI (like GPT-4o, Gemini, or Nemotron). Vision AIs are smarter—they understand layout and context.
*   **PDFs**: Use a library to extract text, then send that text to the AI.

**Code Pattern:**
```javascript
async function prepareFileForAI(file) {
    if (file.type === 'application/pdf') {
        // 1. Extract text using PDF.js
        const text = await extractPdfText(file); 
        return { type: 'text', content: text };
    } else if (file.type.startsWith('image/')) {
        // 2. Do nothing yet, pass the file object
        return { type: 'image', file: file };
    }
}
```

### Step 3: Preparing the AI Payload

To send an image to an AI API (OpenAI compatible), you usually need to convert it to a **Base64 Data URL**.

```javascript
// Helper: Convert File to Base64
function toBase64(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = () => resolve(reader.result);
        reader.onerror = error => reject(error);
    });
}
```

### Step 4: The AI API Request

This is the core. We construct a prompt that instructs the AI to return **pure JSON**.

**Key Prompting Techniques:**
1.  **Review Role**: "Analyze this document..."
2.  **Strict Output**: "Return ONLY valid JSON."
3.  **Schema Definition**: Explicitly list the keys you want (e.g., `sender_name`, `date`).
4.  **Constraints**: List valid options for dropdowns (e.g., "Priority must be 'Low' or 'High'").

**Vision Request Example (JavaScript):**
```javascript
async function analyzeImage(file) {
    const base64Image = await toBase64(file);
    
    const payload = {
        model: "nvidia/nemotron-nano-12b-v2-vl:free", // Or gpt-4o, gemini-flash
        messages: [{
            role: "user",
            content: [
                { type: "text", text: "Extract: sender, date, subject. Return JSON." },
                { 
                    type: "image_url", 
                    image_url: { url: base64Image } 
                }
            ]
        }]
    };

    const response = await fetch('https://openrouter.ai/api/v1/chat/completions', {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${API_KEY}`,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
    });

    const data = await response.json();
    return parseAIResponse(data.choices[0].message.content);
}
```

### Step 5: Parsing & Auto-Filling

AI responses can sometimes include markdown (e.g., ```json ... ```). You need robust parsing logic.

```javascript
function parseAIResponse(text) {
    // Regex to find the JSON object between curly braces
    const jsonMatch = text.match(/\{[\s\S]*\}/);
    if (!jsonMatch) throw new Error("No JSON found");
    return JSON.parse(jsonMatch[0]);
}

function autoFill(data) {
    // Simple ID mapping
    document.getElementById('senderInput').value = data.sender;
    document.getElementById('subjectInput').value = data.subject;
    
    // Smart Dropdown selection
    const prioritySelect = document.getElementById('prioritySelect');
    // iterate options to find case-insensitive match
    Array.from(prioritySelect.options).forEach(opt => {
        if (opt.value.toLowerCase() === data.priority.toLowerCase()) {
            prioritySelect.value = opt.value;
        }
    });
}
```

## 3. Libraries Used

To replicate this, you likely need two external scripts:

1.  **Bootstrap 5** (Optional): For easy layout and UI.
2.  **PDF.js** (Required for PDFs): To read PDF content in the browser without a server.
    *   `<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>`
3.  **No Library needed for Images**: Standard HTML5 `FileReader` API handles images.

## 4. Checklist for New Apps

If you want to add this to a new app (e.g., an Expense Tracker or Invoice System):

- [ ] **Define your Data Model**: What exactly do you need to extract? (e.g., `total_amount`, `vendor`, `date`).
- [ ] **Prompt Engineering**: Write a prompt that lists these exact fields.
- [ ] **UI Integration**: Add a drop zone div.
- [ ] **API Access**: Get an API Key (OpenAI, OpenRouter, Anthropic, etc.).
- [ ] **Security Check**: If public, move the API call to a backend function (Netlify Function, Vercel API Route, Express server) so your Key isn't exposed in the browser source code.
