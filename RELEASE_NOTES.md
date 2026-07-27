Release song: https://www.youtube.com/watch?v=JfP3sB-hke4

## [5.14.0-test.5] - 2026-07-27
### Added
- PB-49943 Add a model cache health check service
- PB-50621 Add recover flow support for OAuth2 & ADFS SSO providers
- PB-52153 Add a command to insert dummy data into the email_queue table
- PB-52421 Add more verbose logs when an SSO provider response is in an incorrect format

### Fixed
- PB-48004 Fix deprecation warnings in makeCsrfCookieSecureIfRequestIsSsl
- PB-52585 Fix resource sharing after inserting dummy data
- PB-52727 Fix typo in SSO settings activated email
- PB-52952 Return 400 instead of 409 when the email is not found in the expected place from a SCIM IdP
- PB-52592 Fix account recovery response email not being sent to custom RBAC roles
- PB-53118 Fix unsuspend action using LDAP with AD is not working as expected

### Security
- PB-29515 Fix MFA remember me policy bypass (INC-2046)
- PB-32421 Validate and filter user-edit request data via a modeless form instead of `UsersEditController::_validateRequestData`
- PB-51819 Upgrade composer/composer (AIKIDO-2026-688935)
- PB-52440 Fix a disabled TOTP provider still being able to mint an MFA verification cookie via non-JSON `/mfa/verify/totp`
- PB-52441 Fix refresh-token rotation being replayable concurrently due to non-atomic consumption
- PB-52452 Harden secret revocation on share
- PB-52457 Block renaming of reserved roles to close a delete-protection bypass
- PB-52461 Fix last-resource-type guard incorrectly counting soft-deleted rows
- PB-52463 Restrict `filter[is-deleted]` on the resource-types index to admins only
- PB-52538 Upgrade js-yaml
- PB-52599 Upgrade spomky-labs/otphp (GHSA-g7m4-839x-ch6v, GHSA-2jx3-65f3-xr8r)
- PB-52687 Fix composer security advisory affecting the cakephp/authentication package (CVE-2026-55590)
- PB-52695 Fix Aikido advisory in the guzzlehttp/guzzle library (AIKIDO-2026-646305)
- PB-52696 Upgrade phpseclib/phpseclib (GHSA-m557-wrgg-6rp4)
- PB-53143 Update CakePHP version to 5.3.7

### Maintenance
- PB-50620 Move the SSO Azure `stage3.php` template to `success/stage3.php`
- PB-52146 Allow additional database drivers to be registered in the healthcheck via dependency injection
- PB-52285 Add test to verify JWT refresh token behaviour with a suspended user
- PB-52553 Add PostgreSQL support to the create_passbolt_db script
- Renovate: Update dependency bacon/bacon-qr-code to v3.1.1
- Renovate: Update dependency cakephp/debug_kit to v5.2.4
- Renovate: Update dependency cakephp/plugin-installer to v2.0.2
- Renovate: Update dependency composer/composer to v2.10.2
- Renovate: Update dependency directorytree/ldaprecord to v3.8.6
- Renovate: Update dependency donatj/phpuseragentparser to v1.12.0
- Renovate: Update dependency ergebnis/phpunit-slow-test-detector to v2.24.0
- Renovate: Update dependency imagine/imagine to v1.5.4
- Renovate: Update dependency phpstan/phpstan to v1.12.33
- Renovate: Update dependency phpunit/phpunit to v11.5.55
- Renovate: Update dependency psalm/phar to v6.16.1
- Renovate: Update dependency seec/phpunit-consecutive-params to v1.2
