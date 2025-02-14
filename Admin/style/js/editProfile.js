// Switch tabs
function switchTab(tabName) {
    document.querySelectorAll('.formPro').forEach(form => form.classList.remove('activeForm'));
    document.querySelectorAll('.tabBtnPro').forEach(btn => btn.classList.remove('active'));
    document.getElementById(tabName + 'Form').classList.add('activeForm');
    currentTarget.classList.add('active');
}
