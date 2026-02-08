<?php

return [

    'invoice_status' => [
        'draft' => 'Draft',
        'issued' => 'Issued',
        'partially_paid' => 'Partially Paid',
        'paid' => 'Paid',
        'overdue' => 'Overdue',
        'cancelled' => 'Cancelled',
    ],

    'expense_status' => [
        'pending' => 'Pending',
        'approval_requested' => 'Approval Requested',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'paid' => 'Paid',
    ],

    'purchase_order_status' => [
        'draft' => 'Draft',
        'pending_approval' => 'Pending Approval',
        'approval_requested' => 'Approval Requested',
        'approved' => 'Approved',
        'ordered' => 'Ordered',
        'partially_received' => 'Partially Received',
        'received' => 'Received',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ],

    'project_status' => [
        'planning' => 'Planning',
        'in_progress' => 'In Progress',
        'on_hold' => 'On Hold',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ],

    'task_status' => [
        'pending' => 'Pending',
        'in_progress' => 'In Progress',
        'in_review' => 'In Review',
        'completed' => 'Completed',
        'on_hold' => 'On Hold',
    ],

    'priority' => [
        'low' => 'Low',
        'normal' => 'Normal',
        'high' => 'High',
        'urgent' => 'Urgent',
    ],

    'milestone_status' => [
        'pending' => 'Pending',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
        'delayed' => 'Delayed',
    ],

    'timesheet_status' => [
        'pending' => 'Pending',
        'approval_requested' => 'Approval Requested',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
    ],

    'leave_status' => [
        'pending' => 'Pending',
        'approval_requested' => 'Approval Requested',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'cancelled' => 'Cancelled',
    ],

    'contract_status' => [
        'drafting' => 'Drafting',
        'in_review' => 'In Review',
        'pending_signature' => 'Pending Signature',
        'active' => 'Active',
        'completed' => 'Completed',
        'terminated' => 'Terminated',
    ],

    'contract_payment_terms' => [
        'lump_sum' => 'Lump Sum',
        'installment' => 'Installment',
        'monthly' => 'Monthly',
        'milestone' => 'Milestone',
    ],

    'lead_status' => [
        'new' => 'New',
        'contacting' => 'Contacting',
        'qualified' => 'Qualified',
        'unqualified' => 'Unqualified',
        'converted' => 'Converted',
    ],

    'lead_source' => [
        'website' => 'Website',
        'referral' => 'Referral',
        'advertisement' => 'Advertisement',
        'exhibition' => 'Exhibition',
        'other' => 'Other',
    ],

    'opportunity_stage' => [
        'discovery' => 'Discovery',
        'contact' => 'Contact',
        'proposal' => 'Proposal',
        'negotiation' => 'Negotiation',
        'closed_won' => 'Closed Won',
        'closed_lost' => 'Closed Lost',
    ],

    'employment_type' => [
        'full_time' => 'Full-time',
        'contract' => 'Contract',
        'intern' => 'Intern',
        'part_time' => 'Part-time',
    ],

    'employee_status' => [
        'active' => 'Active',
        'on_leave' => 'On Leave',
        'resigned' => 'Resigned',
    ],

    'customer_type' => [
        'prospect' => 'Prospect',
        'customer' => 'Customer',
        'vip' => 'VIP',
        'dormant' => 'Dormant',
    ],

    'active_status' => [
        'active' => 'Active',
        'inactive' => 'Inactive',
    ],

    'attendance_status' => [
        'normal' => 'Normal',
        'late' => 'Late',
        'early_leave' => 'Early Leave',
        'absent' => 'Absent',
        'on_leave' => 'On Leave',
        'business_trip' => 'Business Trip',
        'remote' => 'Remote',
    ],

    'account_type' => [
        'asset' => 'Asset',
        'liability' => 'Liability',
        'equity' => 'Equity',
        'revenue' => 'Revenue',
        'expense' => 'Expense',
    ],

    'payment_method' => [
        'cash' => 'Cash',
        'card' => 'Card',
        'bank_transfer' => 'Bank Transfer',
        'check' => 'Check',
        'other' => 'Other',
    ],

    'stock_movement_type' => [
        'incoming' => 'Incoming',
        'outgoing' => 'Outgoing',
        'adjustment' => 'Adjustment',
        'transfer' => 'Transfer',
        'return_stock' => 'Return',
    ],

    'approval_status' => [
        'in_progress' => 'In Progress',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'cancelled' => 'Cancelled',
    ],

    'approval_action_type' => [
        'approval' => 'Approval',
        'agreement' => 'Agreement',
        'reference' => 'Reference',
    ],

    'approval_action' => [
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'acknowledged' => 'Acknowledged',
        'auto_skipped' => 'Auto-skipped',
    ],

    'supplier_payment_terms' => [
        'prepaid' => 'Prepaid',
        'postpaid' => 'Postpaid',
        'settlement' => 'Settlement',
    ],

];
