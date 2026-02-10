# Pull Request Checklist:

## Pre-Approval

- [ ] Does the PR have a **release-notes-friendly title**?
- [ ] There is a **description section** in the pull request that details what the proposed changes do. It can be very brief if need be, but it ought to exist.
- [ ] Hotfixes should be branched off of the `master` branch and PR'd using the **squash & merge** option into the `Hotfix-x.x.x` branch.

- [ ] All new **text** is preferably **internationalized** (i.e., no end-user-visible text is hard-coded on the PHP pages), and the [spreadsheet tracking internationalizations](https://docs.google.com/spreadsheets/d/133fps9w2pUCEjUA6IGCcQotk7dn9KvepMXJ2IWUZsE8/edit?usp=sharing) has been updated either with a new row or with checkmarks to existing rows.
- [ ] There are **no linter errors**
- [ ] New features have **responsive design** (i.e., look aesthetically pleasing both full screen and with small or mobile screens)
- [ ] [**Symbiota coding standards**](https://docs.google.com/document/d/1-FwCZP5Zu4f-bPwsKeVVsZErytALOJyA2szjbfSUjmc/edit?usp=sharing) have been followed
- [ ] If any files have been reformatted (e.g., by an **autoformatter**), the reformat is its own, **separate commit** in the PR
- [ ] Comment which **GitHub issue(s)**, if any does this PR address
- [ ] If this PR makes any changes that would require **additional configuration** of any Symbiota portals outside of the files tracked in this repository, make sure that those changes are **detailed in [this document]**(https://docs.google.com/document/d/1T7xbXEf2bjjm-PMrlXpUBa69aTMAIROPXVqJqa2ow_I/edit?usp=sharing)
- [ ] If this **feature** has not been **documented** in [https://docs.symbiota.org/](https://docs.symbiota.org/) OR if changes are needed to the documentation, create a new github issue in [https://github.com/Symbiota/Symbiota/issues](https://github.com/Symbiota/Symbiota/issues) **labeled as documentation** and **add a link** to it herein.
- [ ] If there are **merge conflicts** with this PR's **parent branch, resolve** them before marking the PR as ready for review.

## Post-Approval

- [ ] It is the code author's responsibility to **merge** their own pull request after it has been approved
- [ ] Remember to use the **squash & merge** option for a merge into the `hotfix` branch
- [ ] Use the **merge** option (not squashed) for merges from the `hotfix` branch into the `master` branch.
  - [ ] a **subsequent PR from `master`** into `Development` should be made with the **merge** option (i.e., no squash)
  - [ ] **Immediately** **delete the `hotfix` branch** and create a new `hotfix` branch
  - [ ] **increment** the Symbiota **version** number in the symbase.php file and commit to the `hotfix` branch

Thanks for contributing and keeping it clean!
