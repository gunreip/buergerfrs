<?php

// Reviewed view/provider relationships. Exact paths and owner keys; no suffix matching.
// These links expose default evidence only, never approve runtime values.
$bindings = [];
$people = 'resources/views/components/management/people/';
$owners = [
    'salutationOptions' => 'management.people.create_person.sections.person_core.salutation_options',
    'genderOptions' => 'management.people.create_person.sections.person_core.gender_options',
];
foreach (['create-person' => 'CreatePerson', 'edit-person' => 'EditPerson'] as $view => $class) {
    $className = 'App\\Livewire\\Management\\People\\'.$class;
    $prefix = $view === 'create-person' ? 'management.people.create_person' : 'management.people.edit_person';
    foreach ([...$owners, 'maritalStatusOptions' => $prefix.'.sections.person_core.marital_status_options'] as $property => $owner) {
        $bindings[$people.$view.'/sections/⚡person-core.blade.php'][$owner] = ['class' => $className, 'property' => $property];
    }
    $bindings[$people.$view.'/sections/⚡emergency-contact.blade.php'][$prefix.'.sections.emergency_contact.emergency_contact_relationship_options'] = [
        'class' => $className, 'property' => 'emergencyContactRelationshipOptions',
    ];
}
foreach (['create', 'edit'] as $modal) {
    $path = 'resources/views/components/admin/partials/role-list/⚡modal-'.$modal.'.blade.php';
    foreach (['admin.partials.role_list.modal_edit.role_badge_color_options' => 'colors',
        'admin.partials.role_list.modal_create.role_badge_variant_options' => 'variants'] as $owner => $kind) {
        $bindings[$path][$owner] = ['path' => 'config/buergerfrs-icons.php', 'config_path' => ['categories', 'role_user_management', 'badge', $kind]];
    }
}

return $bindings;
