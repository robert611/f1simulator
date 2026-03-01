function startCountdown(linkElement, seconds, storageKey) {
    const originalText = linkElement.textContent;
    const originalHref = linkElement.getAttribute('href');

    linkElement.classList.add('blocked-link');
    linkElement.dataset.originalHref = originalHref;
    linkElement.removeAttribute('href');

    const updateButton = () => {
        if (seconds > 0) {
            linkElement.textContent = `Poczekaj ${seconds}s`;
            seconds--;
            setTimeout(updateButton, 1000);
        } else {
            linkElement.textContent = originalText;
            linkElement.setAttribute('href', linkElement.dataset.originalHref);
            linkElement.classList.remove('blocked-link');
            localStorage.removeItem(storageKey);
        }
    };

    updateButton();
}

function initBlockButton() {
    const blockButtons = document.querySelectorAll('.block-button-js');

    blockButtons.forEach(link => {
        const storageKey = link.getAttribute('data-storage-key');

        if (!storageKey) {
            console.warn('Przycisk z klasą "block-button-js" nie ma atrybutu "data-storage-key"');
            return;
        }

        const blockedUntil = localStorage.getItem(storageKey);
        if (blockedUntil) {
            const now = Date.now();
            const blockTime = parseInt(blockedUntil);

            if (now < blockTime) {
                const remainingMs = blockTime - now;
                startCountdown(link, Math.ceil(remainingMs / 1000), storageKey);
            } else {
                localStorage.removeItem(storageKey);
            }
        }

        link.addEventListener('click', function (e) {
            if (link.classList.contains('blocked-link')) {
                e.preventDefault();
                return;
            }

            const blockUntil = Date.now() + 60000;
            localStorage.setItem(storageKey, blockUntil.toString());
        });
    });
}

document.addEventListener('DOMContentLoaded', initBlockButton);
