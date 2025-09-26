<?php

use Illuminate\Support\Facades\Session;

function flashResponse($message, $color = 'success')
{
    // Map old color names to new notification types
    $typeMap = [
        'primary' => 'info',
        'green' => 'success',
        'red' => 'error',
        'yellow' => 'warning',
        'blue' => 'info',
        'danger' => 'error',
        'warning' => 'warning',
        'success' => 'success',
        'info' => 'info'
    ];

    $type = $typeMap[$color] ?? 'success';

    Session::flash($type, $message);
}

function getDataTableAction($title, $route, $color = 'secondary')
{
    return '<a href="' . $route . '" class="btn btn-sm text-' . $color . ' font-weight-bold text-xs" data-toggle="tooltip" data-original-title="' . $title . '">' . $title . '</a>';
}

function uploadImage($driver, $image)
{
    return $image->store($driver, 'public');
}

function confirmActionButton($title, $route, $id, $color = 'danger', $icon = 'fa-trash')
{
    return '<a href="#" class="btn btn-sm btn-' . $color . ' confirm-action"
            data-id="' . $id . '"
            data-route="' . $route . '"
            data-title="' . $title . '"
            data-toggle="tooltip"
            data-original-title="' . $title . '">
            <i class="fa ' . $icon . '"></i>
        </a>';
}

function checkPermission($permission)
{
    if (!\Illuminate\Support\Facades\Auth::check()) {
        return false;
    }

    // Convert dots to spaces to match the permission format in database
    $permission = str_replace('.', ' ', $permission);

    return \Illuminate\Support\Facades\Auth::user()->can($permission);
}

function checkPermissionAndRedirect($permission)
{
    if (!checkPermission($permission)) {
        flashResponse('Unauthorized action. You do not have permission to access this resource.', 'danger');
        abort(403, 'Unauthorized action.');
    }
}


function hexToRgb($hex)
{
    // Remove # if present
    $hex = ltrim($hex, '#');

    // Convert hex to RGB
    if (strlen($hex) == 3) {
        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }

    if (strlen($hex) != 6) {
        return '6, 193, 103'; // Default green RGB
    }

    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));

    return "$r, $g, $b";
}

