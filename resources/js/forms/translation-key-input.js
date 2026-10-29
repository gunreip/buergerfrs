// Restrict only explicitly marked key editors, never stored values or general searches.
export function filterTranslationKey(value, position = value.length) {
    const lastDot = value.lastIndexOf('.');
    let text = '';
    let caret = 0;
    for (let index = 0; index < value.length; index++) {
        const allowed = index < lastDot ? /[a-z0-9_.-]/ : /[a-zA-Z0-9_.-]/;
        if (allowed.test(value[index])) {
            text += value[index];
            if (index < position) caret++;
        }
    }
    return { text, caret };
}

if (typeof document !== 'undefined') {
    const filterInput = (event) => {
        const input = event.target;
        if (!(input instanceof HTMLInputElement) || !input.hasAttribute('data-translation-key-input') || event.isComposing) return;
        const start = filterTranslationKey(input.value, input.selectionStart ?? input.value.length);
        const end = filterTranslationKey(input.value, input.selectionEnd ?? input.value.length);
        if (start.text !== input.value) {
            input.value = start.text;
            input.setSelectionRange(start.caret, end.caret);
        }
    };
    // Capture runs before Alpine/Livewire consume the input, including pasted text.
    document.addEventListener('input', filterInput, true);
    document.addEventListener('compositionend', (event) => {
        if (event.target instanceof HTMLInputElement && event.target.hasAttribute('data-translation-key-input')) {
            filterInput(event);
            event.target.dispatchEvent(new Event('input', { bubbles: true }));
        }
    }, true);
}
