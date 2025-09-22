<?php

function getSideBarElement($icon, $text, $route, $route_params = [], $other_selected_routes = [], $additional_permissions = [])
{
    return [
        'icon' => $icon,
        'text' => $text,
        'route' => $route,
        'route_params' => $route_params,
        'other_selected_routes' => $other_selected_routes,
        'additional_permissions' => $additional_permissions
    ];
}

function formatPermissionString($route)
{
    // Convert route name to permission string
    // Example: admin.students.index -> admin students index
    return str_replace('.', ' ', $route);
}
