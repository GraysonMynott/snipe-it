<?php

return array(

    'deleted'               => 'Deleted firmware',
    'does_not_exist'        => 'Model does not exist.',
    'no_association'        => 'WARNING! The firmware for this item is invalid or missing!',
    'no_association_fix'    => 'This will break things in weird and horrible ways. Edit this asset now to assign it a new firmware.',
    'assoc_users'	        => 'This firmware is currently associated with one or more assets and cannot be deleted. Please delete the assets, and then try deleting again. ',
    'invalid_category_type' => 'The category must be an asset category.',

    'create' => array(
        'error'   => 'Model was not created, please try again.',
        'success' => 'Model created successfully.',
        'duplicate_set' => 'A firmware with that name already exists.',
    ),

    'update' => array(
        'error'   => 'Model was not updated, please try again',
        'success' => 'Model updated successfully.',
    ),

    'delete' => array(
        'confirm'   => 'Are you sure you wish to delete this asset model?',
        'error'   => 'There was an issue deleting the firmware. Please try again.',
        'success' => 'The firmware was deleted successfully.'
    ),

    'restore' => array(
        'error'   		=> 'Firmware was not restored, please try again',
        'success' 		=> 'Firmware restored successfully.'
    ),

    'bulkedit' => array(
        'error'   		=> 'No fields were changed, so nothing was updated.',
        'success' 		=> 'Firmware successfully updated. |:model_count models successfully updated.',
        'warn'          => 'You are about to update the properties of the following firmware:|You are about to edit the properties of the following :model_count models:',

    ),

    'bulkdelete' => array(
        'error'   		    => 'No firmware was selected, so nothing was deleted.',
        'success' 		    => 'Firmware deleted!|:success_count firmwares deleted!',
        'success_partial' 	=> ':success_count model(s) were deleted, however :fail_count were unable to be deleted because they still have assets associated with them.'
    ),

);
