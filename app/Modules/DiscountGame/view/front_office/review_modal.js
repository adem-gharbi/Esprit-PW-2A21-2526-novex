(function () {
    const modal = document.querySelector('[data-review-modal]');

    if (!modal) {
        return;
    }

    modal.querySelectorAll('[data-review-close]').forEach(function (button) {
        button.addEventListener('click', function () {
            modal.hidden = true;
        });
    });
}());
