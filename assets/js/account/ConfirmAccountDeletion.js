const confirmAccountDeletion = {
    initEventListeners: () => {
        const deleteAccountForm = document.getElementById('delete-account-form');

        deleteAccountForm?.addEventListener('submit', (event) => {
            const warningMessage = event.target.dataset.warningMessage;
            const answer = confirm(warningMessage);

            if (false === answer) {
                event.preventDefault();
            }
        });
    },
};

confirmAccountDeletion.initEventListeners();

