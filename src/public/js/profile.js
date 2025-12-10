document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('avatar');
    const previewImage = document.getElementById('avatar-preview-image');
    const placeholder = document.querySelector('#avatar-preview .avatar-placeholder');

    if (!fileInput || !previewImage || !placeholder) return;

    // ★ 初期表示時：すでに src が入っていたら画像を表示、プレースホルダーを隠す
    const initialSrc = previewImage.getAttribute('src');
    if (initialSrc) {
        previewImage.style.display = 'block';
        placeholder.style.display = 'none';
    }

    fileInput.addEventListener('change', function (e) {
        const file = e.target.files[0];

        // 何も選択されていない場合
        if (!file) {
            previewImage.src = '';
            previewImage.style.display = 'none';
            placeholder.style.display = 'block';
            return;
        }

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
