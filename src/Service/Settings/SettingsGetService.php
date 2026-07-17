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
namespace App\Service\Settings;

use App\Model\Dto\Settings\SettingsDto;
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

    public const SETTINGS_PASSBOLT_EMAIL_VALIDATE_REGEX = 'email.validate.regex';

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

    private array $appSettings = [];
    private array $passboltSettings = [];
    private string $role;

    /**
     * SettingsGetService constructor
     *
     * @param string $role user role
     */
    public function __construct(string $role)
    {
        $this->role = $role;
    }

    /**
     * Get the list of settings that should be displayed publicly.
     *
     * @return \App\Model\Dto\Settings\SettingsDto
     */
    public function getSettings(): SettingsDto
    {
        $this->setBaseAppSettings();
        $this->setBasePassboltSettings();
        $this->getEmailValidateRegex();
        if ($this->isUser()) {
            $this->setUserAppSettings();
        }
        $this->setPassboltSettings();

        return new SettingsDto($this->appSettings, $this->passboltSettings);
    }

    /**
     * Set the base app settings.
     */
    public function setBaseAppSettings(): void
    {
        $this->appSettings = [
            'url' => Router::url('/', true),
            'locale' => (new GetOrgLocaleService())->getLocale(),
        ];
    }

    /**
     * Set the base passbolt settings.
     */
    public function setBasePassboltSettings(): void
    {
        $this->passboltSettings = [
            'legal' => Configure::read(self::SETTINGS_PASSBOLT_LEGAL),
            'edition' => Configure::read(self::SETTINGS_PASSBOLT_EDITION),
        ];
    }

    /**
     * Get the email validate regex
     */
    public function getEmailValidateRegex(): void
    {
        if (is_string(Configure::read(EmailValidationRule::REGEX_CHECK_KEY))) {
            $this->passboltSettings = Hash::insert(
                $this->passboltSettings,
                self::SETTINGS_PASSBOLT_EMAIL_VALIDATE_REGEX,
                Configure::read(EmailValidationRule::REGEX_CHECK_KEY)
            );
        }
    }

    /**
     * Check if the user is logged and has not a guest role.
     *
     * @return bool
     */
    public function isUser(): bool
    {
        return $this->role !== Role::GUEST;
    }

    /**
     * Set the list of user app settings
     */
    public function setUserAppSettings(): void
    {
        $this->appSettings['version'] = [
            'number' => Configure::read(self::SETTINGS_PASSBOLT_VERSION),
            'name' => Configure::read(self::SETTINGS_PASSBOLT_NAME),
        ];
        $this->appSettings['debug'] = Configure::read('debug') ? 1 : 0;
        $this->appSettings['server_timezone'] = date_default_timezone_get();
        $this->appSettings['session_timeout'] = Configure::read(
            'Session.timeout',
            (int)ini_get('session.gc_maxlifetime') / 60
        );
        $this->appSettings['image_storage'] = [
            'public_path' => Configure::read(self::SETTINGS_PASSBOLT_IMAGE_STORAGE_PUBLIC_PATH),
        ];
    }

    /**
     * Set the list of passbolt settings
     */
    public function setPassboltSettings(): void
    {
        $this->passboltSettings['plugins'] = $this->_getWhiteListedPluginConfig($this->_getPluginWhiteList());
    }

    /**
     * Get plugin options that are whitelisted.
     *
     * @return array list of whiteList
     */
    protected function _getPluginWhiteList(): array
    {
        $configKey = $this->isUser() ? 'whiteList' : 'whiteListPublic';
        $pluginsDefaultConfig = Configure::read(self::SETTINGS_PASSBOLT_PLUGINS, []);
        $res = [];

        foreach ($pluginsDefaultConfig as $pluginName => $pluginConfig) {
            foreach ($this->_getWhiteListPath($pluginConfig, $configKey) as $whiteListPath) {
                $res[] = $pluginName . '.' . $whiteListPath;
            }
        }

        return $res;
    }

    /**
     * Get whitelisted options paths.
     *
     * @return array list of whitelist path
     */
    protected function _getWhiteListPath(array $pluginConfig, string $configKey): array
    {
        $whiteListPaths = $this->isUser() ? $this->alwaysWhiteListed : [];

        $whiteListOptions = Hash::extract($pluginConfig, self::SETTINGS_VISIBILITY_KEY . '.' . $configKey);
        if (is_array($whiteListOptions)) {
            $whiteListPaths = array_merge($whiteListPaths, $whiteListOptions);
        }

        return $whiteListPaths;
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
