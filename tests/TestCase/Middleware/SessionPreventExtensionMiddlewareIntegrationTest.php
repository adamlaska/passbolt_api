<?php
declare(strict_types=1);

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
 * @since         5.15.0
 */
namespace App\Test\TestCase\Middleware;

use App\Test\Lib\AppIntegrationTestCase;

class SessionPreventExtensionMiddlewareIntegrationTest extends AppIntegrationTestCase
{
    public function testSessionPreventExtensionMiddleware_PhantomSession(): void
    {
        //phantom session (unauthenticated request)
        $this->get('/auth/login');

        $this->assertResponseOk();
        $this->assertSessionNotHasKey('SessionPreventExtensionMiddleware');
        $this->assertSessionNotHasKey('Config');
    }

    public function testSessionPreventExtensionMiddleware_LoggedInSession(): void
    {
        $this->logInAsUser();
        $this->get('/resources.json');

        $this->assertResponseOk();
        // Here it is updating last user interaction
        $this->assertSessionHasKey('SessionPreventExtensionMiddleware');
        // We are not updating the Config.time as we are not preventing the extension
        $this->assertSessionNotHasKey('Config');
    }

    public function testSessionPreventExtensionMiddleware_LoggedInSessionWhenCallingIsAuthenticatedEndpoint(): void
    {
        $user = $this->logInAsUser();

        $timeReference = 1234;
        $_SESSION['SessionPreventExtensionMiddleware']['time'] = $timeReference;

        $this->get('/auth/is-authenticated.json');

        $expectedSession = [
            'SessionPreventExtensionMiddleware' => [
                'time' => $timeReference,
            ],
            'Config' => [
                'time' => $timeReference,
            ],
            'Auth' => [
                'user' => [
                    'id' => $user->id,
                ],
            ]
        ];
        $this->assertResponseOk();
        $this->assertSame($expectedSession, $_SESSION);
    }
}
