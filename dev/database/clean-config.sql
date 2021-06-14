delete from flw_guidancefields_flw_flowsteps_c
where flow_step_id not in (select id from flw_flowsteps);

delete from flw_guidancefields_flw_flowsteps_c
where flow_field_id not in (select id from flw_guidancefields);

delete from flw_flowsteps_flw_flowstepproperties_1_c
where property_id not in (select id from properties);

delete from flw_flowsteps_flw_flowstepproperties_1_c
where flow_step_id not in (select id from flw_flowsteps);

delete from flw_guidancefieldsvalidators_conditions
where child_id not in (select id from flw_guidancefieldvalidators);

delete from fltrs_fieldsgroup_fltrs_fields_1_c
where fltrs_fieldsgroup_fltrs_fields_1fltrs_fields_idb not in (select id from fltrs_fields);

delete from dash_dashboardmenuactiongroup_dash_menuactions
where dashboard_menu_action_id not in (select id from dash_menuactions);

delete from conditional_message_validators
where conditional_message_id not in (select id from conditionalmessage);

delete from conditional_message_validators
where validator_id not in (select id from flw_guidancefieldvalidators);

delete from list_topbar_list_sorting_options_c
where list_sorting_option_id not in (select id from list_sorting_options);

delete from dash_dashboard_dash_dashboardproperties_c
where property_id not in (select id from properties);

delete from dash_dashboard_dash_dashboardproperties_c
where dashboard_id not in (select id from dash_dashboard);

delete from flw_guidancefields_flw_guidancefieldvalidators_1_c
where validator_id not in (select id from flw_guidancefieldvalidators);

delete from grid_panels_flw_guidancefieldvalidators_1_c
where grid_panel_id not in (select id from grid_panels);

delete from acl_roles_users
where acl_role_id not in (select id from acl_roles);

delete from acl_roles_actions
where acl_role_id not in (select id from acl_roles);

delete from acl_roles_actions
where acl_action_id not in (select id from acl_actions);

delete from securitygroups_acl_roles
where acl_role_id not in (select id from acl_roles);

delete from securitygroups_acl_roles
where security_group_id not in (select id from securitygroups);

delete from securitygroups_users
where user_id not in (select id from users);

delete from securitygroups_users
where securitygroup_id not in (select id from securitygroups);

delete from flw_guidancefieldsvalidators_conditions
where parent_id not in (select id from flw_guidancefieldvalidators);

delete from trans_translation
where translation is null;
