# we4v

#### _A social-governance and direct-democracy online software that cements power to the local level, while enabling only information-sharing and administrative functionality to networked groups and teams spanning geographic regions larger than the local_.

### Philosophy
Even though I'm using Laravel for the backend, I've avoided relying too much, if at all, on model relationships and the elegant, expressive code they afford, for my database queries. My goal has been to go to disk as little as possible ... for performance reasons. Hence the tortured scripts fetching complex data structures from multiple tables, and the formatting scripts – all in the App/Actions folder with model-name subfolders – that compile that complex data into arrays I return to InertiaJs/Vue. In other words, the philosophy I've chosen, in all my amateur enthusiasm, favours the end user and thus also response times over code maintainability _to some degree_. Code maintainability is very important to me, almost a joint top priority with usability, response times and resource efficiency, but I do sacrifice it to user satisfaction as just described.

### Code structure for form-completion checking
we4v's check-form-completion system is _very_ complicated indeed; I thought I'd better sketch out its elements here.

In manageModals.js, which is in `./resources/js/Pages/Composables/manageModals.js`, there are four methods handling the checking system I've set up to enable/disable each form's submit button, and apply the styles that indicate visually the enabled and disabled states, handled via the boolean variable `greyButtonEnabled`, which is passed to the relevant Vue component via a prop. (Forms in we4v are overwhelmingly delivered within modals.) These are the names of the relevant methods with descriptions:

1. `checkIfUserMaySubmit(mode)`: Runs checks using `getElementById` by taking a `mode` variable that carries strings such as 'group' or 'team' to set the CSS ids. Once all the boolean variables have been set, it then determines whether the relevant submit button should be enabled or disabled.

2. `checkIfProjectGroupSelected()`: _Like it says on the tin_. When creating a new Project, it must be assigned to a Group to be submitted.

3. `checkIfRoleInputFieldsFilled()`: Groups and Teams have members, and members (Associates) have roles. If, when clicking a checkbox to invite a specific Associate to join a Group or Team, the user fails to enter a string into the Role input field, or fills the text field but fails to click its corresponding checkbox, the border of that field is coloured red and the submit button either remains disabled or is changed to disabled. This method plays a major role in that calculation. Thie method is a shell method, so to speak, that calls the workhorses `checkRolesAgainstAssocs()` and `checkAssocsAgainstRoles()`, before finally calling `checkIfUserMaySubmit(mode)` described in point 1. above.

4. `checkIfTaskAssigneeSelected()`: To be completed.

Next up are the methods and variables in MyGroups.vue:

1. `addRemoveAssociate(mode, user_id_role)`: This method primarily adds or removes associates to/from an array/object that is then comsumed by both `submitGroupData()` and `submitTeamData()` to save/update Groups/Teams and their member details. It is 'borrowed' by the check-form-completion system to fire the methods `checkIfRoleInputFieldsFilled(user_id_role)` and `checkIfUserMaySubmit(mode)` every time a user clicks an Associate checkbox.

2. `onCollectMemberRoles()`: Every time a user exits focus from one the role input fields, this method is called. It toggles the boolean variable `selectedAssoc`, assigns a user_id to the variable `roleUserId`, then calls the method `checkIfRoleInputFieldsFilled(user_id)`. This latter method has to be called every time a determination must be made as to what colour border the role input field ought to display. The truthiness of `selectedAssoc` is needed for:

3. `watch(selectedAssoc)`: Not strictly a method, but every time `selectedAssoc` changes state, this method has to be fired. It sets the role-input-field's border colour.

Next up are the methods and variables in MyProjects.vue:

(To be completed)

Finally, the DOM data and triggers/variables therein:

1. Vue component `Input.vue` for `groupName`, `groupDescription`, `teamName` and `teamFunction`: Fires `@check-if-user-may-submit="checkIfUserMaySubmit('group/team')"` on blur in the `MyGroups.vue` page. Ditto for `MyProjects.vue` with its 'project' and 'task' modes, names and descriptions, start and end dates, etc. CSS ids are critical here: `:id="'groupName'"`, `:id="'groupDescription'"`, `:id="'teamName'"`, `:id="'teamFunction'"`, etc. See code in method `checkIfUserMaySubmit()` in `manageModals.js` to see what `id` values are required.

2. Standard HTML input for Associate checkboxes: `@click="addRemoveAssociate('group', associate.user_id), selectedAssoc = !selectedAssoc, roleUserId = associate.user_id"`. See `addRemoveAssociate(mode, user_id_role)` above. The CSS `id` of these elements is critical: `:id="'checkbox_'+associate.user_id"`.

3. Vue component `InputNoLabel.vue`: Calls `@collect-member-roles="onCollectMemberRoles"` on blur. See eponymous point 2. above. CSS `class` is critical: `class="assocRoles"`, which becomes `class="assocRolesEdit"` for edit modals.

4. Vue component `ButtonGrey.js`: Has to have the id 'submitForm', this per modal form.