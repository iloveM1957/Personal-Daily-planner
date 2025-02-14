// 切换选项卡
function switchTab(tabName) {
    document.querySelectorAll('.formPro').forEach(form => form.classList.remove('activeForm'));
    document.querySelectorAll('.tabBtnPro').forEach(btn => btn.classList.remove('active'));
    document.getElementById(tabName + 'Form').classList.add('activeForm');
    event.currentTarget.classList.add('active');
}

// 预览上传的头像
function previewAvatar(event) {
    const reader = new FileReader();
    reader.onload = function() {
        const preview = document.getElementById('avatarPreview');
        preview.src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}

// editProfile.js 新增确认逻辑
function confirmAccountDeletion() {
    if (confirm('⚠️ Warning: This action will permanently delete your account!\n\nAre you sure you want to delete your account?')) {
        // 提交隐藏表单
        document.getElementById('deleteForm').submit();
    }
}