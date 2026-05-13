<?php

// config/buergerfrs-forms.php

/*
|--------------------------------------------------------------------------
| BuergerFRS Form Metadata
|--------------------------------------------------------------------------
|
| This file contains technical form metadata.
|
| The stable key is the Livewire / validation field name, e.g.:
| - salutation
| - firstName
| - birthCountryId
|
| This config intentionally does not contain labels, tooltip texts or
| translation keys. Translations remain a separate concern.
|
| required:
| - true  => field is required for validation and UI required markers
| - false => field is optional
|
| status_relevant:
| - true  => field is included in form/tab completion indicators
| - false => field is ignored for completion indicators
|
*/

return [
    'people.create' => [
        'fields' => [
            // form-person.person.
            'salutation' => [
                'tab' => 'person',
                'required' => true,
                'status_relevant' => true,
            ],

            'nameTitle' => [
                'tab' => 'person',
                'required' => false,
                'status_relevant' => true,
            ],

            'gender' => [
                'tab' => 'person',
                'required' => true,
                'status_relevant' => true,
            ],

            'firstName' => [
                'tab' => 'person',
                'required' => true,
                'status_relevant' => true,
            ],

            'middleName' => [
                'tab' => 'person',
                'required' => false,
                'status_relevant' => true,
            ],

            'preferredName' => [
                'tab' => 'person',
                'required' => false,
                'status_relevant' => true,
            ],

            'lastName' => [
                'tab' => 'person',
                'required' => true,
                'status_relevant' => true,
            ],

            'maritalStatus' => [
                'tab' => 'person',
                'required' => false,
                'status_relevant' => true,
            ],

            'birthName' => [
                'tab' => 'person',
                'required' => false,
                'status_relevant' => true,
            ],

            'birthCountryId' => [
                'tab' => 'person',
                'required' => true,
                'status_relevant' => true,
            ],

            'birthPlaceText' => [
                'tab' => 'person',
                'required' => true,
                'status_relevant' => true,
            ],

            'dateOfBirth' => [
                'tab' => 'person',
                'required' => true,
                'status_relevant' => true,
            ],

            // form-person.avatar.
            'avatarUpload' => [
                'tab' => 'person',
                'required' => false,
                'status_relevant' => true,
            ],

            // form-person.contact.
            'phone' => [
                'tab' => 'contact',
                'required' => false,
                'status_relevant' => true,
            ],

            'mobile' => [
                'tab' => 'contact',
                'required' => false,
                'status_relevant' => true,
            ],

            'emailPrivate' => [
                'tab' => 'contact',
                'required' => false,
                'status_relevant' => true,
            ],

            'emailWork' => [
                'tab' => 'contact',
                'required' => false,
                'status_relevant' => true,
            ],

            // form-person.address.
            'addressCountryId' => [
                'tab' => 'address',
                'required' => true,
                'status_relevant' => true,
            ],

            'addressPostalCode' => [
                'tab' => 'address',
                'required' => true,
                'status_relevant' => true,
            ],

            'addressCity' => [
                'tab' => 'address',
                'required' => true,
                'status_relevant' => true,
            ],

            'addressStreet' => [
                'tab' => 'address',
                'required' => true,
                'status_relevant' => true,
            ],

            'addressHouseNumber' => [
                'tab' => 'address',
                'required' => true,
                'status_relevant' => true,
            ],

            'addressLine2' => [
                'tab' => 'address',
                'required' => false,
                'status_relevant' => true,
            ],

            // form-person.international.
            'primaryNationalityCountryId' => [
                'tab' => 'international',
                'required' => true,
                'status_relevant' => true,
            ],

            'primaryLanguageId' => [
                'tab' => 'international',
                'required' => false,
                'status_relevant' => true,
            ],

            // form-person.identification.
            'nationalIdNumber' => [
                'tab' => 'identification',
                'required' => true,
                'status_relevant' => true,
            ],

            'nationalIdIssuingAuthority' => [
                'tab' => 'identification',
                'required' => false,
                'status_relevant' => true,
            ],

            'taxId' => [
                'tab' => 'identification',
                'required' => false,
                'status_relevant' => true,
            ],

            'socialSecurityNumber' => [
                'tab' => 'identification',
                'required' => false,
                'status_relevant' => true,
            ],

            'pensionInsuranceNumber' => [
                'tab' => 'identification',
                'required' => false,
                'status_relevant' => true,
            ],

            'residencePermitNumber' => [
                'tab' => 'identification',
                'required' => false,
                'status_relevant' => true,
            ],

            // form-person.health-insurance.
            'healthInsuranceProviderId' => [
                'tab' => 'health-insurance',
                'required' => false,
                'status_relevant' => true,
            ],

            'healthInsuranceNumber' => [
                'tab' => 'health-insurance',
                'required' => false,
                'status_relevant' => true,
            ],

            // form-person.documents.
            'documentType' => [
                'tab' => 'documents',
                'required' => false,
                'status_relevant' => true,
            ],

            'documentTitle' => [
                'tab' => 'documents',
                'required' => false,
                'status_relevant' => true,
            ],

            'documentNumber' => [
                'tab' => 'documents',
                'required' => false,
                'status_relevant' => true,
            ],

            'documentIssuingAuthority' => [
                'tab' => 'documents',
                'required' => false,
                'status_relevant' => true,
            ],

            'documentIssuedAt' => [
                'tab' => 'documents',
                'required' => false,
                'status_relevant' => true,
            ],

            'documentExpiresAt' => [
                'tab' => 'documents',
                'required' => false,
                'status_relevant' => true,
            ],

            'documentUpload' => [
                'tab' => 'documents',
                'required' => false,
                'status_relevant' => true,
            ],

            // form-person.emergency-contact.
            'emergencyContactName' => [
                'tab' => 'emergency-contact',
                'required' => false,
                'status_relevant' => true,
            ],

            'emergencyContactRelationship' => [
                'tab' => 'emergency-contact',
                'required' => false,
                'status_relevant' => true,
            ],

            'emergencyContactPhone' => [
                'tab' => 'emergency-contact',
                'required' => false,
                'status_relevant' => true,
            ],

            'emergencyContactEmail' => [
                'tab' => 'emergency-contact',
                'required' => false,
                'status_relevant' => true,
            ],
        ],
    ],
];
