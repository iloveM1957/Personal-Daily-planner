// ====================== accordion function ======================
function initAccordion() {
    const accItems = document.querySelectorAll('.annAccordion');
    accItems.forEach(item => {
        item.addEventListener('click', toggleAccordion);
    });
}

function toggleAccordion() {
    this.classList.toggle('annActive');
    const panel = this.nextElementSibling;
    panel.style.maxHeight = panel.style.maxHeight ? null : panel.scrollHeight + "px";
}

// ====================== Modal box control ======================
function openModal() {
    document.getElementById('annAskModal').style.display = "block";
}

function closeModal() {
    document.getElementById('annAskModal').style.display = "none";
}

// ====================== Initialization entry ======================
function initANN() {
    initAccordion();
    bindSearch();
}

// Perform initialization directly
initANN();