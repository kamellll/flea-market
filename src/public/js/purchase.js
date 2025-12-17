document.addEventListener('DOMContentLoaded', function () {
    const paySelect = document.getElementById('pay');
    const payText = document.querySelector('.pay-box__content');
    const payMethod = document.getElementById('pay_method');

    // 初期値（「選択してください」のときに戻したい文字）
    const defaultText = payText.textContent;

    paySelect.addEventListener('change', function () {
        const selectedOption = paySelect.options[paySelect.selectedIndex];

        if (paySelect.value === '') {
            // 何も選択されていないときは元の文言
            payText.textContent = defaultText;
            payMethod.value = 1;
        } else {
            // 選択された option の表示テキストをそのまま使う
            payText.textContent = selectedOption.textContent;
            payMethod.value = selectedOption.value;
        }
    });
});