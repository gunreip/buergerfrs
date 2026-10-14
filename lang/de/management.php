<?php

return [
    'people' => [
        'create_person' => [
            'form_person' => [
                'document_type_options' => [
                    'options' => [
                        'document' => 'Dokument',
                        'health_insurance_proof' => 'Nachweis über den Krankenversicherungsschutz',
                        'id_card_copy' => 'Kopie des Personalausweises',
                        'invoice' => 'Rechnung',
                        'it_document' => 'IT-Dokument',
                        'other' => 'Sonstiges',
                        'passport_copy' => 'Kopie des Reisepasses',
                        'receipt' => 'Quittung',
                        'residence_permit_copy' => 'Kopie der Aufenthaltsgenehmigung',
                        'tax_document' => 'Steuerunterlage',
                        'wikipedia_pdf_dokument' => 'Wikipedia-PDF-Dokument',
                    ],
                ],
                'emergency' => 'Notfall',
            ],
            'phone' => 'Telefon',
            'sections' => [
                'address' => [
                    'country' => 'Land',
                ],
                'documents' => [
                    'document_type_options' => [
                        'options' => [
                            'document' => 'Dokument',
                            'health_insurance_proof' => 'Nachweis über den Krankenversicherungsschutz',
                            'id_card_copy' => 'Kopie des Personalausweises',
                            'invoice' => 'Rechnung',
                            'it_document' => 'IT-Dokument',
                            'other' => 'Sonstiges',
                            'passport_copy' => 'Kopie des Reisepasses',
                            'receipt' => 'Quittung',
                            'residence_permit_copy' => 'Kopie der Aufenthaltsgenehmigung',
                            'tax_document' => 'Steuerunterlage',
                            'wikipedia_pdf_dokument' => 'Wikipedia-PDF-Dokument',
                        ],
                    ],
                    'please_enter_the_issuing_authority_for_the_document_this_is_important_for_correc' => 'Bitte geben Sie die ausstellende Behörde für das Dokument an. Dies ist wichtig für die korrekte Identifizierung des Dokuments der Person sowie für etwaige dokumentspezifische Überprüfungen.',
                ],
                'emergency_contact' => [
                    'emergency_contact_relationship_options' => [
                        'options' => [
                            'caregiver' => 'Pflegekraft',
                            'child' => 'Kind',
                            'friend' => 'Freund',
                            'guardian' => 'Wächter',
                            'other' => 'Sonstige',
                            'parent' => 'Elternteil',
                            'partner' => 'Partner',
                            'sibling' => 'Geschwister',
                            'spouse' => 'Ehepartner',
                        ],
                    ],
                ],
                'person_core' => [
                    'gender_options' => [
                        'options' => [
                            'diverse' => 'Divers',
                            'female' => 'Weiblich',
                            'male' => 'Männlich',
                            'unknown' => 'Unbekannt',
                        ],
                    ],
                    'marital_status_options' => [
                        'options' => [
                            'divorced' => 'Geschieden',
                            'married' => 'Verheiratet',
                            'registered_partnership' => 'Eingetragene Lebenspartnerschaft',
                            'separated' => 'Getrennt',
                            'single' => 'Single',
                            'unknown' => 'Unbekannt',
                            'widowed' => 'verwitwet',
                        ],
                    ],
                    'salutation_options' => [
                        'options' => [
                            'mr' => 'Herr',
                            'mrs' => 'Frau',
                            'mx' => 'Divers',
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
                            'correspondence' => 'Korrespondenz',
                            'documentation' => 'Dokumentation',
                            'identity' => 'Identität',
                            'insurance' => 'Versicherung',
                            'medical' => 'Medizin',
                            'other' => 'Sonstiges',
                            'residence' => 'Wohnort',
                            'tax' => 'Steuern',
                        ],
                    ],
                ],
                'emergency_contact' => [
                    'emergency_contact_relationship_options' => [
                        'options' => [
                            'caregiver' => 'Pflegekraft',
                            'child' => 'Kind',
                            'friend' => 'Freund',
                            'guardian' => 'Wächter',
                            'other' => 'Sonstiges',
                            'parent' => 'Elternteil',
                            'partner' => 'Partner',
                            'sibling' => 'Geschwister',
                            'spouse' => 'Ehepartner',
                        ],
                    ],
                ],
                'person_core' => [
                    'marital_status_options' => [
                        'options' => [
                            'divorced' => 'Geschieden',
                            'married' => 'Verheiratet',
                            'registered_partnership' => 'Eingetragene Lebenspartnerschaft',
                            'separated' => 'Getrennt',
                            'single' => 'Single',
                            'unknown' => 'Unbekannt',
                            'widowed' => 'verwitwet',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
