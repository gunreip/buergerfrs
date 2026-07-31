<?php

return [
    'people' => [
        'create_person' => [
            'form_person' => [],
            'phone' => 'Phone',
            'sections' => [
                'address' => [
                    'country' => 'Country',
                ],
                'documents' => [
                    'please_enter_the_issuing_authority_for_the_document_this_is_important_for_correc' => 'Please enter the issuing authority for the document. This is important for correctly identifying the person\'s document and for any document-specific validations.',
                ],
            ],
        ],
        'edit_person' => [
            'sections' => [
                'documents' => [
                    'archive_modal' => [
                        'category' => 'category',
                    ],
                ],
            ],
        ],
    ],
];
