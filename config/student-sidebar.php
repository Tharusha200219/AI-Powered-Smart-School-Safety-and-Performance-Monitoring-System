<?php

// Student sidebar configuration
return [
    [
        'items' => [
            getSideBarElement('home', 'Dashboard', 'student.dashboard'),
        ],
    ],
    [
        'name' => 'Academic',
        'items' => [
            getSideBarElement('trending_up', 'Performance', 'student.performance'),
            getSideBarElement('event_seat', 'Seat Assignment', 'student.seat-assignment'),
        ],
    ],
];
