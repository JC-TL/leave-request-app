// State Management
let concepts = [];
let currentConcept = null;
let completedConcepts = new Set(JSON.parse(localStorage.getItem('completedConcepts') || '[]'));
let expandedSections = new Set(JSON.parse(localStorage.getItem('expandedSections') || '[]'));

// DOM Elements
const navMenu = document.getElementById('navMenu');
const welcomeScreen = document.getElementById('welcomeScreen');
const conceptContent = document.getElementById('conceptContent');
const searchInput = document.getElementById('searchInput');
const progressFill = document.getElementById('progressFill');
const progressPercent = document.getElementById('progressPercent');

// Initialize App
function initApp() {
    try {
        // Use embedded concepts data from concepts-data.js (no fetch needed, avoids CORS issues)
        if (typeof CONCEPTS_DATA === 'undefined') {
            throw new Error('CONCEPTS_DATA not loaded. Make sure concepts-data.js is loaded before app.js');
        }
        concepts = CONCEPTS_DATA.concepts;
        
        renderNavigation();
        updateProgress();
        setupEventListeners();
    } catch (error) {
        console.error('Error initializing app:', error);
        showError('Failed to load study guide: ' + error.message);
    }
}

// Render Navigation Menu
function renderNavigation() {
    navMenu.innerHTML = '';
    
    concepts.forEach(concept => {
        const navItem = document.createElement('div');
        navItem.className = `nav-item ${completedConcepts.has(concept.id) ? 'completed' : ''}`;
        navItem.dataset.conceptId = concept.id;
        
        navItem.innerHTML = `
            <span class="nav-item-icon">${concept.icon}</span>
            <span class="nav-item-text">${concept.title.replace(/^[^\s]+\s/, '')}</span>
            ${completedConcepts.has(concept.id) ? '<span class="nav-item-check">✓</span>' : ''}
        `;
        
        navItem.addEventListener('click', () => showConcept(concept.id));
        navMenu.appendChild(navItem);
    });
}

// Show Concept
function showConcept(conceptId) {
    const concept = concepts.find(c => c.id === conceptId);
    if (!concept) return;
    
    currentConcept = concept;
    welcomeScreen.style.display = 'none';
    conceptContent.style.display = 'block';
    conceptContent.innerHTML = renderConceptContent(concept);
    
    // Update active nav item
    document.querySelectorAll('.nav-item').forEach(item => {
        item.classList.remove('active');
        if (item.dataset.conceptId === conceptId) {
            item.classList.add('active');
        }
    });
    
    // Restore expanded sections
    restoreExpandedSections();
    
    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
    
    // Setup copy buttons
    setupCopyButtons();
    
    // Setup section toggles
    setupSectionToggles();
}

// Render Concept Content
function renderConceptContent(concept) {
    let html = `
        <div class="concept-header">
            <h1>
                <span class="icon">${concept.icon}</span>
                ${concept.title.replace(/^[^\s]+\s/, '')}
            </h1>
            <p class="concept-description">${concept.description}</p>
        </div>
    `;
    
    concept.sections.forEach((section, index) => {
        const sectionId = `${concept.id}-section-${index}`;
        const isExpanded = expandedSections.has(sectionId) || section.expanded;
        
        html += `
            <div class="concept-section">
                <div class="section-header" data-section-id="${sectionId}">
                    <h2>
                        <span>${section.title}</span>
                    </h2>
                    <span class="section-toggle ${isExpanded ? 'expanded' : ''}">▼</span>
                </div>
                <div class="section-content ${isExpanded ? 'expanded' : ''}" id="${sectionId}">
                    ${renderSectionContent(section, concept)}
                </div>
            </div>
        `;
    });
    
    // Add completion button
    html += `
        <div class="completion-section">
            <button class="completion-btn ${completedConcepts.has(concept.id) ? 'completed' : ''}" 
                    onclick="toggleCompletion('${concept.id}')">
                ${completedConcepts.has(concept.id) ? '✓ Completed' : 'Mark as Completed'}
            </button>
        </div>
    `;
    
    return html;
}

// Render Section Content
function renderSectionContent(section, concept) {
    let html = `<p>${section.content}</p>`;
    
    // Code Example
    if (section.codeExample) {
        html += `
            <div class="code-example">
                <div class="code-example-header">
                    <span class="file-path">📁 ${section.codeExample.file}</span>
                    <button class="copy-btn" onclick="copyToClipboard(this)">Copy</button>
                </div>
                <pre class="code-block"><code>${escapeHtml(section.codeExample.code)}</code></pre>
            </div>
        `;
    }
    
    // Explanation
    if (section.explanation) {
        html += `
            <div class="info-box info">
                <strong>💡 Explanation:</strong> ${section.explanation}
            </div>
        `;
    }
    
    // Usage List
    if (section.usage && section.usage.length > 0) {
        html += '<ul class="usage-list">';
        section.usage.forEach(usage => {
            html += `
                <li class="usage-item">
                    <div class="usage-item-header">📍 ${usage.location}</div>
                    <div class="usage-item-description">${usage.description}</div>
                </li>
            `;
        });
        html += '</ul>';
    }
    
    // Visual Diagram
    if (section.visualDiagram) {
        html += '<div class="visual-diagram">';
        section.visualDiagram.steps.forEach((step, index) => {
            html += `<span class="flow-step">${step}</span>`;
            if (index < section.visualDiagram.steps.length - 1) {
                html += '<span class="arrow">→</span>';
            }
        });
        html += '</div>';
    }
    
    // Info Box
    if (section.info) {
        html += `
            <div class="info-box tip">
                ${section.info}
            </div>
        `;
    }
    
    return html;
}

