document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('product_image');
    const label = document.querySelector('.image-upload__label');
    const preview = document.getElementById('image_preview');

    input.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) {
            // ファイル未選択ならプレビューを消してボタンを戻す
            preview.src = '';
            label.classList.remove('has-image');
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            label.classList.add('has-image'); // ボタン非表示＋画像表示
        };
        reader.readAsDataURL(file);
    });
});