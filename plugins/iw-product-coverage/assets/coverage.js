(() => {
    'use strict';
    function init(root) {
        if (root.dataset.iwpcReady) return;
        root.dataset.iwpcReady = '1';
        const select = root.querySelector('.iwpc-select');
        let target = root.querySelector('.iwpc-content');
        if (root.dataset.target) {
            try { target = document.querySelector(root.dataset.target); } catch (_) { target = null; }
            if (!target || target === root || target.contains(root)) {
                console.warn('IW Product Coverage: destino inválido o contiene el selector.', root.dataset.target);
                if (select) select.disabled = true;
                return;
            }
            if (target.dataset.iwpcOwner && target.dataset.iwpcOwner !== (select ? select.id : 'none')) {
                console.warn('IW Product Coverage: use un destino diferente para cada selector.');
                return;
            }
            target.dataset.iwpcOwner = select ? select.id : 'none';
            target.classList.add('iwpc-controlled');
            if (!target.id) target.id = `${select ? select.id : 'iwpc'}-target`;
            if (select) select.setAttribute('aria-controls', target.id);
        }
        function sync() {
            if (!target) return;
            const available = !!(select && select.value);
            target.hidden = !available;
            if (select) select.setAttribute('aria-expanded', String(available));
            target.querySelectorAll('form.wpcf7-form').forEach(form => {
                const set = (name, value) => {
                    let field = form.querySelector(`input[name="${name}"]`);
                    if (!field) {
                        field = document.createElement('input'); field.type = 'hidden'; field.name = name; form.appendChild(field);
                    }
                    field.value = value;
                };
                set('_iwpc_product', root.dataset.product);
                set('_iwpc_signature', root.dataset.signature);
                set('_iwpc_region', available ? select.value : '');
                set('region-disponible', available ? select.options[select.selectedIndex].text : '');
            });
        }
        sync();
        if (select) select.addEventListener('change', sync);
        if (target) {
            target.addEventListener('submit', sync, true);
            target.addEventListener('reset', () => setTimeout(sync, 0));
            new MutationObserver(sync).observe(target, {childList:true, subtree:true});
        }
    }
    function scan() { document.querySelectorAll('.iwpc').forEach(init); }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', scan); else scan();
    new MutationObserver(scan).observe(document.documentElement, {childList:true, subtree:true});
})();