// Setup Popular Buttons
function setupPopularButtons() {
    document.querySelectorAll('.popular-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const conceptId = e.target.dataset.concept;
            if (concepts.length > 0) {
                showConcept(conceptId);
            }
        });
    });
}

// Setup Event Listeners
function setupEventListeners() {
    // Search
    searchInput.addEventListener('input', handleSearch);
    
    // Popular buttons (setup after concepts load)
    setupPopularButtons();
    
    // Keyboard shortcuts
    document.addEventListener('keydown', (e) => {
        if (e.key === '/' && e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
            e.preventDefault();
            searchInput.focus();
        }
    });
}

// Handle Search
function handleSearch(e) {
    const query = e.target.value.toLowerCase().trim();
    
    if (!query) {
        renderNavigation();
        return;
    }
    
    const filtered = concepts.filter(concept => {
        return concept.title.toLowerCase().includes(query) ||
               concept.description.toLowerCase().includes(query) ||
               concept.sections.some(section => 
                   section.title.toLowerCase().includes(query) ||
                   section.content.toLowerCase().includes(query)
               );
    });
    
    navMenu.innerHTML = '';
    filtered.forEach(concept => {
        const navItem = document.createElement('div');
        navItem.className = `nav-item ${completedConcepts.has(concept.id) ? 'completed' : ''}`;
        navItem.dataset.conceptId = concept.id;
        
        navItem.innerHTML = `
            <span class="nav-item-icon">${concept.icon}</span>
            <span class="nav-item-text">${highlightMatch(concept.title.replace(/^[^\s]+\s/, ''), query)}</span>
            ${completedConcepts.has(concept.id) ? '<span class="nav-item-check">✓</span>' : ''}
        `;
        
        navItem.addEventListener('click', () => {
            showConcept(concept.id);
            searchInput.value = '';
            renderNavigation();
        });
        
        navMenu.appendChild(navItem);
    });
}

// Highlight Search Matches
function highlightMatch(text, query) {
    if (!query) return text;
    const regex = new RegExp(`(${query})`, 'gi');
    return text.replace(regex, '<mark>$1</mark>');
}

// Setup Section Toggles
function setupSectionToggles() {
    document.querySelectorAll('.section-header').forEach(header => {
        header.addEventListener('click', (e) => {
            const sectionId = e.currentTarget.dataset.sectionId;
            const content = document.getElementById(sectionId);
            const toggle = e.currentTarget.querySelector('.section-toggle');
            
            if (content.classList.contains('expanded')) {
                content.classList.remove('expanded');
                toggle.classList.remove('expanded');
                expandedSections.delete(sectionId);
            } else {
                content.classList.add('expanded');
                toggle.classList.add('expanded');
                expandedSections.add(sectionId);
            }
            
            saveExpandedSections();
        });
    });
}

// Setup Copy Buttons
function setupCopyButtons() {
    document.querySelectorAll('.copy-btn').forEach(btn => {
        btn.addEventListener('click', copyToClipboard.bind(null, btn));
    });
}

// Copy to Clipboard
function copyToClipboard(button) {
    const codeBlock = button.parentElement.nextElementSibling;
    const code = codeBlock.textContent;
    
    navigator.clipboard.writeText(code).then(() => {
        button.textContent = 'Copied!';
        button.classList.add('copied');
        setTimeout(() => {
            button.textContent = 'Copy';
            button.classList.remove('copied');
        }, 2000);
    }).catch(err => {
        console.error('Failed to copy:', err);
        button.textContent = 'Error';
    });
}

// Toggle Completion
function toggleCompletion(conceptId) {
    if (completedConcepts.has(conceptId)) {
        completedConcepts.delete(conceptId);
    } else {
        completedConcepts.add(conceptId);
    }
    
    localStorage.setItem('completedConcepts', JSON.stringify([...completedConcepts]));
    updateProgress();
    renderNavigation();
    
    // Update completion button
    const btn = document.querySelector('.completion-btn');
    if (btn) {
        if (completedConcepts.has(conceptId)) {
            btn.textContent = '✓ Completed';
            btn.classList.add('completed');
        } else {
            btn.textContent = 'Mark as Completed';
            btn.classList.remove('completed');
        }
    }
}

// Update Progress
function updateProgress() {
    const total = concepts.length;
    const completed = completedConcepts.size;
    const percentage = total > 0 ? Math.round((completed / total) * 100) : 0;
    
    progressFill.style.width = `${percentage}%`;
    progressPercent.textContent = `${percentage}%`;
}

// Save/Restore Expanded Sections
function saveExpandedSections() {
    localStorage.setItem('expandedSections', JSON.stringify([...expandedSections]));
}

function restoreExpandedSections() {
    expandedSections.forEach(sectionId => {
        const content = document.getElementById(sectionId);
        const header = content?.previousElementSibling;
        const toggle = header?.querySelector('.section-toggle');
        
        if (content && header && toggle) {
            content.classList.add('expanded');
            toggle.classList.add('expanded');
        }
    });
}

// Utility Functions
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showError(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.textContent = message;
    document.body.appendChild(errorDiv);
    setTimeout(() => errorDiv.remove(), 5000);
}

// Make toggleCompletion available globally
window.toggleCompletion = toggleCompletion;

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initApp);
} else {
    initApp();
}
