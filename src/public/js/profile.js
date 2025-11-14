document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('avatar');
    const previewImage = document.getElementById('avatar-preview-image');
    const placeholder = document.querySelector('#avatar-preview .avatar-placeholder');
    const fileNameSpan = document.getElementById('avatar-file-name');

    if (!fileInput) return;

    fileInput.addEventListener('change', function (e) {
        const file = e.target.files[0];

        // 何も選択されていない場合
        if (!file) {
            previewImage.src = '';
            previewImage.style.display = 'none';
            placeholder.style.display = 'block';
            //fileNameSpan.textContent = '選択されていません';
            return;
        }

        // ファイル名表示
        fileNameSpan.textContent = file.name;

        // 画像プレビュー
        const reader = new FileReader();
        reader.onload = function (event) {
            previewImage.src = event.target.result;
            previewImage.style.display = 'block';
            placeholder.style.display = 'none';
        };
        reader.readAsDataURL(file);
    });
});