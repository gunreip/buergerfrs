<?php

// config/buergerfrs-forms.php

return [
  'management' => 
  [
    'people' => 
    [
      'create-person' => 
      [
        'sections' => 
        [
          'person' => 
          [
            'path' => 'resources/views/components/management/people/create-person/sections/⚡person-core.blade.php',
            'fields' => 
            [
              'salutation' => true,
              'nameTitle' => false,
              'gender' => true,
              'firstName' => true,
              'middleName' => false,
              'preferredName' => false,
              'lastName' => true,
              'maritalStatus' => true,
              'birthName' => false,
              'birthCountryId' => true,
              'birthPlaceText' => true,
              'dateOfBirth' => true,
              'isTestData' => false,
            ],
          ],
          'avatar' => 
          [
            'path' => 'resources/views/components/management/people/create-person/sections/⚡avatar.blade.php',
            'fields' => 
            [
              'avatarUpload' => false,
            ],
          ],
          'contact' => 
          [
            'path' => 'resources/views/components/management/people/create-person/sections/⚡contact.blade.php',
            'fields' => 
            [
              'phone' => false,
              'mobile' => false,
              'emailPrivate' => false,
              'emailWork' => false,
            ],
          ],
          'address' => 
          [
            'path' => 'resources/views/components/management/people/create-person/sections/⚡address.blade.php',
            'fields' => 
            [
              'addressCountryId' => true,
              'addressPostalCode' => true,
              'addressCity' => true,
              'addressStreet' => true,
              'addressHouseNumber' => true,
              'addressLine2' => false,
            ],
          ],
          'international' => 
          [
            'path' => 'resources/views/components/management/people/create-person/sections/⚡international.blade.php',
            'fields' => 
            [
              'primaryNationalityCountryId' => true,
              'primaryLanguageId' => true,
            ],
          ],
          'identification' => 
          [
            'path' => 'resources/views/components/management/people/create-person/sections/⚡identification.blade.php',
            'fields' => 
            [
              'nationalIdNumber' => true,
              'nationalIdIssuingAuthority' => false,
              'taxId' => false,
              'socialSecurityNumber' => false,
              'pensionInsuranceNumber' => false,
              'residencePermitNumber' => false,
            ],
          ],
          'health-insurance' => 
          [
            'path' => 'resources/views/components/management/people/create-person/sections/⚡health-insurance.blade.php',
            'fields' => 
            [
              'healthInsuranceProviderId' => false,
              'healthInsuranceNumber' => false,
            ],
          ],
          'documents' => 
          [
            'path' => 'resources/views/components/management/people/create-person/sections/⚡documents.blade.php',
            'fields' => 
            [
              'documentType' => false,
              'documentTitle' => false,
              'documentNumber' => false,
              'documentIssuingAuthority' => false,
              'documentIssuedAt' => false,
              'documentExpiresAt' => false,
              'documentUpload' => false,
            ],
          ],
          'emergency-contact' => 
          [
            'path' => 'resources/views/components/management/people/create-person/sections/⚡emergency-contact.blade.php',
            'fields' => 
            [
              'emergencyContactName' => false,
              'emergencyContactRelationship' => false,
              'emergencyContactPhone' => false,
              'emergencyContactEmail' => false,
            ],
          ],
        ],
        'form-login' => 
        [
          'path' => 'resources/views/components/management/people/create-person/⚡form-login.blade.php',
          'fields' => 
          [
            'email' => 
            [
              'required' => true,
              'status_relevant' => false,
            ],
          ],
        ],
      ],
      'edit-person' => 
      [
        'sections' => 
        [
          'documents' => 
          [
            'add-modal' => 
            [
              'path' => 'resources/views/components/management/people/edit-person/sections/documents/⚡add-modal.blade.php',
              'fields' => 
              [
                'newDocumentType' => true,
                'newDocumentCategory' => false,
                'newDocumentTitle' => true,
                'newDocumentNumber' => false,
                'newDocumentIssuingAuthority' => false,
                'newDocumentIssuedAt' => true,
                'newDocumentExpiresAt' => false,
                'newDocumentUpload' => true,
              ],
            ],
            'archive-modal' => 
            [
              'path' => 'resources/views/components/management/people/edit-person/sections/documents/⚡archive-modal.blade.php',
              'fields' => 
              [
              ],
            ],
          ],
        ],
      ],
      'person-overview' => 
      [
        'filter' => 
        [
          'path' => 'resources/views/components/management/people/person-overview/⚡filter.blade.php',
          'fields' => 
          [
            'birthCountryFilter' => false,
            'clientFilter' => false,
            'search' => false,
            'testDataFilter' => false,
            'userFilter' => false,
          ],
        ],
        'meta' => 
        [
          'path' => 'resources/views/components/management/people/person-overview/⚡meta.blade.php',
          'fields' => 
          [
          ],
        ],
        'table' => 
        [
          'path' => 'resources/views/components/management/people/person-overview/⚡table.blade.php',
          'fields' => 
          [
          ],
        ],
      ],
    ],
  ],
];
