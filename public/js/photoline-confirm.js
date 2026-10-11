/*
 * Photoline confirmation pop-up.
 *
 * Add data-confirm to any <form> to ask before it is submitted:
 *
 *   <form method="POST" action="..."
 *         data-confirm="Delete this staff account?"
 *         data-confirm-title="Delete staff account"   (optional)
 *         data-confirm-ok="Delete">                   (optional)
 */
(function () {
    function openConfirm(options, onConfirm) {
        var previouslyFocused = document.activeElement;

        var overlay = document.createElement('div');
        overlay.className = 'pl-confirm-overlay';
        overlay.innerHTML =
            '<div class="pl-confirm-box" role="dialog" aria-modal="true" aria-labelledby="plConfirmTitle">' +
                '<div class="pl-confirm-icon">?</div>' +
                '<h2 class="pl-confirm-title" id="plConfirmTitle"></h2>' +
                '<p class="pl-confirm-message"></p>' +
                '<div class="pl-confirm-actions">' +
                    '<button type="button" class="pl-confirm-btn pl-confirm-cancel">Cancel</button>' +
                    '<button type="button" class="pl-confirm-btn pl-confirm-ok"></button>' +
                '</div>' +
            '</div>';

        // textContent keeps the messages safe from HTML injection
        overlay.querySelector('.pl-confirm-title').textContent = options.title;
        overlay.querySelector('.pl-confirm-message').textContent = options.message;
        var okButton = overlay.querySelector('.pl-confirm-ok');
        var cancelButton = overlay.querySelector('.pl-confirm-cancel');
        okButton.textContent = options.ok;

        function close() {
            document.removeEventListener('keydown', onKeyDown);
            overlay.remove();
            if (previouslyFocused && previouslyFocused.focus) {
                previouslyFocused.focus();
            }
        }

        function onKeyDown(event) {
            if (event.key === 'Escape') {
                close();
            }
        }

        cancelButton.addEventListener('click', close);
        okButton.addEventListener('click', function () {
            close();
            onConfirm();
        });
        overlay.addEventListener('click', function (event) {
            if (event.target === overlay) {
                close();
            }
        });
        document.addEventListener('keydown', onKeyDown);

        document.body.appendChild(overlay);
        requestAnimationFrame(function () {
            overlay.classList.add('is-open');
        });
        okButton.focus();
    }

    // Runs after the form's own submit handlers, so any validation
    // that already blocked the submit is respected.
    document.addEventListener('submit', function (event) {
        var form = event.target;

        if (!form.matches || !form.matches('form[data-confirm]') || event.defaultPrevented) {
            return;
        }

        event.preventDefault();

        openConfirm({
            title: form.getAttribute('data-confirm-title') || 'Please confirm',
            message: form.getAttribute('data-confirm'),
            ok: form.getAttribute('data-confirm-ok') || 'Confirm'
        }, function () {
            form.submit();
        });
    });
})();
