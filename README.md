SrMemberships
====================

SrMemberships offers several workflows for importing people into courses and
groups:
- The first workflow imports a list of account names (logins) and automatically enrolls the corresponding accounts into the course or group.
- The second workflow offers to automatically enroll people from local or global roles.
- The third workflow imports a list of matriculation numbers and automatically enrolls the corresponding accounts into the course or group.
  - This workflow comes with an added option of creating non-existent accounts, if the imported data contains at least login-names, passwords and mail-addresses.

## Installation

Start at your ILIAS root directory

mkdir -p Customizing/global/plugins/Services/Cron/CronHook/  
cd Customizing/global/plugins/Services/Cron/CronHook/  
git clone https://github.com/srsolutionsag/SrMemberships.git

As ILIAS administrator go to "Administration->Plugins" and install/activate the
plugin or install via CLI.
Requirements

    ILIAS 8.x
    PHP >= 7.4

## ILIAS Plugin SLA

We love and live the philosophy of Open Source Software! Most of our
developments, which we develop on behalf of customers or in our own work, we
make publicly available to all interested parties free of charge
at https://github.com/srsolutionsag.

Do you use one of our plugins professionally? Secure the timely availability of
this plugin also for future ILIAS versions by signing an SLA. Find out more
about this at https://sr.solutions/plugins.

Please note that we only guarantee support and release maintenance for
institutions that sign an SLA.

## Contact

- support@sr.solutions
- https://sr.solutions
