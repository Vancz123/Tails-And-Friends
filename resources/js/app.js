const imagePreviewInputs = document.querySelectorAll('[data-image-preview-input]');

for (const imageInput of imagePreviewInputs) {
    if (!(imageInput instanceof HTMLInputElement)) {
        continue;
    }

    const imagePreview = imageInput.closest('.field-group')?.querySelector('[data-image-preview]');
    const previewImage = imagePreview?.querySelector('[data-image-preview-image]');
    const previewLabel = imagePreview?.querySelector('[data-image-preview-label]');

    if (
        !(imagePreview instanceof HTMLElement)
        || !(previewImage instanceof HTMLImageElement)
        || !(previewLabel instanceof HTMLElement)
    ) {
        continue;
    }

    const defaultImageSource = previewImage.getAttribute('src');
    const defaultImageAlt = previewImage.alt;
    const defaultImageLabel = previewLabel.textContent ?? '';
    const defaultPreviewHidden = imagePreview.hidden;
    let previewObjectUrl = null;

    imageInput.addEventListener('change', () => {
        const selectedFile = imageInput.files?.[0];

        if (previewObjectUrl) {
            URL.revokeObjectURL(previewObjectUrl);
            previewObjectUrl = null;
        }

        if (!selectedFile) {
            if (defaultImageSource) {
                previewImage.src = defaultImageSource;
            } else {
                previewImage.removeAttribute('src');
            }

            previewImage.alt = defaultImageAlt;
            previewLabel.textContent = defaultImageLabel;
            imagePreview.hidden = defaultPreviewHidden;

            return;
        }

        previewObjectUrl = URL.createObjectURL(selectedFile);
        previewImage.src = previewObjectUrl;
        previewImage.alt = 'Pré-visualização da imagem selecionada';
        previewLabel.textContent = selectedFile.name;
        imagePreview.hidden = false;
    });

    window.addEventListener('pagehide', () => {
        if (previewObjectUrl) {
            URL.revokeObjectURL(previewObjectUrl);
        }
    }, { once: true });
}

const gameCharacterTabGroups = document.querySelectorAll('[data-game-character-tabs]');

for (const tabGroup of gameCharacterTabGroups) {
    const tabs = Array.from(tabGroup.querySelectorAll('[data-game-character-tab]'));
    const panels = Array.from(tabGroup.querySelectorAll('[data-game-character-panel]'));

    if (tabs.length === 0 || panels.length === 0) {
        continue;
    }

    const activateTab = (selectedTab, shouldFocus = false) => {
        const panelId = selectedTab.getAttribute('aria-controls');
        const selectedPanel = panels.find((panel) => panel.id === panelId);

        if (!selectedPanel) {
            return;
        }

        for (const tab of tabs) {
            const isSelected = tab === selectedTab;

            tab.setAttribute('aria-selected', String(isSelected));
            tab.tabIndex = isSelected ? 0 : -1;
        }

        for (const panel of panels) {
            panel.hidden = panel !== selectedPanel;
        }

        if (shouldFocus) {
            selectedTab.focus();
        }
    };

    for (const [index, tab] of tabs.entries()) {
        tab.addEventListener('click', () => activateTab(tab));
        tab.addEventListener('keydown', (event) => {
            if (!['ArrowRight', 'ArrowLeft', 'Home', 'End'].includes(event.key)) {
                return;
            }

            event.preventDefault();

            let nextIndex = index;

            if (event.key === 'ArrowRight') {
                nextIndex = (index + 1) % tabs.length;
            } else if (event.key === 'ArrowLeft') {
                nextIndex = (index - 1 + tabs.length) % tabs.length;
            } else if (event.key === 'Home') {
                nextIndex = 0;
            } else if (event.key === 'End') {
                nextIndex = tabs.length - 1;
            }

            activateTab(tabs[nextIndex], true);
        });
    }

    const initiallySelectedTab = tabs.find((tab) => tab.getAttribute('aria-selected') === 'true') ?? tabs[0];

    activateTab(initiallySelectedTab);
}
