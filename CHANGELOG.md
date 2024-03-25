# Changelog

## Version 2.1.3
- [FIX] unsupported object type lead to an error
- [FIX] array access for object_type
- [FIX] write errors to log instead of collection in cron result

## Version 2.1.2
- [FIX] migrate other modes to new modes (fixed step)

## Version 2.1.1
- [FIX] migrate other modes to new modes

## Version 2.1.0
- [FIX] an issue in some database calls
- [FIX] migrate old mode 8 to new SyncMode:64
- [FEATURE] added support CLI installation

## Version 2.0.1
- [FIX] an issue with newer PHP versions (wrong type)
- [FIX] updated classmap to avoid php version check

## Version 2.0.0
- Support for ILIAS 8

## Version 1.0.0

- The plugin currently supports three workflows: Import of persons via login/external account, import of persons via matriculation numbers, enrollment of persons based on role affiliation.
- Logins and matriculation numbers can be imported as text lists or files (TXT, CSV, Excel).
- Three processing methods are available: Enroll persons of the list, match list of persons (enroll missing memberships, remove unnecessary memberships) or remove list of persons.
  The actions can be executed ad-hoc when saving or, if desired, also regularly as a cronjob.
- The workflows are currently available in courses and groups (configurable per workflow).
- Workflows can be made available to persons who can manage members in the respective object or be granted to a set of local and global roles.
- More workflows will be added in future versions of the plugin, contact support@sr.solutions if you are interested in another workflow.


