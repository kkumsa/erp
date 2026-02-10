<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Button/Action Labels
    |--------------------------------------------------------------------------
    */
    'buttons' => [
        'save' => 'Save',
        'cancel' => 'Cancel',
        'delete' => 'Delete',
        'create' => 'Create',
        'edit' => 'Edit',
        'update' => 'Update',
        'view' => 'View',
        'restore' => 'Restore',
        'force_delete' => 'Permanently Delete',
        'approve' => 'Approve',
        'reject' => 'Reject',
        'submit' => 'Submit',
        'search' => 'Search',
        'filter' => 'Filter',
        'reset' => 'Reset',
        'export' => 'Export',
        'import' => 'Import',
        'print' => 'Print',
        'download' => 'Download',
        'upload' => 'Upload',
        'close' => 'Close',
        'confirm' => 'Confirm',
        'back' => 'Back',
        'next' => 'Next',
        'previous' => 'Previous',
        'select' => 'Select',
        'select_all' => 'Select All',
        'deselect_all' => 'Deselect All',
        'add' => 'Add',
        'remove' => 'Remove',
        'add_item' => 'Add Item',
        'add_step' => 'Add Step',
        'mark_as_read' => 'Mark as Read',
        'restore_selected' => 'Restore Selected',
        'force_delete_selected' => 'Permanently Delete Selected',
        'match' => 'Match',
        'unmatch' => 'Unmatch',
        'save_profile' => 'Save Profile',
        'change_password' => 'Change Password',
    ],

    /*
    |--------------------------------------------------------------------------
    | Confirmation/Modal Messages
    |--------------------------------------------------------------------------
    */
    'confirmations' => [
        'delete' => 'Are you sure you want to delete this?',
        'delete_description' => 'This action cannot be undone.',
        'restore' => 'Are you sure you want to restore this item?',
        'restore_heading' => 'Confirm Restore',
        'force_delete' => 'This item will be permanently deleted. This action cannot be undone.',
        'force_delete_heading' => 'Confirm Permanent Deletion',
        'restore_selected' => 'Are you sure you want to restore all selected items?',
        'restore_selected_heading' => 'Restore Selected Items',
        'force_delete_selected' => 'The selected items will be permanently deleted. This action cannot be undone.',
        'force_delete_selected_heading' => 'Permanently Delete Selected Items',
        'approve' => 'Are you sure you want to approve this item?',
        'reject' => 'Are you sure you want to reject this item?',
        'notification_restore' => 'Are you sure you want to restore this notification?',
        'notification_restore_heading' => 'Restore Notification',
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Messages
    |--------------------------------------------------------------------------
    */
    'notifications' => [
        'created' => ':resource has been created.',
        'updated' => ':resource has been updated.',
        'deleted' => ':resource has been deleted.',
        'restored' => 'Restored successfully',
        'force_deleted' => 'Permanently deleted',
        'restored_count' => ':count item(s) restored',
        'force_deleted_count' => ':count item(s) permanently deleted',
        'approved' => 'Approved successfully.',
        'rejected' => 'Rejected.',
        'profile_updated' => 'Profile has been updated.',
        'password_changed' => 'Password has been changed.',
        'matching_success' => 'Payment matched successfully',
        'matching_failed' => 'Matching failed',
        'matching_already_processed' => 'Already processed',
        'matching_already_processed_body' => 'This deposit has already been processed.',
        'matching_not_found' => 'Deposit or invoice not found.',
        'unmatching_success' => 'Match removed successfully',
        'unmatching_failed' => 'Unmatch failed',
        'unmatching_not_found' => 'Processed deposit not found.',
        'saved' => 'Saved successfully.',
        'error' => 'An error occurred.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Section Headings
    |--------------------------------------------------------------------------
    */
    'sections' => [
        'basic_info' => 'Basic Information',
        'additional_info' => 'Additional Information',
        'personal_info' => 'Personal Information',
        'contact_info' => 'Contact Information',
        'company_info' => 'Company Information',
        'classification' => 'Classification',
        'schedule' => 'Schedule',
        'budget_and_status' => 'Budget & Status',
        'detail_content' => 'Details',
        'note' => 'Notes',
        'approval' => 'Approval',
        'approval_info' => 'Approval Information',
        'assignment_and_status' => 'Assignment & Status',
        'billing_and_status' => 'Billing & Status',
        'additional_settings' => 'Additional Settings',
        'work_info' => 'Work Information',
        'password_change' => 'Change Password',
        'conditions' => 'Conditions',
        'approval_steps' => 'Approval Steps',

        // Resource-specific sections
        'sales_info' => 'Sales Information',
        'customer_info' => 'Company Information',
        'invoice_info' => 'Invoice Information',
        'invoice_item_info' => 'Invoice Item Information',
        'contract_info' => 'Contract Information',
        'contract_terms' => 'Contract Terms',
        'contract_status' => 'Contract Status',
        'project_info' => 'Project Information',
        'task_info' => 'Task Information',
        'expense_info' => 'Expense Information',
        'payment_info' => 'Payment Information',
        'order_info' => 'Order Information',
        'order_items' => 'Order Items',
        'timesheet_info' => 'Timesheet Information',
        'account_info' => 'Account Information',
        'leave_request' => 'Leave Request',
        'leave_info' => 'Leave Information',
        'attendance_info' => 'Attendance Information',
        'milestone_info' => 'Milestone Information',
        'stock_info' => 'Stock Information',
        'stock_movement_info' => 'Stock Movement Information',
        'user_info' => 'User Information',
        'employee_info' => 'Employee Information',
        'product_category_info' => 'Product Category Information',
        'expense_category_info' => 'Expense Category Information',
        'deposit_info' => 'Deposit Information',
        'approval_flow_info' => 'Approval Flow Information',
        'department_info' => 'Department Information',
        'leave_type_info' => 'Leave Type Information',
    ],

    /*
    |--------------------------------------------------------------------------
    | Empty State Messages
    |--------------------------------------------------------------------------
    */
    'empty_states' => [
        'no_records' => 'No records found.',
        'no_deleted_items' => 'No deleted items',
        'trash_empty' => 'The trash is empty.',
        'no_notifications' => 'No notifications',
        'notifications_description' => 'Received notifications will appear here.',
        'no_login_history' => 'No login history',
        'login_history_description' => 'Login/logout history will appear here.',
        'no_results' => 'No results found.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Search/Filter Labels
    |--------------------------------------------------------------------------
    */
    'search' => [
        'placeholder' => 'Search...',
        'search' => 'Search',
        'filter' => 'Filter',
        'clear_filters' => 'Clear Filters',
        'all' => 'All',
        'select' => 'Select',
        'no_options' => 'No options available.',
        'select_first' => 'Please select first',
    ],

    /*
    |--------------------------------------------------------------------------
    | Approval Related
    |--------------------------------------------------------------------------
    */
    'approval' => [
        'pending' => 'Pending',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'cancelled' => 'Cancelled',
        'approval_request' => 'Approval Request',
        'consensus' => 'Consensus',
        'reference' => 'Reference',
        'approval_type_approve' => 'Approve (can approve/reject)',
        'approval_type_consensus' => 'Consensus (provide opinion, cannot reject)',
        'approval_type_reference' => 'Reference (view only, sends notification)',
        'specific_user' => 'Specific User',
        'role' => 'Role',
    ],

    /*
    |--------------------------------------------------------------------------
    | Navigation Groups
    |--------------------------------------------------------------------------
    */
    'nav_groups' => [
        'crm' => 'CRM',
        'finance' => 'Finance',
        'project' => 'Projects',
        'hr' => 'HR',
        'purchasing' => 'Purchasing',
        'inventory' => 'Inventory',
        'inventory_logistics' => 'Inventory & Logistics',
        'system' => 'System Settings',
        'my_settings' => 'My Settings',
    ],

    /*
    |--------------------------------------------------------------------------
    | Navigation Labels (Resources)
    |--------------------------------------------------------------------------
    */
    'nav_labels' => [
        'customers' => 'Customers',
        'contacts' => 'Contacts',
        'leads' => 'Leads',
        'opportunities' => 'Opportunities',
        'contracts' => 'Contracts',
        'payment_matching' => 'Payment Matching',
        'invoices' => 'Invoices',
        'invoice_items' => 'Invoice Items',
        'payments' => 'Payments',
        'bank_deposits' => 'Bank Deposits',
        'expenses' => 'Expenses',
        'expense_categories' => 'Expense Categories',
        'accounts' => 'Accounts',
        'projects' => 'Projects',
        'tasks' => 'Tasks',
        'timesheets' => 'Timesheets',
        'milestones' => 'Milestones',
        'departments' => 'Departments',
        'employees' => 'Employees',
        'leaves' => 'Leave Management',
        'leave_types' => 'Leave Types',
        'attendances' => 'Attendance',
        'suppliers' => 'Suppliers',
        'purchase_orders' => 'Purchase Orders',
        'purchase_order_items' => 'PO Items',
        'products' => 'Products',
        'product_categories' => 'Product Categories',
        'stocks' => 'Stock Status',
        'stock_movements' => 'Stock Movements',
        'warehouses' => 'Warehouses',
        'users' => 'Users',
        'roles' => 'Roles',
        'approval_flows' => 'Approval Flows',
        'trash' => 'Trash',
        'my_profile' => 'My Profile',
        'notification_history' => 'Notification History',
    ],

    /*
    |--------------------------------------------------------------------------
    | Model Labels (singular/plural)
    |--------------------------------------------------------------------------
    */
    'models' => [
        'customer' => 'Customer',
        'contact' => 'Contact',
        'lead' => 'Lead',
        'opportunity' => 'Opportunity',
        'contract' => 'Contract',
        'invoice' => 'Invoice',
        'invoice_item' => 'Invoice Item',
        'payment' => 'Payment',
        'bank_deposit' => 'Bank Deposit',
        'expense' => 'Expense',
        'expense_category' => 'Expense Category',
        'account' => 'Account',
        'project' => 'Project',
        'task' => 'Task',
        'timesheet' => 'Timesheet',
        'milestone' => 'Milestone',
        'department' => 'Department',
        'employee' => 'Employee',
        'leave' => 'Leave',
        'leave_type' => 'Leave Type',
        'attendance' => 'Attendance',
        'supplier' => 'Supplier',
        'purchase_order' => 'Purchase Order',
        'purchase_order_item' => 'PO Item',
        'product' => 'Product',
        'product_category' => 'Product Category',
        'stock' => 'Stock',
        'stock_movement' => 'Stock Movement',
        'warehouse' => 'Warehouse',
        'user' => 'User',
        'role' => 'Role',
        'approval_flow' => 'Approval Flow',
    ],

    /*
    |--------------------------------------------------------------------------
    | Status Labels (Common)
    |--------------------------------------------------------------------------
    */
    'statuses' => [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'draft' => 'Draft',
        'pending' => 'Pending',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
        'on_hold' => 'On Hold',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'overdue' => 'Overdue',
        'expired' => 'Expired',
        'valid' => 'Valid',
        'read' => 'Read',
        'unread' => 'Unread',
        'deleted' => 'Deleted',
        'processed' => 'Processed',
        'unprocessed' => 'Unprocessed',
    ],

    /*
    |--------------------------------------------------------------------------
    | Table/List Related
    |--------------------------------------------------------------------------
    */
    'table' => [
        'actions' => 'Actions',
        'bulk_actions' => 'Bulk Actions',
        'no_records' => 'No records found.',
        'showing' => 'Showing :first to :last of :total',
        'selected' => ':count selected',
        'per_page' => ':count per page',
        'sort_asc' => 'Ascending',
        'sort_desc' => 'Descending',
    ],

    /*
    |--------------------------------------------------------------------------
    | General Labels
    |--------------------------------------------------------------------------
    */
    'general' => [
        'yes' => 'Yes',
        'no' => 'No',
        'none' => 'None',
        'all' => 'All',
        'auto_generated' => 'Auto-generated',
        'won' => 'KRW',
        'currency_prefix' => '₩',
        'percent' => '%',
        'hours_suffix' => 'h',
        'days_suffix' => 'day(s)',
        'months_suffix' => 'month(s)',
        'items_count' => ':count item(s)',
        'login' => 'Login',
        'logout' => 'Logout',
        'login_failed' => 'Login Failed',
        'korean' => '한국어',
        'english' => 'English',
        'detail' => 'Detail',
    ],

    /*
    |--------------------------------------------------------------------------
    | Placeholders/Helper Texts
    |--------------------------------------------------------------------------
    */
    'placeholders' => [
        'select' => 'Select',
        'none' => 'None',
        'none_top_category' => 'None (top-level category)',
        'none_top_department' => 'None (top-level department)',
        'auto_generated' => 'Auto-generated',
        'select_project_first' => 'Please select a project first',
        'select_task' => 'Select task',
        'example_bank_account' => 'e.g. Woori Bank 1005-xxx-xxxx',
        'example_approval_name' => 'e.g. Purchase Order Default Approval',
    ],

    /*
    |--------------------------------------------------------------------------
    | Helper Texts
    |--------------------------------------------------------------------------
    */
    'helpers' => [
        'account_for_payment' => 'Bank/cash account for processing transactions',
        'default_approval_flow' => 'Set as the default approval flow for this target type. Used when no condition-matching flow is found.',
        'approval_conditions' => 'When conditions are set, this approval flow is automatically applied to matching documents. Without conditions, it is only used as a "default approval flow".',
        'approval_steps' => 'Add approval steps in order. Step numbers are assigned automatically.',
        'sales_account' => 'Sales account applied when selling products in this category',
        'purchase_account' => 'Purchase account applied when buying products in this category',
        'profile_description' => 'Update your name, email, and profile picture.',
        'password_description' => 'Change your password. Leave blank if you don\'t want to change it.',
        'unlimited' => 'Unlimited',
    ],

    /*
    |--------------------------------------------------------------------------
    | Page Titles
    |--------------------------------------------------------------------------
    */
    'pages' => [
        'dashboard' => 'Dashboard',
        'login_ip' => 'IP',
        'trash' => 'Trash',
        'payment_matching' => 'Payment Matching',
        'my_profile' => 'My Profile',
        'notification_settings' => 'Notification Settings',
        'notification_history' => 'Notification History',
    ],

    /*
    |--------------------------------------------------------------------------
    | Target Types (Approval Flow)
    |--------------------------------------------------------------------------
    */
    'target_types' => [
        'purchase_order' => 'Purchase Order',
        'expense' => 'Expense',
        'leave' => 'Leave',
        'timesheet' => 'Timesheet',
    ],

    /*
    |--------------------------------------------------------------------------
    | Event Labels
    |--------------------------------------------------------------------------
    */
    'events' => [
        'login' => 'Login',
        'logout' => 'Logout',
        'login_failed' => 'Login Failed',
    ],

    /*
    |--------------------------------------------------------------------------
    | Permissions
    |--------------------------------------------------------------------------
    */
    'permissions' => [
        'actions' => [
            'view' => 'View',
            'create' => 'Create',
            'update' => 'Update',
            'delete' => 'Delete',
            'approve' => 'Approve',
            'sign' => 'Sign',
            'convert' => 'Convert',
            'export' => 'Export',
            'adjust' => 'Adjust',
        ],
        'edit_title' => 'Edit Role: :name',
        'saved' => 'Permissions have been saved.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Widget Headings
    |--------------------------------------------------------------------------
    */
    'widgets' => [
        'active_projects' => 'Active Projects',
        'latest_invoices' => 'Latest Invoices',
        'monthly_revenue_expense' => 'Monthly Revenue/Expense',
        'revenue' => 'Revenue',
        'expense' => 'Expense',
    ],

    /*
    |--------------------------------------------------------------------------
    | Stats Widget
    |--------------------------------------------------------------------------
    */
    'stats' => [
        'monthly_revenue' => 'Monthly Revenue',
        'increase' => ':value% increase',
        'decrease' => ':value% decrease',
        'pending_amount' => 'Pending Amount',
        'payment_pending' => 'Awaiting payment',
        'active_projects' => 'Active Projects',
        'active_projects_desc' => 'Active projects',
        'customers_employees' => 'Customers / Employees',
        'customers_employees_value' => ':customers companies / :employees staff',
        'active_customers_employees' => 'Active customers / Active employees',
        'paid_basis' => 'Based on paid invoices',
        'monthly_expense' => 'Monthly Expenses',
        'approved_expense' => 'Approved expenses',
        'pending_approval_expense' => 'Pending Approval',
        'count_suffix' => ':count',
        'needs_review' => 'Needs review',
        'active_employees' => 'Active Employees',
        'person_count' => ':count',
        'currently_active' => 'Currently active',
        'today_attendance' => 'Today\'s Attendance',
        'today_attendance_desc' => 'Today\'s attendance',
        'pending_leave' => 'Pending Leave Requests',
        'new_hires_month' => 'New Hires This Month',
        'year_month_format' => ':month/:year',
        'my_tasks' => 'My Tasks',
        'in_progress_tasks' => 'Tasks in progress',
        'participating_projects' => 'My Projects',
        'in_progress' => 'In progress',
        'remaining_leave' => 'Remaining Leave',
        'day_count' => ':count day(s)',
        'year_basis' => 'Year :year',
        'pending_expenses' => 'Pending Expenses',
        'submitted_expenses' => 'Submitted expenses',
        'projects_count' => ':count',
    ],

    /*
    |--------------------------------------------------------------------------
    | Activity Log Panel
    |--------------------------------------------------------------------------
    */
    'activity_log' => [
        'title' => 'Activity Log',
        'no_activity' => 'No activity recorded.',
        'created' => 'Created',
        'updated' => 'Updated',
        'deleted' => 'Deleted',
        'system' => 'System',
        'and_more' => '... and :count more',
    ],

    /*
    |--------------------------------------------------------------------------
    | Approval Status Panel
    |--------------------------------------------------------------------------
    */
    'approval_panel' => [
        'title' => 'Approval Progress',
        'approval_line' => 'Approval Flow',
        'in_progress' => 'In Progress',
        'step_progress' => 'Step :current/:total',
        'final_approved' => 'Final Approved',
        'rejected' => 'Rejected',
        'requester' => 'Requester',
        'request_date' => 'Request Date',
        'completed_date' => 'Completed Date',
        'step_n' => 'Step :n',
        'skip' => 'Skipped',
        'waiting' => 'Waiting',
        'upcoming' => 'Upcoming',
        'role_label' => '(Role)',
        'auto_skip_reason' => 'Requester has approval authority (auto-skipped)',
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Matching Page
    |--------------------------------------------------------------------------
    */
    'payment_matching' => [
        'invoice_list' => 'Invoice List',
        'deposit_list' => 'Deposit List',
        'unpaid_partial' => 'Unpaid/Partial',
        'unprocessed' => 'Unprocessed',
        'processed' => 'Processed',
        'invoice_number_col' => 'Invoice #',
        'invoice_amount' => 'Invoice Amount',
        'processing_col' => 'Status',
        'deposit_date' => 'Deposit Date',
        'depositor' => 'Depositor',
        'no_invoices' => 'No invoices found.',
        'no_deposits' => 'No deposits found.',
        'confirm_title' => 'Confirm Payment Match',
        'confirm_depositor' => 'Depositor:',
        'confirm_amount' => 'Amount:',
        'confirm_invoice' => 'Invoice:',
        'confirm_message' => 'Register this deposit as a payment for the selected invoice?',
        'register_payment' => 'Register Payment',
        'unmatch_confirm' => 'Remove the payment match for this deposit?',
        'drag_hint' => 'Drag to an invoice on the left',
        'select_invoice_below' => 'Select an invoice below',
        'deposit_matching_note' => 'Deposit match: :name',
        'matching_success_body' => ':name ₩:amount → :invoice',
        'unmatching_success_body' => 'Payment match for :name has been removed.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Trash Page
    |--------------------------------------------------------------------------
    */
    'trash_page' => [
        'type_col' => 'Type',
        'name_col' => 'Name',
        'detail_1' => 'Detail 1',
        'detail_2' => 'Detail 2',
    ],

    /*
    |--------------------------------------------------------------------------
    | View Mode Toggle
    |--------------------------------------------------------------------------
    */
    'view_mode' => [
        'page' => 'Page',
        'slide' => 'Slide-over',
    ],

    /*
    |--------------------------------------------------------------------------
    | Navigation Order (Sidebar)
    |--------------------------------------------------------------------------
    */
    'nav_order' => [
        'change_order' => 'Change order',
        'apply_order' => 'Apply order',
        'reset_order' => 'Reset order',
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Settings Page
    |--------------------------------------------------------------------------
    */
    'notification_settings' => [
        'saved' => 'Notification settings have been saved.',
        'category_work' => 'Work',
        'category_approval' => 'Approval',
        'category_crm' => 'CRM',
        'category_finance' => 'Finance/Inventory',
        'task_assigned_label' => 'Task Assigned',
        'task_assigned_desc' => 'When a task is assigned to you',
        'task_status_changed_label' => 'Task Completed',
        'task_status_changed_desc' => 'When a task in your project is completed',
        'milestone_completed_label' => 'Milestone Completed',
        'milestone_completed_desc' => 'When a milestone in your project is completed',
        'leave_requested_label' => 'Leave Request',
        'leave_requested_desc' => 'When a new leave request is submitted',
        'leave_status_changed_label' => 'Leave Approved/Rejected',
        'leave_status_changed_desc' => 'When your leave request is approved or rejected',
        'expense_submitted_label' => 'Expense Claim',
        'expense_submitted_desc' => 'When a new expense approval request is submitted',
        'expense_status_changed_label' => 'Expense Approved/Rejected',
        'expense_status_changed_desc' => 'When your expense claim is approved or rejected',
        'purchase_order_approval_label' => 'PO Approval',
        'purchase_order_approval_desc' => 'When a new purchase order approval request is submitted',
        'lead_assigned_label' => 'Lead Assigned',
        'lead_assigned_desc' => 'When a new lead is assigned to you',
        'opportunity_stage_changed_label' => 'Opportunity Stage Changed',
        'opportunity_stage_changed_desc' => 'When your opportunity stage changes',
        'invoice_overdue_label' => 'Invoice Overdue',
        'invoice_overdue_desc' => 'When an invoice payment is overdue',
        'contract_expiring_label' => 'Contract Expiring',
        'contract_expiring_desc' => 'When a contract is nearing expiration',
        'low_stock_label' => 'Low Stock',
        'low_stock_desc' => 'When product stock falls below minimum quantity',
        'payment_received_label' => 'Payment Received',
        'payment_received_desc' => 'When a payment is received for your invoice',
    ],

];
