<?php

return [

    'page' => [
        'title' => 'Budget',
    ],

    'heading' => [
        'welcome' => 'Welcome back',
        'intro' => 'This is your budgeting page. Do it wisely.',

    ],

    'info-box' => [
        'overbudget' => 'You have spent %s which is %s%% more than your expected monthly budget. You have nothing left to spend :(',
        'underbudget' => 'You have spent %s%% of your expected monthly budget. You still have %s%% to go.',
        'budget-status' => 'Budget Status',
        'transactions' => 'Transactions',
        'looking-good' => 'Looking good',
        'good-progress' => 'Good progress',
        'almost-there' => 'Almost there',
        'ooh' => 'Ohh',
        'budget-usage' => 'Budget Usage',
        'spent' => 'Spent!',
        'you-have-spent' => 'You have spent',
        'out-of' => 'out of',
    ],

    'budget-graph' => [
        'budgeting-chart' => 'Budgeting Chart',
        'budgeting' => 'Budgeting',
    ],

    'links' => [
        'adjust-budget' => 'Adjust Budget',
    ],

    'button'    => [
        'adjust' => 'Adjust',
        'cancel' => 'Cancel',
        'distribute' => 'Distribute',
        'save-changes' => 'Save Changes'
    ],

    'budget-table' => [
        'budgeting-goals' => 'Budgeting Goals',
        'updated' => 'Updated:',
        'set-goal' => 'Set Goal',
        'transactions' => 'Transactions',
        'progress' => 'Progress',

        'other'=> 'Other',
        'spent' => 'Spent',
        'completed' => 'Completed',
        'actions' => 'Actions',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'empty' => 'It\'s empty here!',
    ],

    'budget-form' => [
        'title' => 'What\'s up %s!',
        'intro' => 'Create a budget of how much you want to spend, save and earning goals. Also distribute your budget.',
        'label' => [
            'spend-month' => 'I want to spend (Monthly)',
            'spend-annual' => 'I want to spend (Annually)',
            'save-month' => 'I want to save (Monthly)',
            'earn-month' => 'I plan to earn (Monthly)',
            'per-month' => 'Per Month',
            'per-year' => 'Per Year',
        ],
        'placeholder' => [
            'example-month' => 'e.g. 1000',
            'example-year' => 'e.g. 12000'
        ],
        'types' => [
            'cash' => 'Cash',
            'bank' => 'Bank',
            'card' => 'Card',
            'ewallet' => 'E-Wallet',
            'other' => 'Other',
        ],
        'status' => [
            'active' => 'Active',
            'inactive' => 'Inactive',
        ]
    ],

    'distribute-form' => [
        'title' => 'Let\'s Distribute',
        'intro' => 'Distribute your budget to categories.',
        'error' => 'You have allocated more than budgeted for a month.',
        'none' => 'No categories to distribute.',
    ],

    'messages' => [
        'are-you-sure' => 'Are you sure?',
        'continue' => 'Continue',
        'delete' => 'This expense will be deleted permanently',
        'adjust-success' => 'Budget successfully adjusted.',
    ],

];
