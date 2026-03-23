/**
 * Passbolt ~ Open source password manager for teams
 * Copyright (c) Passbolt SA (https://www.passbolt.com)
 *
 * Licensed under GNU Affero General Public License version 3 of the or any later version.
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Passbolt SA (https://www.passbolt.com)
 * @license       https://opensource.org/licenses/AGPL-3.0 AGPL License
 * @link          https://www.passbolt.com Passbolt(tm)
 * @since         2.0.0
 */
document.addEventListener('DOMContentLoaded', () => {
    const seeTraceBtn = document.querySelector('.see-trace');
    const trace = document.querySelector('.trace');

    if (seeTraceBtn) {
        seeTraceBtn.addEventListener('click', (e) => {
            e.preventDefault();
            trace.classList.toggle('hidden');
        });
    }

    /**
     * Show/hide SMTP config inputs based on the selected authentication method.
     * @param {string} authMethod
     */
    const setSmtpConfigInputs = (authMethod) => {
        const usernameBlock = document.getElementById('smtp-config-input-username');
        const passwordBlock = document.getElementById('smtp-config-input-password');
        const usernameInput = document.querySelector('input[name="username"]');
        const passwordInput = document.querySelector('input[name="password"]');

        if (authMethod === 'username_only') {
            usernameBlock.classList.remove('hidden');
            passwordBlock.classList.add('hidden');
            passwordInput.value = '';
        } else if (authMethod === 'none') {
            usernameBlock.classList.add('hidden');
            passwordBlock.classList.add('hidden');
            usernameInput.value = '';
            passwordInput.value = '';
        } else {
            usernameBlock.classList.remove('hidden');
            passwordBlock.classList.remove('hidden');
        }
    };

    const authMethodElem = document.querySelector('select[name="authentication_method"]');

    setSmtpConfigInputs(authMethodElem.value);

    authMethodElem.addEventListener('change', function() {
        setSmtpConfigInputs(this.value);
    });
});
