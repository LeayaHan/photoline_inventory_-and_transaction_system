<style>
    /* Unified table actions: one clean row, never chopped or wrapped. */
    .action-group,
    .row-actions,
    .inventory-actions,
    .actions,
    .audit-actions {
        display: inline-flex !important;
        align-items: center;
        justify-content: flex-start;
        flex-wrap: nowrap !important;
        gap: 7px;
        white-space: nowrap !important;
        vertical-align: middle;
    }

    .action-group form,
    .row-actions form,
    .inventory-actions form,
    .actions form,
    .audit-actions form {
        display: inline-flex !important;
        margin: 0 !important;
        padding: 0 !important;
        flex: 0 0 auto !important;
    }

    .action-btn,
    .row-actions a,
    .inventory-actions a,
    .inventory-actions button,
    .actions .btn,
    .view-button,
    .manager-table-action,
    .replenishment-action,
    .audit-actions a,
    .audit-actions button {
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto !important;
        min-width: 64px;
        height: 34px;
        padding: 0 12px !important;
        box-sizing: border-box;
        border: 1px solid transparent !important;
        border-radius: 8px !important;
        text-decoration: none !important;
        font-size: 11px !important;
        font-weight: 800 !important;
        line-height: 1 !important;
        white-space: nowrap !important;
        cursor: pointer;
        transition: background .15s ease, border-color .15s ease, color .15s ease;
    }

    .row-actions a,
    .inventory-actions a,
    .view-button,
    .manager-table-action,
    .action-view,
    .audit-actions a {
        color: #1769e8 !important;
        background: #f3f7fd !important;
        border-color: #dbe7f8 !important;
    }

    .actions .btn-warning,
    .action-edit {
        color: #1769e8 !important;
        background: #fff !important;
        border-color: #bcd3f7 !important;
    }

    .action-claim {
        color: #20754a !important;
        background: #ebf8f0 !important;
        border-color: #ccebd9 !important;
    }

    .actions .btn-danger,
    .inventory-actions .delete-button,
    .action-void,
    .replenishment-action {
        color: #b52a25 !important;
        background: #fff0ef !important;
        border-color: #f3cfcc !important;
    }

    .replenishment-action {
        color: #1769e8 !important;
        background: #f3f7fd !important;
        border-color: #dbe7f8 !important;
    }

    .action-locked {
        color: #8792a4 !important;
        background: #f5f7fa !important;
        border-color: #e4e8ee !important;
        cursor: default !important;
    }

    .row-actions a:hover,
    .inventory-actions a:hover,
    .view-button:hover,
    .manager-table-action:hover,
    .actions .btn-warning:hover,
    .replenishment-action:hover,
    .action-view:hover,
    .action-edit:hover,
    .audit-actions a:hover {
        background: #eaf2ff !important;
        border-color: #bcd3f7 !important;
    }

    .actions .btn-danger:hover,
    .inventory-actions .delete-button:hover,
    .action-void:hover {
        background: #ffe6e4 !important;
        border-color: #efb9b5 !important;
    }

    /* Give action columns enough room for the complete button group. */
    td:has(.action-group),
    td:has(.row-actions),
    td:has(.inventory-actions),
    td:has(.actions),
    td:has(.audit-actions) {
        min-width: 220px;
        white-space: nowrap !important;
    }

    th:has(+ td .action-group),
    th:has(+ td .row-actions),
    th:has(+ td .inventory-actions),
    th:has(+ td .actions),
    th:has(+ td .audit-actions) {
        min-width: 220px;
    }

    /* The Staff transaction table has four actions when a transaction is pending. */
    .data-table th:last-child,
    .data-table td:last-child {
        min-width: 280px;
    }

    .data-table td:last-child .action-group {
        min-width: 266px;
    }

    /* Audit tables only need a compact action area. */
    .audit-table th:last-child,
    .audit-table td:last-child {
        min-width: 155px;
    }

    @media (max-width: 700px) {
        .action-group,
        .row-actions,
        .inventory-actions,
        .actions,
        .audit-actions {
            gap: 6px;
        }

        .action-btn,
        .row-actions a,
        .inventory-actions a,
        .inventory-actions button,
        .actions .btn,
        .view-button,
        .manager-table-action,
        .replenishment-action,
        .audit-actions a,
        .audit-actions button {
            min-width: 60px;
            height: 32px;
            padding: 0 10px !important;
            font-size: 10px !important;
        }
    }
</style>
