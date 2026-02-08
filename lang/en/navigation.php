<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Navigation Groups
    |--------------------------------------------------------------------------
    */
    'groups' => [
        'dashboard'          => 'Dashboard',
        'crm'                => 'CRM',
        'project'            => 'Projects',
        'hr'                 => 'HR Management',
        'purchasing'         => 'Purchasing',
        'finance'            => 'Finance',
        'inventory'          => 'Inventory',
        'inventory_logistics' => 'Inventory/Logistics',
        'my_settings'        => 'My Settings',
        'system_settings'    => 'System Settings',
    ],

    /*
    |--------------------------------------------------------------------------
    | Navigation Labels
    |--------------------------------------------------------------------------
    */
    'labels' => [
        // Dashboard
        'dashboard'             => 'Dashboard',

        // CRM
        'customer'              => 'Customers',
        'contact'               => 'Contacts',
        'lead'                  => 'Lead Generation',
        'opportunity'           => 'Opportunities',
        'contract'              => 'Contracts',

        // Projects
        'project'               => 'Projects',
        'task'                  => 'Tasks',
        'timesheet'             => 'Timesheets',
        'milestone'             => 'Milestones',

        // HR Management
        'department'            => 'Departments',
        'employee'              => 'Employees',
        'leave'                 => 'Leave Management',
        'attendance'            => 'Attendance',

        // Purchasing
        'supplier'              => 'Suppliers',
        'purchase_order'        => 'Purchase Orders',
        'purchase_order_item'   => 'Purchase Order Items',

        // Finance
        'payment_matching'      => 'Payment Matching',
        'invoice'               => 'Invoices',
        'invoice_item'          => 'Invoice Items',
        'payment'               => 'Payments',
        'bank_deposit'          => 'Bank Deposits',
        'expense'               => 'Expenses',
        'expense_category'      => 'Expense Categories',
        'account'               => 'Chart of Accounts',

        // Inventory
        'product'               => 'Products',
        'product_category'      => 'Product Categories',
        'stock'                 => 'Stock Status',
        'warehouse'             => 'Warehouses',

        // Inventory/Logistics
        'stock_movement'        => 'Stock Movements',

        // My Settings
        'my_profile'            => 'My Profile',
        'notification_settings' => 'Notification Settings',
        'notification_history'  => 'Notification History',

        // System Settings
        'user'                  => 'Users',
        'role'                  => 'Roles',
        'approval_flow'         => 'Approval Flows',
        'leave_type'            => 'Leave Types',
        'trash'                 => 'Trash',
    ],

];
