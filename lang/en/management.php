<?php

return [
    'people' => [
        'create_person' => [
            'form_person' => [
                'document_type_options' => [
                    'options' => [
                        'document' => 'Document',
                        'health_insurance_proof' => 'Health insurance proof',
                        'id_card_copy' => 'ID card copy',
                        'invoice' => 'Invoice',
                        'it_document' => 'IT Document',
                        'other' => 'Other',
                        'passport_copy' => 'Passport copy',
                        'receipt' => 'Receipt',
                        'residence_permit_copy' => 'Residence permit copy',
                        'tax_document' => 'Tax document',
                        'wikipedia_pdf_dokument' => 'Wikipedia PDF-Document',
                    ],
                ],
                'emergency' => 'Emergency',
            ],
            'phone' => 'Phone',
            'sections' => [
                'address' => [
                    'country' => 'Country',
                ],
                'documents' => [
                    'document_type_options' => [
                        'options' => [
                            'document' => 'Document',
                            'health_insurance_proof' => 'Health insurance proof',
                            'id_card_copy' => 'ID card copy',
                            'invoice' => 'Invoice',
                            'it_document' => 'IT-Document',
                            'other' => 'Other',
                            'passport_copy' => 'Passport copy',
                            'receipt' => 'Receipt',
                            'residence_permit_copy' => 'Residence permit copy',
                            'tax_document' => 'Tax document',
                            'wikipedia_pdf_dokument' => 'Wikipedia PDF-Document',
                        ],
                    ],
                    'please_enter_the_issuing_authority_for_the_document_this_is_important_for_correc' => 'Please enter the issuing authority for the document. This is important for correctly identifying the person\'s document and for any document-specific validations.',
                ],
                'emergency_contact' => [
                    'emergency_contact_relationship_options' => [
                        'options' => [
                            'caregiver' => 'Caregiver',
                            'child' => 'Child',
                            'friend' => 'Friend',
                            'guardian' => 'Guardian',
                            'other' => 'Other',
                            'parent' => 'Parent',
                            'partner' => 'Partner',
                            'sibling' => 'Sibling',
                            'spouse' => 'Spouse',
                        ],
                    ],
                ],
                'person_core' => [
                    'gender_options' => [
                        'options' => [
                            'diverse' => 'Diverse',
                            'female' => 'Female',
                            'male' => 'Male',
                            'unknown' => 'Unknown',
                        ],
                    ],
                    'marital_status_options' => [
                        'options' => [
                            'divorced' => 'Divorced',
                            'married' => 'Married',
                            'registered_partnership' => 'Registered partnership',
                            'separated' => 'Separated',
                            'single' => 'Single',
                            'unknown' => 'Unknown',
                            'widowed' => 'Widowed',
                        ],
                    ],
                    'salutation_options' => [
                        'options' => [
                            'mr' => 'Mr.',
                            'mrs' => 'Mrs.',
                            'mx' => 'Mx.',
                        ],
                    ],
                ],
            ],
        ],
        'edit_person' => [
            'sections' => [
                'documents' => [
                    'document_category_options' => [
                        'options' => [
                            'correspondence' => 'Correspondence',
                            'documentation' => 'Documentation',
                            'identity' => 'Identity',
                            'insurance' => 'Insurance',
                            'medical' => 'Medical',
                            'other' => 'Other',
                            'residence' => 'Residence',
                            'tax' => 'Tax',
                        ],
                    ],
                ],
                'emergency_contact' => [
                    'emergency_contact_relationship_options' => [
                        'options' => [
                            'caregiver' => 'Caregiver',
                            'child' => 'Child',
                            'friend' => 'Friend',
                            'guardian' => 'Guardian',
                            'other' => 'Other',
                            'parent' => 'Parent',
                            'partner' => 'Partner',
                            'sibling' => 'Sibling',
                            'spouse' => 'Spouse',
                        ],
                    ],
                ],
                'person_core' => [
                    'marital_status_options' => [
                        'options' => [
                            'divorced' => 'Divorced',
                            'married' => 'Married',
                            'registered_partnership' => 'Registered partnership',
                            'separated' => 'Separated',
                            'single' => 'Single',
                            'unknown' => 'Unknown',
                            'widowed' => 'Widowed',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
