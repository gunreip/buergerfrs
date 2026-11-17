import { expect, test, type Locator } from '@playwright/test';
import { mkdir, rename, writeFile } from 'node:fs/promises';
import path from 'node:path';

test('Translation Workbench – Hardcopys', async ({ page }) => {
    const directory = path.resolve('storage/app/private/playwright/hardcopies');
    await mkdir(directory, { recursive: true });
    const shots: string[] = [];

    async function capture(name: string) {
        await page.mouse.move(0, 0);
        await page.evaluate(() => document.fonts.ready.then(() => undefined));
        await expect(page.locator('[data-workbench-page-loading]:visible, [data-workbench-modal-loading]:visible, [data-flux-tab-panel][data-selected] [role="status"]:visible').filter({ has: page.locator('svg') })).toHaveCount(0);
        const file = test.info().outputPath(name + '.png');
        await page.screenshot({ path: file, animations: 'disabled' });
        shots.push(name);
        await test.info().attach(name, { path: file, contentType: 'image/png' });
    }

    async function captureDialog(name: string, dialog: Locator) {
        await test.step(name, async () => {
            await expect(dialog).toBeVisible();
            await capture(name);
            await page.keyboard.press('Escape');
            await expect(dialog).toBeHidden();
        });
    }

    async function openCreateOption(select: Locator) {
        await select.locator('[data-flux-select-button]').click();
        await select.locator('[data-flux-option-create]').click();
    }

    async function chooseReason(select: Locator) {
        await select.locator('[data-flux-select-button]').click();
        await select.locator('ui-option[value]').first().click();
    }

    const dialog = (name: string) => page.locator(`dialog[data-modal="${name}"]`);
    const route = '/admin/translation-workbench';
    await page.goto(route + '?tab=findings');
    const toggle = page.locator('[wire\\:click="toggleTestcase"]');
    await expect(toggle, 'Workbench-Anmeldung und lokale Testcase-Funktion erforderlich').toBeVisible();
    const sandbox = page.locator('[data-workbench-test-environment]');
    await expect(sandbox, 'Für den Lauf eine eigene Sitzung mit ausgeschaltetem Testcase verwenden').toHaveCount(0);
    await toggle.click();
    await expect(sandbox).toBeVisible();

    try {
        const tabs = ['findings', 'in-progress', 'translations', 'review', 'publication', 'published', 'source-replacement', 'source-issues', 'completed', 'source-usage'];
        for (const tab of tabs) {
            await test.step(tab, async () => {
                const trigger = page.locator(`[data-flux-tab][name="${tab}"]`);
                await page.goto(route + '?tab=' + tab);
                await expect(sandbox).toBeVisible();
                await expect(trigger).toHaveAttribute('aria-selected', 'true');
                const panel = page.locator(`[data-flux-tab-panel][name="${tab}"]`);
                await expect(panel.locator('table').first()).toBeVisible();

                await trigger.evaluate(el => el.scrollIntoView({ block: 'start' }));
                await capture(tab);
            });
        }

        const usage = page.locator('[data-flux-tab-panel][name="source-usage"] tbody tr').first();
        await usage.locator('button[wire\\:click^="openUsageWorkflow("]').click();
        await captureDialog('source-usage-workflow', dialog('source-usage-workflow'));
        await usage.locator('button[wire\\:click^="open("]').last().click();
        const usageDecision = dialog('source-usage-decision');
        await expect(usageDecision.locator('textarea[readonly]')).toBeVisible();
        await capture('source-usage-decision');
        await openCreateOption(usageDecision.locator('ui-select[wire\\:model="reason"]'));
        await captureDialog('create-source-usage-reason', page.locator('dialog[data-modal^="workbench-create-workflow-reason-"]:visible'));

        await page.goto(route + '?tab=findings');
        await expect(sandbox).toBeVisible();
        const findings = page.locator('[data-flux-tab-panel][name="findings"]');
        await findings.locator('input[wire\\:model\\.live\\.debounce\\.300ms="search"]').fill('fixtures/shared-1.php');
        const row = findings.locator('tr').filter({ hasText: 'fixtures/shared-1.php' });
        await row.locator('button[wire\\:click^="open("]').last().click();
        const editor = page.locator('dialog[data-modal="generation-finding-editor"]');
        await expect(editor).toBeVisible();
        await capture('edit-finding');
        await editor.locator('button[wire\\:click^="openWorkflowStatus("]').last().click();
        await captureDialog('workflow-status', dialog('workbench-workflow-status'));
        await openCreateOption(editor.locator('[data-editor-error-target="assignmentKey"]'));
        await captureDialog('create-translation-key', dialog('workbench-create-translation-key'));
        await openCreateOption(editor.locator('[data-editor-error-target="reason"]'));
        await captureDialog('create-translation-reason', dialog('workbench-create-change-reason'));
        const shared = page.locator('dialog[data-modal="workbench-shared-findings"]');
        await editor.locator('button[wire\\:click^="openShared("]').last().click();
        await expect(shared.locator('tbody tr').first()).toBeVisible();
        await capture('shared-ui-candidates');
        const sharedReason = shared.locator('ui-select[wire\\:model\\.live="sharedReason"]');
        await openCreateOption(sharedReason);
        await captureDialog('create-assignment-reason', dialog('workbench-create-assignment-reason'));
        await shared.locator('tbody ui-checkbox:not([disabled])').first().click();
        await chooseReason(sharedReason);
        await shared.locator('button[wire\\:click="reviewSharedAssignment"]').last().click();
        await captureDialog('confirm-shared-key-assignment', dialog('workbench-shared-assignment-confirmation'));
        await page.keyboard.press('Escape');
        await expect(shared).toBeHidden();
        await expect(editor).toBeVisible();
        await editor.locator('button[wire\\:click="saveDraft"]').last().click();
        await expect(page.locator('[data-flux-toast-dialog][data-variant="danger"]:visible').last()).toBeVisible();
        await capture('edit-finding-validation-toast');

        await expect(sandbox).toBeAttached();
        const target = editor.locator('textarea[wire\\:model="value"]');
        const locale = await target.getAttribute('lang');
        const text = locale === 'de' ? 'Änderungen speichern' : await editor.locator('textarea[wire\\:model="sourceLanguageValue"]').inputValue();
        await target.fill(text);
        await chooseReason(editor.locator('[data-editor-error-target="reason"]'));
        await editor.locator('button[wire\\:click="saveDraft"]').last().click();
        await editor.locator('button[wire\\:click="submitForReview"]').last().click();
        await expect(editor).toBeHidden();
        const reviewPanel = page.locator('[data-flux-tab-panel][name="review"]');
        await reviewPanel.locator('thead ui-checkbox').click();
        await reviewPanel.locator('button[wire\\:click="openReviewBatch"]').last().click();
        const review = dialog('workbench-review-batch');
        await expect(review.locator('tbody tr').first()).toBeVisible();
        await capture('review-selected-revisions');
        const reviewReason = review.locator('ui-select[wire\\:model="reviewBatchReason"]');
        const reasonDialog = page.locator('dialog[data-modal^="workbench-create-workflow-reason-"]:visible');
        await openCreateOption(reviewReason);
        await captureDialog('create-review-reason', reasonDialog);
        await chooseReason(reviewReason);
        await review.locator('button[wire\\:click="decideReviewBatch(true)"]').last().click();
        await expect(review).toBeHidden();

        await page.goto(route + '?tab=publication');
        await expect(sandbox).toBeVisible();
        const publicationPanel = page.locator('[data-flux-tab-panel][name="publication"]');
        await publicationPanel.locator('thead ui-checkbox').click();
        await publicationPanel.locator('button[wire\\:click="openPublicationTask"]').last().click();
        const prepare = dialog('workbench-publication-task');
        await expect(prepare).toBeVisible();
        await capture('prepare-publication');
        const publicationReason = prepare.locator('ui-select[wire\\:model="publicationReason"]');
        await openCreateOption(publicationReason);
        await captureDialog('create-publication-reason', reasonDialog);
        await chooseReason(publicationReason);
        await prepare.locator('button[wire\\:click="preparePublicationTask"]').last().click();
        const publication = dialog('generation-publication-confirmation');
        await expect(publication).toBeVisible();
        await capture('publication-preview');
        await expect(sandbox).toBeAttached();
        await publication.locator('button[wire\\:click="publish"]').last().click();
        await expect(publication.locator('button[wire\\:click="publish"]')).toHaveCount(0);
        await capture('publication-result');

        await page.goto(route + '?tab=source-replacement');
        await expect(sandbox).toBeVisible();
        const replacement = page.locator('[data-flux-tab-panel][name="source-replacement"]');
        const source = replacement.locator('tbody tr').filter({ hasText: 'fixtures/shared-1.php' });
        const groupCheckbox = source.getByRole('checkbox');
        await groupCheckbox.click();
        await expect(groupCheckbox).toBeChecked();
        await source.locator('button[wire\\:click*="openSourceComparison("]').last().click();
        await captureDialog('source-comparison', dialog('source-comparison'));
        await source.locator('button[wire\\:click*="previewSelectedSource("]').last().click();
        await captureDialog('source-replacement-preview', dialog('generation-source-preview'));
        await replacement.locator('button[wire\\:click="previewSelectedSourceGroup"]').last().click();
        const preview = dialog('generation-source-preview');
        await expect(preview.locator('tbody tr').first()).toBeVisible();
        await capture('source-replacement-group-preview');
        const sourceReason = preview.locator('ui-select[wire\\:model="sourceReason"]');
        await openCreateOption(sourceReason);
        await captureDialog('create-source-replacement-reason', reasonDialog);
        await chooseReason(sourceReason);
        await preview.locator('button[wire\\:click="prepareSource"]').last().click();
        await captureDialog('confirm-source-replacement', dialog('generation-source-run'));
    } finally {
        await page.goto(route);
        await expect(sandbox).toBeVisible();
        await toggle.click();
        await expect(sandbox).toHaveCount(0);
    }

    const version = Date.now();
    for (const name of shots) {
        await rename(test.info().outputPath(name + '.png'), path.join(directory, name + '.png'));
    }
    const tiles = shots.map(name => `<figure><a href="${name}.png?v=${version}"><img src="${name}.png?v=${version}" alt="${name}"></a><figcaption>${name}</figcaption></figure>`);
    await writeFile(path.join(directory, 'index.html'), `<!doctype html><html lang="de"><meta charset="utf-8"><title>Translation Workbench</title>
<style>body{background:#18181b;color:white;font:16px system-ui}main{display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:1rem}figure{margin:0}img{width:100%}</style>
<h1>Translation Workbench</h1><main>${tiles.join('\n')}</main></html>`);
    console.log('Hardcopys:', directory);
});
