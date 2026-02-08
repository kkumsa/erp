<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Common Fields (used across multiple resources)
    |--------------------------------------------------------------------------
    */
    'name' => 'Name',
    'title' => 'Title',
    'code' => 'Code',
    'description' => 'Description',
    'status' => 'Status',
    'priority' => 'Priority',
    'type' => 'Type',
    'memo' => 'Memo',
    'note' => 'Note',
    'reason' => 'Reason',
    'color' => 'Color',
    'is_active' => 'Active',
    'is_default' => 'Default',
    'sort_order' => 'Sort Order',
    'created_at' => 'Created At',
    'updated_at' => 'Updated At',
    'deleted_at' => 'Deleted At',

    /*
    |--------------------------------------------------------------------------
    | Date/Time Fields
    |--------------------------------------------------------------------------
    */
    'date' => 'Date',
    'start_date' => 'Start Date',
    'end_date' => 'End Date',
    'due_date' => 'Due Date',
    'deadline' => 'Deadline',
    'issue_date' => 'Issue Date',
    'order_date' => 'Order Date',
    'expected_date' => 'Expected Delivery Date',
    'payment_date' => 'Payment Date',
    'expense_date' => 'Expense Date',
    'hire_date' => 'Hire Date',
    'birth_date' => 'Date of Birth',
    'resignation_date' => 'Resignation Date',
    'actual_end_date' => 'Actual End Date',
    'expected_close_date' => 'Expected Close Date',
    'actual_close_date' => 'Actual Close Date',
    'completed_date' => 'Completed Date',
    'signed_at' => 'Signed At',
    'approved_at' => 'Approved At',
    'deposited_at' => 'Deposited At',
    'processed_at' => 'Processing Status',

    /*
    |--------------------------------------------------------------------------
    | Financial Fields
    |--------------------------------------------------------------------------
    */
    'amount' => 'Amount',
    'total_amount' => 'Total',
    'subtotal' => 'Subtotal',
    'tax_amount' => 'Tax',
    'tax_rate' => 'Tax Rate (%)',
    'unit_price' => 'Unit Price',
    'unit_cost' => 'Unit Cost',
    'quantity' => 'Quantity',
    'unit' => 'Unit',
    'discount' => 'Discount (%)',
    'paid_amount' => 'Paid Amount',
    'balance' => 'Balance',
    'hourly_rate' => 'Hourly Rate',
    'budget' => 'Budget',
    'base_salary' => 'Base Salary',
    'expected_revenue' => 'Expected Revenue',
    'probability' => 'Probability',
    'weighted_amount' => 'Weighted Amount',

    /*
    |--------------------------------------------------------------------------
    | Progress/Time Fields
    |--------------------------------------------------------------------------
    */
    'progress' => 'Progress',
    'hours' => 'Hours',
    'estimated_hours' => 'Estimated Hours',
    'actual_hours' => 'Actual Hours',
    'days' => 'Days',
    'annual_leave_days' => 'Annual Leave Days',
    'duration_months' => 'Duration (Months)',

    /*
    |--------------------------------------------------------------------------
    | Person/Entity Relationship Fields
    |--------------------------------------------------------------------------
    */
    'customer' => 'Customer',
    'customer_id' => 'Customer',
    'supplier' => 'Supplier',
    'supplier_id' => 'Supplier',
    'employee' => 'Employee',
    'employee_id' => 'Employee',
    'manager' => 'Manager',
    'manager_id' => 'Project Manager',
    'project' => 'Project',
    'project_id' => 'Project',
    'contract' => 'Contract',
    'contract_id' => 'Contract',
    'opportunity' => 'Opportunity',
    'opportunity_id' => 'Opportunity',
    'invoice' => 'Invoice',
    'invoice_id' => 'Invoice',
    'task' => 'Task',
    'task_id' => 'Task',
    'milestone' => 'Milestone',
    'milestone_id' => 'Milestone',
    'product' => 'Product',
    'product_id' => 'Product',
    'warehouse' => 'Warehouse',
    'warehouse_id' => 'Warehouse',
    'department' => 'Department',
    'department_id' => 'Department',
    'account' => 'Account',
    'account_id' => 'Account',
    'category' => 'Category',
    'category_id' => 'Category',
    'leave_type' => 'Leave Type',
    'leave_type_id' => 'Leave Type',
    'contact' => 'Contact',
    'contact_id' => 'Contact',
    'approver' => 'Approver',
    'approved_by' => 'Approved By',
    'recorder' => 'Recorder',
    'recorded_by' => 'Recorded By',
    'creator' => 'Creator',
    'assigned_to' => 'Assigned To',
    'signed_by' => 'Signed By',
    'is_primary' => 'Primary Contact',
    'user' => 'User',
    'user_id' => 'User',
    'parent' => 'Parent',
    'parent_id' => 'Parent',

    /*
    |--------------------------------------------------------------------------
    | Contact/Personal Fields
    |--------------------------------------------------------------------------
    */
    'email' => 'Email',
    'phone' => 'Phone',
    'mobile' => 'Mobile',
    'fax' => 'Fax',
    'address' => 'Address',
    'website' => 'Website',
    'company_name' => 'Company Name',
    'contact_name' => 'Contact Name',
    'position' => 'Position',
    'job_title' => 'Job Title',
    'business_number' => 'Business Number',
    'representative' => 'Representative',
    'industry' => 'Industry',
    'business_type' => 'Business Type',
    'emergency_contact' => 'Emergency Contact',
    'ip_address' => 'IP Address',
    'user_agent' => 'Browser',

    /*
    |--------------------------------------------------------------------------
    | Employee/HR Fields
    |--------------------------------------------------------------------------
    */
    'employee_code' => 'Employee Code',
    'employment_type' => 'Employment Type',
    'check_in' => 'Check In',
    'check_out' => 'Check Out',
    'work_time' => 'Work Time',

    /*
    |--------------------------------------------------------------------------
    | Document Number Fields
    |--------------------------------------------------------------------------
    */
    'invoice_number' => 'Invoice Number',
    'contract_number' => 'Contract Number',
    'expense_number' => 'Expense Number',
    'payment_number' => 'Payment Number',
    'po_number' => 'PO Number',
    'reference_number' => 'Reference Number',
    'reference' => 'Reference',
    'transaction_number' => 'Transaction Number',

    /*
    |--------------------------------------------------------------------------
    | Inventory/Logistics Fields
    |--------------------------------------------------------------------------
    */
    'reserved_quantity' => 'Reserved Quantity',
    'available_quantity' => 'Available Quantity',
    'received_quantity' => 'Received Quantity',
    'before_quantity' => 'Before Quantity',
    'after_quantity' => 'After Quantity',
    'destination_warehouse_id' => 'Destination Warehouse',
    'shipping_address' => 'Shipping Address',

    /*
    |--------------------------------------------------------------------------
    | Payment/Deposit Fields
    |--------------------------------------------------------------------------
    */
    'method' => 'Payment Method',
    'payment_terms' => 'Payment Terms',
    'terms' => 'Terms',
    'depositor_name' => 'Depositor Name',
    'bank_account' => 'Bank Account',
    'is_billable' => 'Billable',

    /*
    |--------------------------------------------------------------------------
    | Sales (CRM) Fields
    |--------------------------------------------------------------------------
    */
    'source' => 'Source',
    'stage' => 'Stage',
    'next_step' => 'Next Step',
    'rejection_reason' => 'Rejection Reason',

    /*
    |--------------------------------------------------------------------------
    | Contract Fields
    |--------------------------------------------------------------------------
    */
    'file_path' => 'Contract File',
    'is_expired' => 'Expired',

    /*
    |--------------------------------------------------------------------------
    | Account/Category Link Fields
    |--------------------------------------------------------------------------
    */
    'sales_account_id' => 'Sales Account',
    'purchase_account_id' => 'Purchase Account',

    /*
    |--------------------------------------------------------------------------
    | Approval Flow Fields
    |--------------------------------------------------------------------------
    */
    'target_type' => 'Target Type',
    'approver_type' => 'Approver Type',
    'approver_id' => 'Approver',
    'action_type' => 'Approval Type',
    'conditions' => 'Conditions',
    'min_amount' => 'Min Amount',
    'max_amount' => 'Max Amount',
    'step_order' => 'Step Order',

    /*
    |--------------------------------------------------------------------------
    | User/Auth Fields
    |--------------------------------------------------------------------------
    */
    'password' => 'Password',
    'current_password' => 'Current Password',
    'new_password' => 'New Password',
    'password_confirmation' => 'Confirm Password',
    'roles' => 'Roles',
    'permissions' => 'Permissions',
    'avatar_url' => 'Profile Image',
    'locale' => 'Language',
    'event' => 'Event',

    /*
    |--------------------------------------------------------------------------
    | Resource-specific Field Labels
    |--------------------------------------------------------------------------
    */
    'account_name' => 'Account Name',
    'category_name' => 'Category Name',
    'department_name' => 'Department Name',
    'type_name' => 'Type Name',
    'parent_department' => 'Parent Department',
    'parent_account' => 'Parent Account',
    'linked_account' => 'Linked Account',
    'department_manager' => 'Department Manager',
    'user_account' => 'User Account',
    'employment_status' => 'Employment Status',
    'used_days' => 'Days Used',
    'default_days' => 'Default Days',
    'is_paid' => 'Paid Leave',
    'matched_payment' => 'Matched Payment',
    'employees_count' => 'Employee Count',
    'application_date' => 'Application Date',
    'is_processed' => 'Processed',
    'received_at' => 'Received At',
    'datetime' => 'Date/Time',
    'content' => 'Content',
    'profile_photo' => 'Profile Photo',

];
