/* Manager reports: tab switching + filter reset link */
(function () {
    var buttons = document.querySelectorAll('.tab-button');
    var panels  = document.querySelectorAll('.tab-panel');
    var input   = document.getElementById('report-input');
    var clear   = document.getElementById('clear-filters');
    var base    = document.currentScript.dataset.base;

    function show(tab) {
        buttons.forEach(function (b) {
            b.classList.toggle('is-active', b.dataset.tab === tab);
        });
        panels.forEach(function (p) {
            p.classList.toggle('is-active', p.dataset.panel === tab);
        });
        input.value = tab;
        clear.href = base + '?report=' + tab;
    }

    buttons.forEach(function (b) {
        b.addEventListener('click', function () { show(b.dataset.tab); });
    });
})();
    
