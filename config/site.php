<?php

/*
|--------------------------------------------------------------------------
| Site defaults
|--------------------------------------------------------------------------
|
| Every value here can be overridden at runtime from the admin panel
| (Settings screen). The database wins; this file is the fallback so the
| site never renders empty on a fresh install.
|
*/

return [
    'name' => 'Anton Tofanidze',
    'headline' => 'Full-Stack Engineer',
    'tagline' => 'B2B marketing & enterprise platforms',
    'role' => 'Full-Stack Engineer — B2B Marketing & Enterprise Platforms',

    'intro' => 'I build the systems B2B marketing teams run on: partner and client portals, '
        .'analytics dashboards, and the integrations that keep Salesforce, Marketo and HubSpot '
        .'talking to each other without losing a single lead.',

    'summary' => 'Full-stack engineer with 6 years of commercial experience across PHP (Laravel, Yii 1/2, '
        .'WordPress, vanilla) and Node.js (NestJS, Express, Sanity). I design enterprise portals and '
        .'dashboards end to end — data model, queue-backed integrations, API layer and the Vue/React '
        .'front end on top of it.',

    'years_experience' => 6,
    'projects_delivered' => 40,
    'available_for_work' => true,
    'availability_note' => 'Open to contract and full-time work',

    'email' => 'tofan0797@gmail.com',
    'phone' => null,
    'location' => 'Remote — Europe',
    'timezone_label' => 'CET / UTC+1',

    'github' => null,
    'linkedin' => null,
    'telegram' => null,

    'meta_title' => 'Anton Tofanidze — Full-Stack Engineer for B2B Marketing Platforms',
    'meta_description' => 'Full-stack engineer with 6 years of experience building B2B marketing and '
        .'enterprise platforms: portals, dashboards, and Salesforce / Marketo / HubSpot integrations. '
        .'PHP (Laravel, Yii), Node.js (NestJS), Vue and React.',

    /*
    | Where contact form notifications are delivered. Falls back to `email`.
    */
    'contact_recipient' => null,
];
