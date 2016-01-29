<?php
    include_once('inc/header.php');
?>

        <div class="wrapper">
            <?php
                include_once("inc/side.php");
            ?>

            <div class="content">
                <div class="modules">
                    <div id="user-modules"></div>
                    <div id="selected-module-details">
                        <div class="go-back">
                            <a href="#" class="go-back-to-all-modules">
                                <span class="c-icon-return"></span>
                                <span class="label">Go back to all modules</span>
                            </a>
                        </div>
                        <div id="selected-module"></div>
                        <div id="selected-module-tasks" class="tasks"></div>
                    </div>
                </div>

                <script type="text/html" id="list_modules">
                    <%
                        if (modules.length) {
                            _.each(modules, function (module, index) {
                                var percentage = (module.user_progress.progress * 100) / module.user_progress.task_count,
                                    moduleStrigified = JSON.stringify(module);
                    %>
                        <div class="module module-id-<%=module.module_id %> <%= module.name_as_class %>" data-module-id="<%= module.module_id %>" data-module='<%= moduleStrigified %>'>
                            <div class="module-thumb">
                                <img src="img/<%= module.thumb_url%>" />
                            </div>
                            <div class="module-details">
                                <h2><%= module.name %></h2>
                                <p><%= module.brief_desc%></p>
                            </div>
                            <div class="progress-bar">
                                <div class="completed" style="width: <%=percentage %>%;"></div>
                                <span class="text">

                                    <span class="number"><%=module.user_progress.progress %></span>/<span class="out-of"><%= module.user_progress.task_count %></span> tasks
                                </span>
                            </div>
                        </div>
                    <%
                            });
                        }
                        else {
                    %>
                            <div class="no-modules">
                                <h2>Get started now!</h2>
                                <p>You currently don't have any modules assigned to you. Start achieving your dream of settling in and <a href="#modules-popup" class="open-popup-link">add a new module</a>.</p>
                            </div>
                    <%
                        }
                    %>

                </script>

                <script type="text/html" id="list_tasks">
                    <%
                        if (tasks.length) {
                            _.each(tasks, function (task, index) {
                    %>
                    <div class="task task-<%= task.id %>" data-task-id="<%= task.id %>">
                        <div class="task-info">
                            <%
                            var tick = "";
                            if (task.task_progress.is_complete === "1") {
                                tick = "tick";
                            }
                            %>
                            <div class="completed <%= tick %>">
                                <div class="completed-inner">
                                    <span class="c-icon c-icon-tick"></span>
                                </div>
                            </div>

                            <div class="task-details">
                                <div class="task-details-inner">
                                    <h3><%= task.name %></h3>
                                    <p class="task-description">
                                        <%= task.brief_desc %>
                                    </p>
                                </div>
                            </div>

                            <div class="expand">
                                <div class="expand-inner">
                                    <span class="c-icon c-icon-expand"></span>
                                </div>
                            </div>
                        </div>

                        <div class="steps">
                            <ul>
                                <%
                                    _.each(task.steps, function (step, index) {
                                        var checked = (step.step_progress.is_complete === "1") ? "checked" : "";
                                %>
                                <li>
                                    <input type="checkbox" id="step_<%= step.id %>" data-step-id="<%= step.id %>" name="step_<%= step.id %>" <%= checked %> />
                                    <label for="step_<%= step.id %>"><%= step.name %></label>
                                    <a href="#" class="expand-step j-expand-step">details</a>
                                    <div class="step-desc hide"><%= step.brief_desc %></div>
                                </li>
                                <%
                                    });
                                %>
                            </ul>
                        </div>
                    </div>
                    <%
                            });
                        }
                        else {
                    %>
                            <div class="no-tasks">
                                <h2>No tasks have beed assigned!</h2>
                                <p>It appears no tasks have yet been assigned to this module. Please check again later.</p>
                            </div>
                    <%
                        }
                    %>
                </script>
            </div>
        </div>


        <div id="modules-popup" class="popup modules-popup mfp-hide">
            <div class="add-modules-container">
                <h2>Add module</h2>
                <p class="description">Some description about adding new modules</p>

                <div id="module-list" class="module-list"></div>
            </div>
        </div>

        <script type="text/html" id="add_module">
            <%
                _.each(modules, function (module, index) {
            %>
                <div class="module module-id-<%=module.module_id %> <%= module.name_as_class %>" data-module-id="<%= module.module_id %>">
                    <div class="module-thumb">
                        <img src="img/<%= module.thumb_url %>" />
                    </div>
                    <div class="module-details">
                        <h2><%= module.name %></h2>
                        <p><%= module.brief_desc %></p>
                    </div>
                    <div class="add-module-action">
                        <span class="already-added <% (!module.already_added) ? print('hidden') : print('') %>">This module has been added</span>
                        <span class="add-icon <% (module.already_added) ? print('hidden') : print('') %>">Add this module</span>
                    </div>
                </div>
            <%
                });
            %>
        </script>

        <div id="myprofile-popup" class="popup myprofile-popup mfp-hide">
            <div class="my-profile-container">
                <h2>My Profile</h2>
                <p class="description">You can update your profile details using the form below. Please make sure your details are always up-to-date.</p>

                <div class="status">
                    <p class="success">Your details have been updated successfully!</p>
                    <p class="error">There was an error while updating your details, please try again later.</p>
                </div>

                <form id="my-profile-form" class="my-profile-form" action="logic/myprofile.php" method="post">
                    <input type="text" name="first_name" class="first-name" placeholder="First name" />
                    <input type="text" name="last_name" class="last-name" placeholder="Last name" />
                    <input type="text" name="age" class="age" placeholder="Age" />
                    <input type="hidden" name="user_id" class="user-id" />
                    <input type="hidden" name="myprofile_flag" value="1" />
                    <input type="submit" value="Update" />
                </form>
            </div>
        </div>

        <div id="module-completed-popup" class="popup module-completed-popup mfp-hide">
            <div class="module-completed-container">
                <div class="top-section">
                    <h3>Well done!</h3>
                    <p>You have successfully completed this module.</p>
                    <a href="#" class="button go-back-to-all-modules">Go back to all modules</a>
                </div>
                <div class="bottom-section mfp-hide">
                    <p>You are now ready to proceed to your next challenge. Go back to your modules</p>
                    <p class="all-completed">We see that you have completed all your modules. Add new module</p>
                </div>
            </div>
        </div>

<?php
    include_once('inc/footer.php');
?>
