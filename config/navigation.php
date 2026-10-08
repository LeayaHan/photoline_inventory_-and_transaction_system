<?php

/*
|--------------------------------------------------------------------------
| Main navigation links
|--------------------------------------------------------------------------
|
| One entry per link: [label, route name, active-route pattern].
| The active pattern is matched with request()->routeIs(), so
| 'transactions.*' keeps "Transactions" highlighted on the create,
| show and edit pages as well.
|
*/

return [

    'staff' => [
        ['Dashboard',    'dashboard',                 'dashboard'],
        ['Transactions', 'transactions.index',        'transactions.*'],
        ['Audits',       'audits.index',              'audits.*'],
    ],

    'manager' => [
        ['Dashboard',        'dashboard',                    'dashboard'],
        ['Transactions',     'manager.transactions.index',   'manager.transactions.*'],
        ['Inventory Audits', 'manager.audits.index',         'manager.audits.*'],
        ['Reports',          'reports.index',                'reports.*'],
        ['Users',            'users.index',                  'users.*'],
    ],

];
