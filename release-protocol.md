# Release protocol

## Alert the team

A release PR should not happen while there are remaining work items in code reivew or QA.

Consult the team if there are still items in QA, or if other previous merges need to be omitted from the release.

The Development branch should be locked during the release procedure.
Note: this locked state should already be the case while the Development branch is being QAed (refer to the pull request template).

## Merge and create the PR

First, resolve any potential merge conflicts with main on the develop branch.

1. `git checkout master`
2. `git pull origin master`
3. `git checkout Development`
4. `git pull origin Development`
5. `git merge master` (there should be no merge conflicts)
6. Update the version in symbbase.php.
7. Add the version change, commit, and push to Development.

Then, issue a pull request for merging the Development branch into the master branch. Await approval, and then merge. Note: do NOT use the "squash and merge" method; it will make subsequent merges with the Development branch more difficult. Instead, use the "Create a merge commit" option.

Note: if you encounter merge conflicts when merging Development into master, something went wrong. Investigate what happened thoroughly before continuing.

## Draft a new release

After completion of the previous steps, draft a new release of the master branch on GitHub:

1. Navigate to [https://github.com/BioKIC/Symbiota/releases/new](https://github.com/BioKIC/Symbiota/releases/new).
2. Designate the target as the master branch.
3. Create a new tag following the SemVer pattern outlined [here](https://semver.org/) (vX.Y.Z). Note that this exact pattern (no period between v and the first name, for instance) must be followed explicitly. The release title should be the same as the tag. You can leave the release description blank.
4. Publish the release.
5. Unlock the Development branch.
6. Notify the team about the release and that the Development branch has been unlocked.
