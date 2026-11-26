/*
 * GAD amount inputs: auto-comma every 3 digits while typing, and force 2 decimals (.00) on blur.
 * Usage: <input type="text" inputmode="decimal" class="gad-input money-input" wire:model="...">
 * The server strips the commas (see App\Livewire\Concerns\CleansMoney).
 */
(function () {
    function format(value, final) {
        var v = String(value == null ? '' : value).replace(/,/g, '').replace(/[^0-9.]/g, '');
        var dot = v.indexOf('.');
        var intPart = dot < 0 ? v : v.slice(0, dot);
        var dec = dot < 0 ? null : v.slice(dot + 1).replace(/\./g, '').slice(0, 2);

        intPart = intPart.replace(/^0+(?=\d)/, '');

        if (final) {
            if (intPart === '' && (dec === null || dec === '')) return '';
            intPart = intPart || '0';
            dec = (dec || '').padEnd(2, '0');
        }

        intPart = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        return dec === null ? intPart : intPart + '.' + dec;
    }

    function onInput(e) {
        var el = e.target;
        if (!el.classList || !el.classList.contains('money-input')) return;

        // keep the caret next to the same digit after commas are inserted
        var caret = el.selectionStart;
        var sig = el.value.slice(0, caret).replace(/[^0-9.]/g, '').length;
        var formatted = format(el.value, false);
        if (formatted === el.value) return;
        el.value = formatted;

        var pos = 0, seen = 0;
        while (pos < formatted.length && seen < sig) {
            if (/[0-9.]/.test(formatted[pos])) seen++;
            pos++;
        }
        try { el.setSelectionRange(pos, pos); } catch (err) { /* ignore */ }
    }

    function onBlur(e) {
        var el = e.target;
        if (!el.classList || !el.classList.contains('money-input')) return;
        var v = format(el.value, true);
        if (el.value !== v) {
            el.value = v;
            el.dispatchEvent(new Event('input', { bubbles: true }));
        }
    }

    function formatAll(root) {
        (root || document).querySelectorAll('.money-input').forEach(function (el) {
            if (el === document.activeElement) return;
            var v = format(el.value, true);
            if (el.value !== v) el.value = v;
        });
    }

    document.addEventListener('input', onInput, true);
    document.addEventListener('focusout', onBlur, true);
    document.addEventListener('DOMContentLoaded', function () { formatAll(); });

    // Re-apply the display format after Livewire re-renders (modals open, save, clear...).
    document.addEventListener('livewire:init', function () {
        if (!window.Livewire || !Livewire.hook) return;
        Livewire.hook('morph.updated', function (ctx) {
            var el = ctx.el;
            if (el && el.classList && el.classList.contains('money-input')) formatAll(el.parentNode);
        });
        Livewire.hook('morph.added', function (ctx) {
            if (ctx.el && ctx.el.querySelectorAll) formatAll(ctx.el);
            if (ctx.el && ctx.el.classList && ctx.el.classList.contains('money-input')) formatAll(ctx.el.parentNode);
        });
        Livewire.hook('commit', function (ctx) {
            ctx.succeed(function () { setTimeout(function () { formatAll(); }, 60); });
        });
    });
})();
