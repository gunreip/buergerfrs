import { test } from 'node:test';
import assert from 'node:assert/strict';
import { filterTranslationKey } from '../../resources/js/forms/translation-key-input.js';

test('filters pasted characters and restricts capitals to the final segment', () => {
    assert.equal(filterTranslationKey('ui.button.Save_2-now').text, 'ui.button.Save_2-now');
    assert.equal(filterTranslationKey(' UI.ButtOn.sa ve!ä/🙂 ').text, '.uttn.save');
    assert.equal(filterTranslationKey('ui.button.ABC').text, 'ui.button.ABC');
    assert.equal(filterTranslationKey('ui.button.ABC.next').text, 'ui.button..next');
    assert.equal(filterTranslationKey('ui.button.').text, 'ui.button.');
    assert.equal(filterTranslationKey('').text, '');
});

test('keeps the cursor at the edited position after removing invalid characters', () => {
    assert.deepEqual(filterTranslationKey('ui.bu!tton.Save', 6), { text: 'ui.button.Save', caret: 5 });
    assert.deepEqual(filterTranslationKey('ui.button.Save', 3), { text: 'ui.button.Save', caret: 3 });
});
