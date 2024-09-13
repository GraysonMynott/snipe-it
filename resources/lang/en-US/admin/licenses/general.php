<?php

return array(
    'about_licenses_title'      => 'About Licenses',
    'about_licenses'            => 'Licenses are used to track software.  They have a specified number of seats that can be checked out to individuals',
    'checkin'  					=> 'Assign License Seat',
    'checkout_history'  		=> 'Assignment History',
    'checkout'  				=> 'Unassign License Seat',
    'edit'  					=> 'Edit License',
    'filetype_info'				=> 'Allowed filetypes are png, gif, jpg, jpeg, doc, docx, pdf, txt, zip, and rar.',
    'clone'  					=> 'Clone License',
    'history_for'  				=> 'History for ',
    'in_out'  					=> 'In/Out',
    'info'  					=> 'License Info',
    'license_seats'  			=> 'License Seats',
    'remaining'                 => 'Remaining',
    'seat'  					=> 'Seat',
    'seats'  					=> 'Seats',
    'software_licenses'  		=> 'Software Licenses',
    'user'  					=> 'User',
    'view'  					=> 'View License',
    'delete_disabled'           => 'This license cannot be deleted yet because some seats are still checked out.',
    'bulk'                      =>
        [
            'checkin_all'           => [
                'button'            => 'Unassign All Seats',
                'modal'             => 'This will action unassign one seat. | This action will unassign all :checkedout_seats_count seats for this license.',
                'enabled_tooltip'   => 'Unassign ALL seats for this license from both users and assets',
                'disabled_tooltip'  => 'This is disabled because there are no seats currently assigned',
                'disabled_tooltip_reassignable'  => 'This is disabled because the License is not reassignable',
                'success'           => 'License successfully unassigned! | All licenses were successfully unassigned!',
                'log_msg'           => 'Unassigned via bulk license unassigment in license GUI',
            ],

            'checkout_all'              => [
                'button'                => 'Assign All Seats',
                'modal'                 => 'This action will assign one seat to the first available user. | This action will assign all :available_seats_count seats to the first available users. A user is considered available for this seat if they do not already have this license checked out to them, and the Auto-Assign License property is enabled on their user account.',
                'enabled_tooltip'   => 'Checkout ALL seats (or as many as are available) to ALL users',
                'disabled_tooltip'  => 'This is disabled because there are no seats currently available',
                'success'           => 'License successfully assigned! | :count licenses were successfully checked out!',
                'error_no_seats'    => 'There are no remaining seats left for this license.',
                'warn_not_enough_seats'    => ':count users were assigned this license, but we ran out of available license seats.',
                'warn_no_avail_users'    => 'Nothing to do. There are no users who do not already have this license assigned to them.',
                'log_msg'           => 'Assigned via bulk license assignment in license GUI',


            ],
    ],

    'below_threshold' => 'There are only :remaining_count seats left for this license with a minimum quantity of :min_amt. You may want to consider purchasing more seats.',
    'below_threshold_short' => 'This item is below the minimum required quantity.',
);
