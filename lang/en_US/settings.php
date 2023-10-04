<?php

return [

    'heading' => [
        'welcome' => 'Welcome back',
        'intro' => 'This is your settings page. Take control!',
        'users-intro' => 'These are users who own accounts',

    ],

    'side-menu' => [
        'profile' => 'Profile',
        'categories' => 'Categories',
        'system' => 'System',
        'security' => 'Security',
    ],

    'button'    => [
        'save' => 'Save Changes',
        'add-category' => 'Add Category',
        'close' => 'Close',
        'add-user' => 'Add User',
        'create-account' => 'Create Account',
        'continue' => 'Continue'
    ],

    'profile-form' => [
        'title' => 'Profile',
        'intro' => 'Update your personal information',
        'label' => [
            'picture' => 'Profile Picture',
            'first-name' => 'First Name',
            'last-name' => 'Last Name',
            'email' => 'Email Address',
            'phone' => 'Phone Number',
            'address' => 'Address',
            'currency' => 'Currency',
            'timezone' => 'Time Zone',
        ],
        'placeholder' => [
            'first-name' => 'First Name',
            'last-name' => 'Last Name',
            'email' => 'Email Address',
            'phone' => 'Phone Number',
            'address' => 'Address',
        ],
    ],

    'password-form' => [
        'title' => 'Security',
        'intro' => 'Update your account password here',
        'label' => [
            'current-password' => 'Password',
        ],
        'placeholder' => [
            'current-password' => 'Password',
        ],
    ],

    'category-table' => [
        'title' => 'Categories',
        'intro' => 'Manage your income and expense categories',
        'number' => 'No.',
        'category-name' => 'Category',
        'actions' => 'Actions',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'empty' => 'It\'s empty here!',
    ],

    'category-form' => [
        'add-title' => 'Add Category',
        'update-title' => 'Edit Category',
        'label' => [
            'name' => 'Category Name',
        ],
        'placeholder' => [
            'name' => 'Category Name',
        ],
    ],

    'system-form' => [
        'title' => 'System',
        'intro' => 'Manage System settings and preferences',
        'label' => [
            'name' => 'System Name',
            'logo' => 'System Logo',
            'favicon' => 'System favicon/icon',
            'smtp-user' => 'SMTP Login Name',
            'smtp-sender' => 'SMTP Sender/From Address',
            'smtp-host' => 'SMTP Host',
            'smtp-port' => 'SMTP Port',
            'smtp-password' => 'SMTP Password',
            'smtp-encryption' => 'SMTP Encryption',
            'smtp-auth' => 'SMTP Authenticate',
            'allow-signup' => 'Allow new users & business to sign up',
        ],
    ],

    'users-table' => [
        'title' => 'System Users',
        'number' => '#',
        'image' => 'Image',
        'name' => 'name',
        'contact' => 'Contact',
        'edit' => 'Edit',
        'joined-on' => 'Joined On',
        'date-joined' => 'Date Joined',
        'status' => 'Status',
        'actions' => 'Actions',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'empty' => 'It\'s empty here!',  
    ],

    'user-form' => [
        'add-title' => 'Create user account',
        'add-intro' => 'Fill in user\'s details, an email with login details will be sent to user.',
        'update-title' => 'Update User Account',
        'update-intro' => 'Update user account information',
        'label' => [
            'phone' => 'Phone number',
            'address' => 'Address',
            'picture' => 'Profile picture',
            
        ],
        'placeholder' => [
            'phone' => 'Phone number',
            'address' => 'Address',
        ],
        'status' => [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'suspended' => 'Suspended',
        ]
    ],

    'messages' => [
        'profile-edit-success' => 'Profile successfully updated',
        'company-edit-success' => 'Company information successfully updated',
        'password-edit-success' => 'Password successfully updated',
        'password-incorrect' => 'You have entered an incorrect password.',
        'already-exists' => 'already exists',
        'are-you-sure' => 'Are you sure?',
        'proceed' => 'Proceed',
        'delete-category' => 'This category and related records will be deleted.',
        'category-edit-success' => 'Category successfully updated',
        'category-add-success' => 'Category successfully added.',
        'category-delete-success' => 'Category successfully deleted.',
        'category-deleted' => 'Category Deleted',
        'settings-edit-success' => 'System settings successfully updated',
        'delete-user' => 'This user\'s profile and data will be deleted.',
        
        'account-created' => 'Account Created!',
        'account-created-success' => 'Account successfully created.',
        'account-updated-success' => 'Account successfully updated.',
        'account-deleted' => 'Account Deleted!',
        'account-delete-success' => 'Account successfully deleted.',

    ],

    'email-content' => [
        'new-account-title' => 'Welcome to %s!',
        'new-account-subtitle' => 'A new account has been created for you at %s.',
        'new-account-message' => 'These are your login Credentials:<br><br><strong>Email:</strong> %s<br><strong>Password:</strong> %s<br><br>Cheers!<br>%s Team',
        'new-account-button' => 'Login Now',
    ]

];
