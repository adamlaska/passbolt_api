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
 * @since         5.14.0
 */
namespace App\Service\Settings;

use App\Model\Entity\Role;
use App\Model\Validation\EmailValidationRule;
use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\Utility\Hash;
use Passbolt\Locale\Service\GetOrgLocaleService;

class SettingsGetService
{
    public const SETTINGS_VISIBILITY_KEY = 'settingsVisibility';

    public const SETTINGS_PASSBOLT_LEGAL = 'passbolt.legal';

    public const SETTINGS_PASSBOLT_EDITION = 'passbolt.edition';

    public const SETTINGS_PASSBOLT_EMAIL_VALIDATE_REGEX = 'passbolt.email.validate.regex';

    public const SETTINGS_PASSBOLT_VERSION = 'passbolt.version';

    public const SETTINGS_PASSBOLT_NAME = 'passbolt.name';

    public const SETTINGS_PASSBOLT_PLUGINS = 'passbolt.plugins';

    public const SETTINGS_PASSBOLT_IMAGE_STORAGE_PUBLIC_PATH = 'ImageStorage.publicPath';

    /**
     * Keys that will always be whitelisted, in addition to the ones defined in config. (once logged in).
     */
    protected array $alwaysWhiteListed = [
        'version',
        'enabled',
    ];

    /**
     * Get the list of settings that should be displayed publicly.
     *
     * @param string $role role of the user accessing the settings.
     * @return array
     */
    public function getSettings(string $role): array
    {
        $baseSettings = [
            'app' => [
                'url' => Router::url('/', true),
                'locale' => (new GetOrgLocaleService())->getLocale(),
            ],
            'passbolt' => [
                'legal' => Configure::read(self::SETTINGS_PASSBOLT_LEGAL),
                'edition' => Configure::read(self::SETTINGS_PASSBOLT_EDITION),
            ],
        ];

        $baseSettings = $this->getEmailValidateRegex($baseSettings);

        $settings = $this->getSettingsByUserRole($role);

        return array_merge_recursive($baseSettings, $settings);
    }

    /**
     * Get the email validate regex.
     *
     * @param array $baseSettings base settings to insert the email validate regex setting.
     * @return array
     */
    public function getEmailValidateRegex(array $baseSettings): array
    {
        if (is_string(Configure::read(EmailValidationRule::REGEX_CHECK_KEY))) {
            $baseSettings = Hash::insert(
                $baseSettings,
                self::SETTINGS_PASSBOLT_EMAIL_VALIDATE_REGEX,
                Configure::read(EmailValidationRule::REGEX_CHECK_KEY)
            );
        }

        return $baseSettings;
    }

    /**
     * Get the list of settings based on users' role.
     *
     * @param string $role role of the user accessing the settings.
     * @return array
     */
    public function getSettingsByUserRole(string $role): array
    {
        if ($role !== Role::GUEST) {
            $settings = [
                'app' => [
                    'version' => [
                        'number' => Configure::read(self::SETTINGS_PASSBOLT_VERSION),
                        'name' => Configure::read(self::SETTINGS_PASSBOLT_NAME),
                    ],
                    'debug' => Configure::read('debug') ? 1 : 0,
                    'server_timezone' => date_default_timezone_get(),
                    // session timeout info in minutes
                    'session_timeout' => Configure::read(
                        'Session.timeout',
                        (int)ini_get('session.gc_maxlifetime') / 60
                    ),
                    'image_storage' => [
                        'public_path' => Configure::read(self::SETTINGS_PASSBOLT_IMAGE_STORAGE_PUBLIC_PATH),
                    ],
                ],
                'passbolt' => [
                    'plugins' => $this->_getWhiteListedPluginConfig($this->_getPluginWhiteList(false)),
                ],
            ];
        } else {
            $settings = [
                'passbolt' => [
                    'plugins' => $this->_getWhiteListedPluginConfig($this->_getPluginWhiteList(true)),
                ],
            ];
        }

        return $settings;
    }

    /**
     * Get plugin options that are whitelisted.
     *
     * @param bool $public for public visibility or not (require log in).
     * @return array list of
     */
    protected function _getPluginWhiteList(bool $public = false): array
    {
        $confKey = $public === true ? 'whiteListPublic' : 'whiteList';
        $pluginsConf = Configure::read(self::SETTINGS_PASSBOLT_PLUGINS, []);
        $res = [];

        foreach ($pluginsConf as $pluginName => $pluginConf) {
            if (!$public) {
                foreach ($this->alwaysWhiteListed as $whiteListed) {
                    $res[] = $pluginName . '.' . $whiteListed;
                }
            }

            $whiteListOptions = Hash::extract($pluginConf, self::SETTINGS_VISIBILITY_KEY . '.' . $confKey);
            if (is_array($whiteListOptions)) {
                foreach ($whiteListOptions as $whiteList) {
                    $res[] = $pluginName . '.' . $whiteList;
                }
            }
        }

        return $res;
    }

    /**
     * Get white listed config.
     *
     * @param array $whiteList white list options array
     * @return array white listed plugins configurations
     */
    protected function _getWhiteListedPluginConfig(array $whiteList): array
    {
        $pluginsConfig = [];
        // Add white listed plugin options.
        foreach ($whiteList as $path) {
            if (Configure::check(self::SETTINGS_PASSBOLT_PLUGINS . '.' . $path)) {
                $pluginsConfig = Hash::insert(
                    $pluginsConfig,
                    $path,
                    Configure::read(self::SETTINGS_PASSBOLT_PLUGINS . '.' . $path)
                );
            }
        }

        return $pluginsConfig;
    }
}
