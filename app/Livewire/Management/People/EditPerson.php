<?php

namespace App\Livewire\Management\People;

use App\Models\Address;
use App\Models\AddressLocality;
use App\Models\AddressPostalCode;
use App\Models\AddressStreet;
use App\Models\Country;
use App\Models\InsuranceProvider;
use App\Models\Language;
use App\Models\Person;
use App\Models\PersonAddress;
use App\Models\PersonContact;
use App\Models\PersonDocument;
use App\Models\PersonDocumentType;
use App\Models\PersonHealthInsurance;
use App\Models\PersonIdentifier;
use App\Models\PersonLanguage;
use App\Models\PersonNationality;
use App\Support\Audit\ManagementActivity;
use App\Support\Documents\PersonDocumentPath;
use App\Support\Forms\FormFieldRegistry;
use Flux\Flux;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Component;
use Livewire\WithFileUploads;

class EditPerson extends Component
{
    use WithFileUploads;

    private const DOCUMENT_ADD_FORM_KEY = 'management.people.edit-person.sections.documents.add-modal';

    private const DEFAULT_DATE_OF_BIRTH_YEARS_AGO = 30;

    public Person $person;

    public string $activeFormTab = 'person';

    public ?string $editingField = null;

    public ?string $editingFieldInitialValue = null;

    public string $salutation = '';

    public string $nameTitle = '';

    public string $gender = '';

    public string $maritalStatus = '';

    public string $firstName = '';

    public string $middleName = '';

    public string $preferredName = '';

    public string $lastName = '';

    public string $birthName = '';

    public ?string $dateOfBirth = null;

    public ?int $birthCountryId = null;

    public string $birthPlaceText = '';

    public string $phone = '';

    public string $mobile = '';

    public string $emailPrivate = '';

    public string $emailWork = '';

    public ?int $addressCountryId = null;

    public string $addressPostalCode = '';

    public string $addressCity = '';

    public string $addressStreet = '';

    public string $addressHouseNumber = '';

    public string $addressLine2 = '';

    public string $identifierNationalIdNumber = '';

    public string $identifierNationalIdIssuingAuthority = '';

    public string $identifierTaxId = '';

    public string $identifierSocialSecurityNumber = '';

    public string $identifierPensionInsuranceNumber = '';

    public string $identifierResidencePermitNumber = '';

    public ?int $healthInsuranceProviderId = null;

    public string $healthInsuranceNumber = '';

    public ?int $documentId = null;

    public bool $documentArchiveModalOpen = false;

    public string $documentArchiveFilter = 'current';

    public string $documentArchiveSortField = 'date';

    public string $documentArchiveSortDirection = 'desc';

    public string $documentType = '';

    public string $documentCategory = '';

    public bool $addDocumentModalOpen = false;

    public string $newDocumentType = '';

    public string $newDocumentCategory = '';

    public string $newDocumentTitle = '';

    public string $newDocumentNumber = '';

    public string $newDocumentIssuingAuthority = '';

    public ?string $newDocumentIssuedAt = null;

    public ?string $newDocumentExpiresAt = null;

    public array|TemporaryUploadedFile|null $newDocumentUpload = [];

    public string $documentTitle = '';

    public string $documentNumber = '';

    public string $documentIssuingAuthority = '';

    public ?string $documentIssuedAt = null;

    public ?string $documentExpiresAt = null;

    public string $emergencyContactName = '';

    public string $emergencyContactRelationship = '';

    public string $emergencyContactPhone = '';

    public string $emergencyContactEmail = '';

    public ?int $emergencyContactPersonId = null;

    public array $primaryNationalityCountryId = [];

    public array $primaryLanguageId = [];

    public array $languageAbilities = [];

    public array $salutationOptions = [
        'mr' => 'Mr.',
        'mrs' => 'Mrs.',
        'mx' => 'Mx.',
    ];

    public array $genderOptions = [
        'female' => 'Female',
        'male' => 'Male',
        'diverse' => 'Diverse',
        'unknown' => 'Unknown',
    ];

    public array $maritalStatusOptions = [
        'single' => 'Single',
        'married' => 'Married',
        'registered_partnership' => 'Registered partnership',
        'divorced' => 'Divorced',
        'widowed' => 'Widowed',
        'separated' => 'Separated',
        'unknown' => 'Unknown',
    ];

    public array $documentTypeOptions = [];

    public array $documentCategoryOptions = [];

    public array $emergencyContactRelationshipOptions = [
        PersonContact::RELATIONSHIP_PARENT => 'Parent',
        PersonContact::RELATIONSHIP_CHILD => 'Child',
        PersonContact::RELATIONSHIP_SPOUSE => 'Spouse',
        PersonContact::RELATIONSHIP_PARTNER => 'Partner',
        PersonContact::RELATIONSHIP_SIBLING => 'Sibling',
        PersonContact::RELATIONSHIP_GUARDIAN => 'Guardian',
        PersonContact::RELATIONSHIP_CAREGIVER => 'Caregiver',
        PersonContact::RELATIONSHIP_FRIEND => 'Friend',
        PersonContact::RELATIONSHIP_OTHER => 'Other',
    ];

    public function mount(Person $person): void
    {
        $this->person = $person;
        $this->refreshDocumentTypeOptions();
        $this->refreshDocumentCategoryOptions();
        $this->fillFromPerson();
    }

    public function updatedBirthCountryId(mixed $value): void
    {
        $this->birthCountryId = filled($value) ? (int) $value : null;
        $this->birthPlaceText = '';
        $this->validateOnly('birthCountryId');
    }

    public function updatedAddressCountryId(): void
    {
        $this->addressPostalCode = '';
        $this->addressCity = '';
        $this->addressStreet = '';
    }

    public function updatedAddressPostalCode(mixed $value): void
    {
        $this->addressPostalCode = trim((string) $value);
        $this->addressCity = '';
        $this->addressStreet = '';
    }

    public function updatedAddressCity(mixed $value): void
    {
        $this->addressCity = trim((string) $value);
        $this->addressStreet = '';
    }

    public function updatedAddressStreet(mixed $value): void
    {
        $this->addressStreet = trim((string) $value);
    }

    public function updatedPrimaryLanguageId(): void
    {
        $this->syncLanguageAbilities();
    }

    public function updatedDocumentId(mixed $value): void
    {
        $documentId = filled($value) ? (int) $value : null;
        $this->selectDocument($documentId);
    }

    public function useCreatedBirthPlaceText(mixed $value): void
    {
        if ($this->birthCountryId === null) {
            return;
        }

        $this->birthPlaceText = trim((string) $value);

        if ($this->getErrorBag()->has('birthPlaceText')) {
            $this->validateOnly('birthPlaceText');
        }
    }

    public function useCreatedAddressPostalCode(mixed $value): void
    {
        $this->updatedAddressPostalCode($value);
    }

    public function useCreatedAddressCity(mixed $value): void
    {
        $this->updatedAddressCity($value);
    }

    public function useCreatedAddressStreet(mixed $value): void
    {
        $this->updatedAddressStreet($value);
    }

    public function useCreatedDocumentIssuingAuthority(mixed $value): void
    {
        $this->documentIssuingAuthority = trim((string) $value);
    }

    public function selectDocument(?int $documentId): void
    {
        $document = $documentId !== null
            ? $this->person->documentRows()->whereKey($documentId)->first()
            : null;

        $this->fillDocumentState($document);
        $this->editingField = null;
        $this->editingFieldInitialValue = null;
    }

    public function openDocumentArchive(): void
    {
        $this->documentArchiveFilter = $this->firstAvailableDocumentArchiveFilter();
        $this->documentArchiveModalOpen = true;
    }

    public function closeDocumentArchive(): void
    {
        $this->documentArchiveModalOpen = false;
    }

    public function sortDocumentArchiveBy(string $field): void
    {
        if (! array_key_exists($field, $this->documentArchiveSortableFields())) {
            return;
        }

        if ($this->documentArchiveSortField === $field) {
            $this->documentArchiveSortDirection = $this->documentArchiveSortDirection === 'asc' ? 'desc' : 'asc';

            return;
        }

        $this->documentArchiveSortField = $field;
        $this->documentArchiveSortDirection = 'asc';
    }

    public function openAddDocumentModal(): void
    {
        $this->resetAddDocumentState();
        $this->addDocumentModalOpen = true;
    }

    public function closeAddDocumentModal(): void
    {
        $this->addDocumentModalOpen = false;
        $this->resetAddDocumentState();
    }

    public function removeNewDocumentUpload(int $index): void
    {
        if (! is_array($this->newDocumentUpload)) {
            $this->newDocumentUpload = [];

            return;
        }

        unset($this->newDocumentUpload[$index]);

        $this->newDocumentUpload = array_values($this->newDocumentUpload);
    }

    public function createNewDocumentType(mixed $value, string $targetField = 'newDocumentType'): void
    {
        $documentType = PersonDocumentType::firstOrCreateFromLabel((string) $value);

        if ($documentType === null) {
            return;
        }

        $this->refreshDocumentTypeOptions();

        if ($targetField === 'documentType') {
            $this->documentType = $documentType->code;
            $this->resetValidation('documentType');

            return;
        }

        $this->newDocumentType = $documentType->code;
        $this->resetValidation('newDocumentType');
    }

    public function createNewDocumentCategory(mixed $value, string $targetField = 'newDocumentCategory'): void
    {
        $label = trim((string) $value);

        if ($label === '') {
            return;
        }

        $category = Str::of(Str::ascii($label, 'de'))
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', '_')
            ->trim('_')
            ->toString();

        if ($category === '') {
            return;
        }

        $this->documentCategoryOptions[$category] = $label;

        if ($targetField === 'documentCategory') {
            $this->documentCategory = $category;
            $this->resetValidation('documentCategory');

            return;
        }

        $this->newDocumentCategory = $category;
        $this->resetValidation('newDocumentCategory');
    }

    public function addDocument(ManagementActivity $managementActivity): void
    {
        if ($this->newDocumentUploads($this->newDocumentUpload)->count() > 1) {
            $this->addError('newDocumentUpload', __('Please upload only one document or image at a time.'));

            Flux::toast(
                heading: __('Too many files'),
                text: __('Please upload only one document or image at a time.'),
                variant: 'warning',
                duration: 3500,
            );

            return;
        }

        $validated = $this->validate($this->addDocumentRules());
        $uploads = $this->newDocumentUploads($validated['newDocumentUpload'] ?? null)
            ->filter(fn(mixed $upload): bool => $upload instanceof TemporaryUploadedFile)
            ->values();

        if ($uploads->count() > 1) {
            $this->addError('newDocumentUpload', __('Please upload only one document or image at a time.'));

            Flux::toast(
                heading: __('Too many files'),
                text: __('Please upload only one document or image at a time.'),
                variant: 'warning',
                duration: 3500,
            );

            return;
        }

        if ($uploads->isEmpty()) {
            Flux::toast(
                heading: __('Document file missing'),
                text: __('Please upload a document or image before saving.'),
                variant: 'warning',
                duration: 3500,
            );

            return;
        }

        $createdDocuments = $uploads
            ->map(function (TemporaryUploadedFile $upload, int $index) use ($validated): PersonDocument {
                $type = $validated['newDocumentType'] ?: PersonDocument::TYPE_OTHER;
                $category = $validated['newDocumentCategory'] ?: $this->documentCategoryForType($type);
                $uploadAttributes = $this->storeDocumentUpload($upload);

                return PersonDocument::query()->create(array_merge([
                    'person_id' => $this->person->id,
                    'type' => $type,
                    'status' => PersonDocument::STATUS_ACTIVE,
                    'category' => $category,
                    'source' => PersonDocument::SOURCE_UPLOAD,
                    'direction' => PersonDocument::DIRECTION_NONE,
                    'title' => $this->titleForAddedDocument(
                        title: $validated['newDocumentTitle'] ?? null,
                        upload: $upload,
                        index: $index,
                    ),
                    'document_number' => $this->normalizeNullableAddressInput($validated['newDocumentNumber'] ?? null),
                    'issuing_authority' => $this->normalizeNullableAddressInput($validated['newDocumentIssuingAuthority'] ?? null),
                    'issued_at' => $validated['newDocumentIssuedAt'] ?: null,
                    'expires_at' => $validated['newDocumentExpiresAt'] ?: null,
                    'valid_from' => $validated['newDocumentIssuedAt'] ?: null,
                    'valid_until' => $validated['newDocumentExpiresAt'] ?: null,
                    'is_current' => true,
                ], $uploadAttributes));
            });

        $firstDocument = $createdDocuments->first();

        if ($firstDocument instanceof PersonDocument) {
            $this->person->refresh();
            $this->fillDocumentState($firstDocument->refresh());
        }

        $managementActivity->personDocumentsAdded(
            person: $this->person,
            documents: $createdDocuments,
            sourceComponent: static::class,
        );

        $this->addDocumentModalOpen = false;
        $this->documentArchiveModalOpen = false;
        $this->resetAddDocumentState();
        $this->activeFormTab = 'documents';

        Flux::toast(
            heading: $createdDocuments->count() === 1 ? __('Document added') : __('Documents added'),
            text: $createdDocuments->count() === 1
                ? __('The document has been added to the person record.')
                : __('The documents have been added to the person record.'),
            variant: 'success',
            duration: 3500,
        );
    }

    public function selectDocumentFromArchive(int $documentId): void
    {
        $document = $this->person->documentRows()->whereKey($documentId)->first();

        if ($document === null) {
            return;
        }

        $this->fillDocumentState($document);
        $this->editingField = null;
        $this->editingFieldInitialValue = null;
        $this->activeFormTab = 'documents';
        $this->documentArchiveModalOpen = false;
    }

    public function dateOfBirthOpenTo(): string
    {
        return now()->subYears(self::DEFAULT_DATE_OF_BIRTH_YEARS_AGO)->toDateString();
    }

    public function dateOfBirthPlaceholder(): string
    {
        return match (app()->getLocale()) {
            'de' => 'tt.mm.jjjj',
            default => 'yyyy-mm-dd',
        };
    }

    public function isRequiredField(string $field): bool
    {
        return app(FormFieldRegistry::class)->isRequired(self::DOCUMENT_ADD_FORM_KEY, $field);
    }

    public function avatarUrl(): ?string
    {
        $avatarPath = trim((string) ($this->person->avatar_path ?? ''));

        if ($avatarPath === '' || ! Storage::disk('public')->exists($avatarPath)) {
            return null;
        }

        return Storage::disk('public')->url($avatarPath);
    }

    public function save(ManagementActivity $managementActivity): void
    {
        if ($this->editingField === null) {
            return;
        }

        try {
            $validated = $this->validate($this->rulesForEditedField());
        } catch (ValidationException $exception) {
            $this->toastValidationFailure($exception);

            throw $exception;
        }

        $attributes = $this->personAttributesFromValidated($validated);

        if ($this->editingField !== null && str_starts_with($this->editingField, 'address')) {
            $this->saveAddress($managementActivity);

            return;
        }

        if (in_array($this->editingField, ['primaryNationalityCountryId', 'primaryLanguageId'], true)) {
            $this->saveInternational($managementActivity);

            return;
        }

        if ($this->editingField !== null && str_starts_with($this->editingField, 'identifier')) {
            $this->saveIdentification($managementActivity);

            return;
        }

        if ($this->editingField !== null && str_starts_with($this->editingField, 'healthInsurance')) {
            $this->saveHealthInsurance($managementActivity);

            return;
        }

        if ($this->editingField !== null && str_starts_with($this->editingField, 'document')) {
            $this->saveDocument($managementActivity);

            return;
        }

        if ($this->editingField !== null && str_starts_with($this->editingField, 'emergencyContact')) {
            $this->saveEmergencyContact($managementActivity);

            return;
        }

        if ($attributes === []) {
            return;
        }

        $before = $this->person->only(array_keys($attributes));

        $this->person->update($attributes);
        $this->person->refresh();

        $managementActivity->personUpdated(
            person: $this->person,
            before: $before,
            after: $this->person->only(array_keys($before)),
            sourceComponent: static::class,
        );

        Flux::toast(
            heading: __('Person updated'),
            text: __('The person record has been updated.'),
            variant: 'success',
            duration: 3500,
        );

        $this->finishEditingField();
    }

    public function isEditingFieldChanged(string $field): bool
    {
        return $this->editingField === $field
            && $this->editingFieldInitialValue !== null
            && $this->editingFieldInitialValue !== $this->fieldComparableValue($field);
    }

    public function editField(string $field): void
    {
        if (! array_key_exists($field, $this->editableFieldTabs())) {
            return;
        }

        if ($this->editingField === $field) {
            $this->closeEditingField();

            return;
        }

        if ($this->editingField !== null) {
            $this->closeEditingField();
        }

        $this->editingField = $field;
        $this->editingFieldInitialValue = $this->fieldComparableValue($field);
        $this->activeFormTab = $this->editableFieldTabs()[$field];

        $this->focusEditedField($field);
    }

    public function render()
    {
        return view('components.management.people.⚡edit-person', [
            'birthCountryOptions' => Country::query()
                ->active()
                ->ordered()
                ->get(['id', 'iso2', 'name', 'native_name']),
            'birthPlaceOptions' => $this->birthPlaceOptions(),
            'addressCountryOptions' => Country::query()
                ->active()
                ->ordered()
                ->get(['id', 'iso2', 'name', 'native_name']),
            'addressPostalCodeOptions' => $this->addressPostalCodeOptions(),
            'addressCityOptions' => $this->addressCityOptions(),
            'addressStreetOptions' => $this->addressStreetOptions(),
            'nationalityCountryOptions' => Country::query()
                ->active()
                ->ordered()
                ->get(['id', 'iso2', 'name', 'native_name']),
            'languageOptions' => Language::query()
                ->active()
                ->ordered()
                ->get(['id', 'iso639_1', 'iso639_3', 'name', 'native_name']),
            'selectedNationalityOptions' => $this->selectedNationalityOptions(),
            'selectedLanguageOptions' => $this->selectedLanguageOptions(),
            'healthInsuranceProviderOptions' => InsuranceProvider::query()
                ->active()
                ->where('type', InsuranceProvider::TYPE_HEALTH)
                ->ordered()
                ->get(['id', 'name', 'short_name', 'code']),
            'documentOptions' => $this->documentOptions(),
            'documentArchiveRows' => $this->documentArchiveRows(),
            'documentArchiveCounts' => $this->documentArchiveCounts(),
            'documentIssuingAuthorityOptions' => $this->documentIssuingAuthorityOptions(),
            'personNumberOptions' => $this->personNumberOptions(),
        ]);
    }

    protected function rules(): array
    {
        return [
            'salutation' => ['required', 'string', Rule::in(array_keys($this->salutationOptions))],
            'nameTitle' => ['nullable', 'string', 'max:255'],
            'gender' => ['required', 'string', Rule::in(array_keys($this->genderOptions))],
            'maritalStatus' => ['required', 'string', Rule::in(array_keys($this->maritalStatusOptions))],
            'firstName' => ['required', 'string', 'max:255'],
            'middleName' => ['nullable', 'string', 'max:255'],
            'preferredName' => ['nullable', 'string', 'max:255'],
            'lastName' => ['required', 'string', 'max:255'],
            'birthName' => ['nullable', 'string', 'max:255'],
            'dateOfBirth' => ['required', 'date'],
            'birthCountryId' => ['required', 'integer', Rule::exists('countries', 'id')],
            'birthPlaceText' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'mobile' => ['nullable', 'string', 'max:255'],
            'emailPrivate' => ['nullable', 'email', 'max:255'],
            'emailWork' => ['nullable', 'email', 'max:255'],
            'addressCountryId' => ['nullable', 'integer', Rule::exists('countries', 'id')],
            'addressPostalCode' => ['nullable', 'string', 'max:255'],
            'addressCity' => ['nullable', 'string', 'max:255'],
            'addressStreet' => ['nullable', 'string', 'max:255'],
            'addressHouseNumber' => ['nullable', 'string', 'max:255'],
            'addressLine2' => ['nullable', 'string', 'max:255'],
            'primaryNationalityCountryId' => ['nullable', 'array'],
            'primaryNationalityCountryId.*' => ['integer', Rule::exists('countries', 'id')],
            'primaryLanguageId' => ['nullable', 'array'],
            'primaryLanguageId.*' => ['integer', Rule::exists('languages', 'id')],
            'languageAbilities' => ['array'],
            'languageAbilities.*.speaking' => ['boolean'],
            'languageAbilities.*.reading' => ['boolean'],
            'languageAbilities.*.writing' => ['boolean'],
            'identifierNationalIdNumber' => ['nullable', 'string', 'max:255'],
            'identifierNationalIdIssuingAuthority' => ['nullable', 'string', 'max:255'],
            'identifierTaxId' => ['nullable', 'string', 'max:255'],
            'identifierSocialSecurityNumber' => ['nullable', 'string', 'max:255'],
            'identifierPensionInsuranceNumber' => ['nullable', 'string', 'max:255'],
            'identifierResidencePermitNumber' => ['nullable', 'string', 'max:255'],
            'healthInsuranceProviderId' => [
                'nullable',
                'integer',
                Rule::exists('insurance_providers', 'id')
                    ->where('type', InsuranceProvider::TYPE_HEALTH)
                    ->where('is_active', true),
            ],
            'healthInsuranceNumber' => ['nullable', 'string', 'max:255'],
            'documentId' => ['nullable', 'integer', Rule::exists('person_documents', 'id')->where('person_id', $this->person->id)],
            'documentType' => ['nullable', 'string', Rule::in($this->documentTypeOptionKeys())],
            'documentCategory' => ['nullable', 'string', Rule::in($this->documentCategoryOptionKeys())],
            'documentTitle' => ['nullable', 'string', 'max:255'],
            'documentNumber' => ['nullable', 'string', 'max:255'],
            'documentIssuingAuthority' => ['nullable', 'string', 'max:255'],
            'documentIssuedAt' => ['nullable', 'date'],
            'documentExpiresAt' => ['nullable', 'date', 'after_or_equal:documentIssuedAt'],
            'emergencyContactName' => ['nullable', 'string', 'max:255'],
            'emergencyContactRelationship' => ['nullable', 'string', Rule::in(array_keys($this->emergencyContactRelationshipOptions))],
            'emergencyContactPhone' => ['nullable', 'string', 'max:255'],
            'emergencyContactEmail' => ['nullable', 'email', 'max:255'],
            'emergencyContactPersonId' => ['nullable', 'integer', Rule::exists('people', 'id')->whereNot('id', $this->person->id)],
        ];
    }

    private function rulesForEditedField(): array
    {
        if ($this->editingField === null) {
            return [];
        }

        $allRules = $this->rules();
        $fieldRules = array_intersect_key($allRules, [$this->editingField => true]);

        if ($this->editingField === 'birthPlaceText') {
            $fieldRules['birthCountryId'] = $allRules['birthCountryId'];
        }

        if ($this->editingField !== null && str_starts_with($this->editingField, 'address')) {
            $fieldRules['addressCountryId'] = $allRules['addressCountryId'];
            $fieldRules['addressPostalCode'] = $allRules['addressPostalCode'];
            $fieldRules['addressCity'] = $allRules['addressCity'];
            $fieldRules['addressStreet'] = $allRules['addressStreet'];
            $fieldRules['addressHouseNumber'] = $allRules['addressHouseNumber'];
            $fieldRules['addressLine2'] = $allRules['addressLine2'];
        }

        if ($this->editingField === 'primaryNationalityCountryId') {
            $fieldRules['primaryNationalityCountryId.*'] = $allRules['primaryNationalityCountryId.*'];
        }

        if ($this->editingField === 'primaryLanguageId') {
            $fieldRules['primaryLanguageId.*'] = $allRules['primaryLanguageId.*'];
            $fieldRules['languageAbilities'] = $allRules['languageAbilities'];
            $fieldRules['languageAbilities.*.speaking'] = $allRules['languageAbilities.*.speaking'];
            $fieldRules['languageAbilities.*.reading'] = $allRules['languageAbilities.*.reading'];
            $fieldRules['languageAbilities.*.writing'] = $allRules['languageAbilities.*.writing'];
        }

        if ($this->editingField !== null && str_starts_with($this->editingField, 'identifier')) {
            $fieldRules['identifierNationalIdNumber'] = $allRules['identifierNationalIdNumber'];
            $fieldRules['identifierNationalIdIssuingAuthority'] = $allRules['identifierNationalIdIssuingAuthority'];
            $fieldRules['identifierTaxId'] = $allRules['identifierTaxId'];
            $fieldRules['identifierSocialSecurityNumber'] = $allRules['identifierSocialSecurityNumber'];
            $fieldRules['identifierPensionInsuranceNumber'] = $allRules['identifierPensionInsuranceNumber'];
            $fieldRules['identifierResidencePermitNumber'] = $allRules['identifierResidencePermitNumber'];
        }

        if ($this->editingField !== null && str_starts_with($this->editingField, 'healthInsurance')) {
            $fieldRules['healthInsuranceProviderId'] = $allRules['healthInsuranceProviderId'];
            $fieldRules['healthInsuranceNumber'] = $allRules['healthInsuranceNumber'];
        }

        if ($this->editingField !== null && str_starts_with($this->editingField, 'document')) {
            $fieldRules['documentId'] = $allRules['documentId'];
            $fieldRules['documentType'] = $allRules['documentType'];
            $fieldRules['documentCategory'] = $allRules['documentCategory'];
            $fieldRules['documentTitle'] = $allRules['documentTitle'];
            $fieldRules['documentNumber'] = $allRules['documentNumber'];
            $fieldRules['documentIssuingAuthority'] = $allRules['documentIssuingAuthority'];
            $fieldRules['documentIssuedAt'] = $allRules['documentIssuedAt'];
            $fieldRules['documentExpiresAt'] = $allRules['documentExpiresAt'];
        }

        if ($this->editingField !== null && str_starts_with($this->editingField, 'emergencyContact')) {
            $fieldRules['emergencyContactName'] = $allRules['emergencyContactName'];
            $fieldRules['emergencyContactRelationship'] = $allRules['emergencyContactRelationship'];
            $fieldRules['emergencyContactPhone'] = $allRules['emergencyContactPhone'];
            $fieldRules['emergencyContactEmail'] = $allRules['emergencyContactEmail'];
            $fieldRules['emergencyContactPersonId'] = $allRules['emergencyContactPersonId'];
        }

        return $fieldRules;
    }

    private function addDocumentRules(): array
    {
        $uploadRules = [$this->addDocumentRuleRequired('newDocumentUpload'), 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,webp'];

        return [
            'newDocumentType' => [$this->addDocumentRuleRequired('newDocumentType'), 'string', Rule::in($this->documentTypeOptionKeys())],
            'newDocumentCategory' => ['nullable', 'string', Rule::in($this->documentCategoryOptionKeys())],
            'newDocumentTitle' => [$this->addDocumentRuleRequired('newDocumentTitle'), 'string', 'max:255'],
            'newDocumentNumber' => ['nullable', 'string', 'max:255'],
            'newDocumentIssuingAuthority' => ['nullable', 'string', 'max:255'],
            'newDocumentIssuedAt' => [$this->addDocumentRuleRequired('newDocumentIssuedAt'), 'date'],
            'newDocumentExpiresAt' => ['nullable', 'date', 'after_or_equal:newDocumentIssuedAt'],
            'newDocumentUpload' => is_array($this->newDocumentUpload)
                ? [$this->addDocumentRuleRequired('newDocumentUpload'), 'array', 'min:1', 'max:1']
                : $uploadRules,
            'newDocumentUpload.*' => ['file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,webp'],
        ];
    }

    private function addDocumentRuleRequired(string $field): string
    {
        return $this->isRequiredField($field) ? 'required' : 'nullable';
    }

    /**
     * @return list<string>
     */
    private function documentTypeOptionKeys(): array
    {
        if ($this->documentTypeOptions === []) {
            $this->refreshDocumentTypeOptions();
        }

        return array_keys($this->documentTypeOptions);
    }

    private function refreshDocumentTypeOptions(): void
    {
        $this->documentTypeOptions = PersonDocumentType::options();
    }

    /**
     * @return list<string>
     */
    private function documentCategoryOptionKeys(): array
    {
        if ($this->documentCategoryOptions === []) {
            $this->refreshDocumentCategoryOptions();
        }

        return array_keys($this->documentCategoryOptions);
    }

    private function refreshDocumentCategoryOptions(): void
    {
        $defaultOptions = collect(PersonDocument::CATEGORIES)
            ->mapWithKeys(fn(string $category): array => [$category => Str::of($category)->replace('_', ' ')->headline()->toString()]);

        $existingOptions = PersonDocument::query()
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->mapWithKeys(fn(string $category): array => [$category => Str::of($category)->replace('_', ' ')->headline()->toString()]);

        $this->documentCategoryOptions = $defaultOptions
            ->merge($existingOptions)
            ->all();
    }

    private function closeEditingField(): void
    {
        if ($this->editingField === null) {
            return;
        }

        if ($this->editingFieldInitialValue === $this->fieldComparableValue($this->editingField)) {
            $this->editingField = null;
            $this->editingFieldInitialValue = null;

            return;
        }

        $this->save(app(ManagementActivity::class));
    }

    private function finishEditingField(): void
    {
        if ($this->editingField === null) {
            return;
        }

        $this->editingField = null;
        $this->editingFieldInitialValue = null;
    }

    private function toastValidationFailure(ValidationException $exception): void
    {
        $firstMessage = collect($exception->errors())
            ->flatten()
            ->first();

        Flux::toast(
            heading: __('Changes could not be saved'),
            text: is_string($firstMessage) && $firstMessage !== ''
                ? $firstMessage
                : __('Please check the highlighted fields.'),
            variant: 'warning',
            duration: 5000,
        );
    }

    private function fieldComparableValue(string $field): string
    {
        $value = match ($field) {
            'primaryLanguageId' => $this->primaryLanguageId === []
                ? []
                : [
                    'languages' => $this->primaryLanguageId,
                    'abilities' => $this->languageAbilities,
                ],
            default => data_get($this, $field),
        };

        return $this->normalizeComparableValue($value);
    }

    private function focusEditedField(string $field): void
    {
        $inputId = $this->editableFieldInputIds()[$field] ?? null;

        if ($inputId === null) {
            return;
        }

        $this->dispatch(
            'buergerfrs:focus-field',
            inputId: $inputId,
            tab: $this->editableFieldTabs()[$field] ?? null,
        );
    }

    private function editableFieldInputIds(): array
    {
        return [
            'salutation' => 'edit-person-salutation',
            'gender' => 'edit-person-gender',
            'nameTitle' => 'edit-person-name-title',
            'firstName' => 'edit-person-first-name',
            'lastName' => 'edit-person-last-name',
            'middleName' => 'edit-person-middle-name',
            'preferredName' => 'edit-person-preferred-name',
            'birthName' => 'edit-person-birth-name',
            'maritalStatus' => 'edit-person-marital-status',
            'birthCountryId' => 'edit-person-birth-country',
            'birthPlaceText' => 'edit-person-birth-place',
            'dateOfBirth' => 'edit-person-date-of-birth',
            'phone' => 'edit-person-phone',
            'mobile' => 'edit-person-mobile',
            'emailPrivate' => 'edit-person-email-private',
            'emailWork' => 'edit-person-email-work',
            'addressCountryId' => 'edit-person-address-country',
            'addressPostalCode' => 'edit-person-address-postal-code',
            'addressCity' => 'edit-person-address-city',
            'addressStreet' => 'edit-person-address-street',
            'addressHouseNumber' => 'edit-person-address-house-number',
            'addressLine2' => 'edit-person-address-line-2',
            'primaryNationalityCountryId' => 'edit-person-primary-nationality',
            'primaryLanguageId' => 'edit-person-primary-language',
            'identifierNationalIdNumber' => 'edit-person-national-id-number',
            'identifierNationalIdIssuingAuthority' => 'edit-person-national-id-issuing-authority',
            'identifierTaxId' => 'edit-person-tax-id',
            'identifierSocialSecurityNumber' => 'edit-person-social-security-number',
            'identifierPensionInsuranceNumber' => 'edit-person-pension-insurance-number',
            'identifierResidencePermitNumber' => 'edit-person-residence-permit-number',
            'healthInsuranceProviderId' => 'edit-person-health-insurance-provider',
            'healthInsuranceNumber' => 'edit-person-health-insurance-number',
            'documentId' => 'edit-person-document-selection',
            'documentType' => 'edit-person-document-type',
            'documentCategory' => 'edit-person-document-category',
            'documentTitle' => 'edit-person-document-title',
            'documentNumber' => 'edit-person-document-number',
            'documentIssuingAuthority' => 'edit-person-document-issuing-authority',
            'documentIssuedAt' => 'edit-person-document-issued-at',
            'documentExpiresAt' => 'edit-person-document-expires-at',
            'emergencyContactName' => 'edit-person-emergency-contact-name',
            'emergencyContactRelationship' => 'edit-person-emergency-contact-relationship',
            'emergencyContactPhone' => 'edit-person-emergency-contact-phone',
            'emergencyContactEmail' => 'edit-person-emergency-contact-email',
            'emergencyContactPersonId' => 'edit-person-emergency-contact-person-number',
        ];
    }

    private function normalizeComparableValue(mixed $value): string
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        if (is_array($value)) {
            $value = $this->normalizeComparableArray($value);

            return $value === [] ? '' : json_encode($value, JSON_THROW_ON_ERROR);
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        return trim((string) $value);
    }

    private function normalizeComparableArray(array $value): array
    {
        ksort($value);

        return array_map(
            fn(mixed $item): mixed => is_array($item) ? $this->normalizeComparableArray($item) : $item,
            $value,
        );
    }

    private function editableFieldTabs(): array
    {
        return [
            'salutation' => 'person',
            'gender' => 'person',
            'nameTitle' => 'person',
            'firstName' => 'person',
            'lastName' => 'person',
            'middleName' => 'person',
            'preferredName' => 'person',
            'birthName' => 'person',
            'maritalStatus' => 'person',
            'birthCountryId' => 'person',
            'birthPlaceText' => 'person',
            'dateOfBirth' => 'person',
            'phone' => 'contact',
            'mobile' => 'contact',
            'emailPrivate' => 'contact',
            'emailWork' => 'contact',
            'addressCountryId' => 'address',
            'addressPostalCode' => 'address',
            'addressCity' => 'address',
            'addressStreet' => 'address',
            'addressHouseNumber' => 'address',
            'addressLine2' => 'address',
            'primaryNationalityCountryId' => 'international',
            'primaryLanguageId' => 'international',
            'identifierNationalIdNumber' => 'identification',
            'identifierNationalIdIssuingAuthority' => 'identification',
            'identifierTaxId' => 'identification',
            'identifierSocialSecurityNumber' => 'identification',
            'identifierPensionInsuranceNumber' => 'identification',
            'identifierResidencePermitNumber' => 'identification',
            'healthInsuranceProviderId' => 'health-insurance',
            'healthInsuranceNumber' => 'health-insurance',
            'documentId' => 'documents',
            'documentType' => 'documents',
            'documentCategory' => 'documents',
            'documentTitle' => 'documents',
            'documentNumber' => 'documents',
            'documentIssuingAuthority' => 'documents',
            'documentIssuedAt' => 'documents',
            'documentExpiresAt' => 'documents',
            'emergencyContactName' => 'emergency-contact',
            'emergencyContactRelationship' => 'emergency-contact',
            'emergencyContactPhone' => 'emergency-contact',
            'emergencyContactEmail' => 'emergency-contact',
            'emergencyContactPersonId' => 'emergency-contact',
        ];
    }

    private function fillFromPerson(): void
    {
        $this->salutation = (string) ($this->person->salutation ?? '');
        $this->nameTitle = (string) ($this->person->name_title ?? '');
        $this->gender = (string) ($this->person->gender ?? '');
        $this->maritalStatus = (string) ($this->person->marital_status ?? '');
        $this->firstName = (string) $this->person->first_name;
        $this->middleName = (string) ($this->person->middle_name ?? '');
        $this->preferredName = (string) ($this->person->preferred_name ?? '');
        $this->lastName = (string) $this->person->last_name;
        $this->birthName = (string) ($this->person->birth_name ?? '');
        $this->dateOfBirth = $this->person->date_of_birth?->toDateString();
        $this->birthCountryId = $this->person->birth_country_id;
        $this->birthPlaceText = (string) ($this->person->birth_place_text ?? '');
        $this->phone = (string) ($this->person->phone ?? '');
        $this->mobile = (string) ($this->person->mobile ?? '');
        $this->emailPrivate = (string) ($this->person->email_private ?? '');
        $this->emailWork = (string) ($this->person->email_work ?? '');
        $this->fillAddressFromPerson();
        $this->fillInternationalFromPerson();
        $this->fillIdentificationFromPerson();
        $this->fillHealthInsuranceFromPerson();
        $this->fillDocumentFromPerson();
        $this->fillEmergencyContactFromPerson();
    }

    private function birthPlaceOptions()
    {
        if ($this->birthCountryId === null) {
            return collect();
        }

        $options = AddressLocality::query()
            ->where('country_id', $this->birthCountryId)
            ->ordered()
            ->limit(100)
            ->pluck('name')
            ->unique()
            ->values();

        $current = trim($this->birthPlaceText);

        if ($current === '' || $options->containsStrict($current)) {
            return $options;
        }

        return $options
            ->prepend($current)
            ->unique()
            ->values();
    }

    private function addressPostalCodeOptions()
    {
        if ($this->addressCountryId === null) {
            return collect();
        }

        $options = AddressPostalCode::query()
            ->where('country_id', $this->addressCountryId)
            ->ordered()
            ->limit(100)
            ->pluck('postal_code');

        return $this->includeCurrentAddressOption($options, $this->addressPostalCode);
    }

    private function addressCityOptions()
    {
        if ($this->addressCountryId === null) {
            return collect();
        }

        $postalCode = $this->addressPostalCodeReference($this->addressPostalCode);

        if ($this->addressPostalCode !== '' && $postalCode === null) {
            return $this->includeCurrentAddressOption(collect(), $this->addressCity);
        }

        $options = AddressLocality::query()
            ->where('country_id', $this->addressCountryId)
            ->when($postalCode !== null, function ($query) use ($postalCode): void {
                $query->where('postal_code_id', $postalCode->id);
            })
            ->ordered()
            ->limit(100)
            ->pluck('name');

        return $this->includeCurrentAddressOption($options, $this->addressCity);
    }

    private function addressStreetOptions()
    {
        if ($this->addressCountryId === null || $this->addressCity === '') {
            return collect();
        }

        $postalCode = $this->addressPostalCodeReference($this->addressPostalCode);
        $locality = $this->addressLocalityReference($this->addressCity, $postalCode);

        if (($this->addressPostalCode !== '' && $postalCode === null) || $locality === null) {
            return $this->includeCurrentAddressOption(collect(), $this->addressStreet);
        }

        $options = AddressStreet::query()
            ->where('country_id', $this->addressCountryId)
            ->when($postalCode !== null, function ($query) use ($postalCode): void {
                $query->where('postal_code_id', $postalCode->id);
            })
            ->when($locality !== null, function ($query) use ($locality): void {
                $query->where('locality_id', $locality->id);
            })
            ->ordered()
            ->limit(100)
            ->pluck('name');

        return $this->includeCurrentAddressOption($options, $this->addressStreet);
    }

    private function includeCurrentAddressOption($options, string $current)
    {
        $current = trim($current);

        if ($current === '' || $options->containsStrict($current)) {
            return $options;
        }

        return $options
            ->prepend($current)
            ->unique()
            ->values();
    }

    private function normalizeSelectedIds(mixed $value): array
    {
        return collect(is_array($value) ? $value : [$value])
            ->filter(fn(mixed $id): bool => filled($id))
            ->map(fn(mixed $id): int => (int) $id)
            ->filter(fn(int $id): bool => $id > 0)
            ->unique()
            ->values()
            ->all();
    }

    private function syncLanguageAbilities(): void
    {
        $selectedLanguageIds = $this->normalizeSelectedIds($this->primaryLanguageId);

        $this->languageAbilities = collect($selectedLanguageIds)
            ->mapWithKeys(function (int $languageId): array {
                $current = $this->languageAbilities[$languageId] ?? [];

                return [
                    $languageId => [
                        'speaking' => (bool) ($current['speaking'] ?? true),
                        'reading' => (bool) ($current['reading'] ?? false),
                        'writing' => (bool) ($current['writing'] ?? false),
                    ],
                ];
            })
            ->all();
    }

    private function selectedNationalityOptions()
    {
        $selectedIds = $this->normalizeSelectedIds($this->primaryNationalityCountryId);

        if ($selectedIds === []) {
            return collect();
        }

        return Country::query()
            ->whereIn('id', $selectedIds)
            ->get(['id', 'iso2', 'name', 'native_name'])
            ->sortBy(fn(Country $country): int => array_search($country->id, $selectedIds, true))
            ->values();
    }

    private function selectedLanguageOptions()
    {
        $selectedIds = $this->normalizeSelectedIds($this->primaryLanguageId);

        if ($selectedIds === []) {
            return collect();
        }

        return Language::query()
            ->whereIn('id', $selectedIds)
            ->get(['id', 'iso639_1', 'iso639_3', 'name', 'native_name'])
            ->sortBy(fn(Language $language): int => array_search($language->id, $selectedIds, true))
            ->values();
    }

    private function addressPostalCodeReference(string $postalCode): ?AddressPostalCode
    {
        if ($this->addressCountryId === null || trim($postalCode) === '') {
            return null;
        }

        return AddressPostalCode::query()
            ->where('country_id', $this->addressCountryId)
            ->where('normalized_postal_code', $this->normalizeAddressReferenceValue($postalCode))
            ->first();
    }

    private function addressLocalityReference(string $city, ?AddressPostalCode $postalCode = null): ?AddressLocality
    {
        if ($this->addressCountryId === null || trim($city) === '') {
            return null;
        }

        return AddressLocality::query()
            ->where('country_id', $this->addressCountryId)
            ->when($postalCode !== null, function ($query) use ($postalCode): void {
                $query->where('postal_code_id', $postalCode->id);
            })
            ->where('normalized_name', $this->normalizeAddressReferenceValue($city))
            ->first();
    }

    private function firstOrCreateAddressPostalCode(?int $countryId, ?string $postalCode): ?AddressPostalCode
    {
        $postalCode = $this->normalizeNullableAddressInput($postalCode);

        if ($countryId === null || $postalCode === null) {
            return null;
        }

        return AddressPostalCode::query()->firstOrCreate([
            'country_id' => $countryId,
            'normalized_postal_code' => $this->normalizeAddressReferenceValue($postalCode),
        ], [
            'postal_code' => $postalCode,
            'is_verified' => false,
            'source' => 'manual',
        ]);
    }

    private function firstOrCreateAddressLocality(?int $countryId, ?AddressPostalCode $postalCode, ?string $city): ?AddressLocality
    {
        $city = $this->normalizeNullableAddressInput($city);

        if ($countryId === null || $city === null) {
            return null;
        }

        return AddressLocality::query()->firstOrCreate([
            'country_id' => $countryId,
            'postal_code_id' => $postalCode?->id,
            'normalized_name' => $this->normalizeAddressReferenceValue($city),
        ], [
            'name' => $city,
            'is_verified' => false,
            'source' => 'manual',
        ]);
    }

    private function firstOrCreateAddressStreet(
        ?int $countryId,
        ?AddressPostalCode $postalCode,
        ?AddressLocality $locality,
        ?string $street
    ): ?AddressStreet {
        $street = $this->normalizeNullableAddressInput($street);

        if ($countryId === null || $street === null) {
            return null;
        }

        return AddressStreet::query()->firstOrCreate([
            'country_id' => $countryId,
            'postal_code_id' => $postalCode?->id,
            'locality_id' => $locality?->id,
            'normalized_name' => $this->normalizeAddressReferenceValue($street),
        ], [
            'name' => $street,
            'is_verified' => false,
            'source' => 'manual',
        ]);
    }

    private function normalizeNullableAddressInput(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function normalizeAddressReferenceValue(string $value): string
    {
        return Str::lower((string) preg_replace('/\s+/u', ' ', trim($value)));
    }

    private function fillAddressFromPerson(): void
    {
        $address = $this->primaryAddress();

        $this->addressCountryId = $address?->country_id;
        $this->addressPostalCode = (string) ($address?->postal_code ?? '');
        $this->addressCity = (string) ($address?->city ?? '');
        $this->addressStreet = (string) ($address?->street ?? '');
        $this->addressHouseNumber = (string) ($address?->house_number ?? '');
        $this->addressLine2 = (string) ($address?->address_line_2 ?? '');
    }

    private function primaryAddress(): ?Address
    {
        return $this->person
            ->addressRows()
            ->with('address')
            ->where('type', PersonAddress::TYPE_HOME)
            ->orderByDesc('is_primary')
            ->latest('id')
            ->first()
            ?->address;
    }

    private function saveAddress(ManagementActivity $managementActivity): void
    {
        $countryId = $this->addressCountryId;
        $postalCodeValue = $this->normalizeNullableAddressInput($this->addressPostalCode);
        $cityValue = $this->normalizeNullableAddressInput($this->addressCity);
        $streetValue = $this->normalizeNullableAddressInput($this->addressStreet);
        $houseNumber = $this->normalizeNullableAddressInput($this->addressHouseNumber);
        $addressLine2 = $this->normalizeNullableAddressInput($this->addressLine2);
        $currentAddress = $this->primaryAddress();
        $before = $currentAddress?->only([
            'country_id',
            'postal_code',
            'city',
            'street',
            'house_number',
            'address_line_2',
        ]) ?? [];

        $postalCode = $this->firstOrCreateAddressPostalCode($countryId, $postalCodeValue);
        $locality = $this->firstOrCreateAddressLocality($countryId, $postalCode, $cityValue);
        $street = $this->firstOrCreateAddressStreet($countryId, $postalCode, $locality, $streetValue);

        $addressAttributes = [
            'country_id' => $countryId,
            'postal_code_id' => $postalCode?->id,
            'locality_id' => $locality?->id,
            'street_id' => $street?->id,
            'postal_code' => $postalCodeValue,
            'city' => $cityValue,
            'street' => $streetValue,
            'house_number' => $houseNumber,
            'address_line_2' => $addressLine2,
        ];

        $address = Address::query()->firstOrCreate($addressAttributes);

        PersonAddress::query()->updateOrCreate([
            'person_id' => $this->person->id,
            'type' => PersonAddress::TYPE_HOME,
        ], [
            'address_id' => $address->id,
            'is_primary' => true,
        ]);

        $this->person->refresh();
        $this->fillAddressFromPerson();

        $managementActivity->personUpdated(
            person: $this->person,
            before: ['address' => $before],
            after: ['address' => $address->only(array_keys($addressAttributes))],
            sourceComponent: static::class,
        );

        Flux::toast(
            heading: __('Person updated'),
            text: __('The person record has been updated.'),
            variant: 'success',
            duration: 3500,
        );

        $this->finishEditingField();
    }

    private function fillInternationalFromPerson(): void
    {
        $this->primaryNationalityCountryId = $this->person
            ->nationalityRows()
            ->orderByDesc('is_primary')
            ->oldest('id')
            ->pluck('country_id')
            ->map(fn(mixed $id): int => (int) $id)
            ->values()
            ->all();

        $languageRows = $this->person
            ->languageRows()
            ->orderByDesc('is_primary')
            ->oldest('id')
            ->get(['language_id', 'preferred_for_communication', 'can_speak', 'can_read', 'can_write']);

        $this->primaryLanguageId = $languageRows
            ->pluck('language_id')
            ->map(fn(mixed $id): int => (int) $id)
            ->values()
            ->all();

        $this->languageAbilities = $languageRows
            ->mapWithKeys(fn(PersonLanguage $row): array => [
                $row->language_id => [
                    'speaking' => (bool) $row->can_speak,
                    'reading' => (bool) $row->can_read,
                    'writing' => (bool) $row->can_write,
                ],
            ])
            ->all();

        $this->syncLanguageAbilities();
    }

    private function saveInternational(ManagementActivity $managementActivity): void
    {
        $before = [
            'nationalities' => $this->person
                ->nationalityRows()
                ->orderByDesc('is_primary')
                ->oldest('id')
                ->get(['country_id', 'is_primary'])
                ->map(fn(PersonNationality $row): array => [
                    'country_id' => $row->country_id,
                    'is_primary' => $row->is_primary,
                ])
                ->values()
                ->all(),
            'languages' => $this->person
                ->languageRows()
                ->orderByDesc('is_primary')
                ->oldest('id')
                ->get(['language_id', 'is_primary', 'preferred_for_communication', 'can_speak', 'can_read', 'can_write'])
                ->map(fn(PersonLanguage $row): array => [
                    'language_id' => $row->language_id,
                    'is_primary' => $row->is_primary,
                    'preferred_for_communication' => $row->preferred_for_communication,
                    'can_speak' => $row->can_speak,
                    'can_read' => $row->can_read,
                    'can_write' => $row->can_write,
                ])
                ->values()
                ->all(),
        ];

        $nationalityIds = $this->normalizeSelectedIds($this->primaryNationalityCountryId);
        $languageIds = $this->normalizeSelectedIds($this->primaryLanguageId);

        $this->person->nationalityRows()->delete();
        foreach ($nationalityIds as $index => $countryId) {
            PersonNationality::query()->create([
                'person_id' => $this->person->id,
                'country_id' => $countryId,
                'is_primary' => $index === 0,
            ]);
        }

        $this->person->languageRows()->delete();
        foreach ($languageIds as $index => $languageId) {
            $abilities = $this->languageAbilities[$languageId] ?? [];

            PersonLanguage::query()->create([
                'person_id' => $this->person->id,
                'language_id' => $languageId,
                'proficiency' => PersonLanguage::PROFICIENCY_UNKNOWN,
                'is_native' => false,
                'is_primary' => $index === 0,
                'preferred_for_communication' => (bool) ($abilities['speaking'] ?? false),
                'can_speak' => (bool) ($abilities['speaking'] ?? false),
                'can_read' => (bool) ($abilities['reading'] ?? false),
                'can_write' => (bool) ($abilities['writing'] ?? false),
            ]);
        }

        $this->person->refresh();
        $this->fillInternationalFromPerson();

        $after = [
            'nationalities' => $this->person
                ->nationalityRows()
                ->orderByDesc('is_primary')
                ->oldest('id')
                ->get(['country_id', 'is_primary'])
                ->map(fn(PersonNationality $row): array => [
                    'country_id' => $row->country_id,
                    'is_primary' => $row->is_primary,
                ])
                ->values()
                ->all(),
            'languages' => $this->person
                ->languageRows()
                ->orderByDesc('is_primary')
                ->oldest('id')
                ->get(['language_id', 'is_primary', 'preferred_for_communication', 'can_speak', 'can_read', 'can_write'])
                ->map(fn(PersonLanguage $row): array => [
                    'language_id' => $row->language_id,
                    'is_primary' => $row->is_primary,
                    'preferred_for_communication' => $row->preferred_for_communication,
                    'can_speak' => $row->can_speak,
                    'can_read' => $row->can_read,
                    'can_write' => $row->can_write,
                ])
                ->values()
                ->all(),
        ];

        $managementActivity->personUpdated(
            person: $this->person,
            before: $before,
            after: $after,
            sourceComponent: static::class,
        );

        Flux::toast(
            heading: __('Person updated'),
            text: __('The person record has been updated.'),
            variant: 'success',
            duration: 3500,
        );

        $this->finishEditingField();
    }

    private function fillIdentificationFromPerson(): void
    {
        $nationalId = $this->identifierForType(PersonIdentifier::TYPE_NATIONAL_ID);

        $this->identifierNationalIdNumber = (string) ($nationalId?->value ?? '');
        $this->identifierNationalIdIssuingAuthority = (string) ($nationalId?->issuing_authority ?? '');
        $this->identifierTaxId = (string) ($this->identifierForType(PersonIdentifier::TYPE_TAX_ID)?->value ?? '');
        $this->identifierSocialSecurityNumber = (string) ($this->identifierForType(PersonIdentifier::TYPE_SOCIAL_SECURITY_NUMBER)?->value ?? '');
        $this->identifierPensionInsuranceNumber = (string) ($this->identifierForType(PersonIdentifier::TYPE_PENSION_INSURANCE_NUMBER)?->value ?? '');
        $this->identifierResidencePermitNumber = (string) ($this->identifierForType(PersonIdentifier::TYPE_RESIDENCE_PERMIT_NUMBER)?->value ?? '');
    }

    private function saveIdentification(ManagementActivity $managementActivity): void
    {
        $before = $this->identifierSnapshot();

        $this->saveIdentifier(
            type: PersonIdentifier::TYPE_NATIONAL_ID,
            value: $this->identifierNationalIdNumber,
            issuingAuthority: $this->identifierNationalIdIssuingAuthority,
            isPrimary: true,
        );
        $this->saveIdentifier(
            type: PersonIdentifier::TYPE_TAX_ID,
            value: $this->identifierTaxId,
        );
        $this->saveIdentifier(
            type: PersonIdentifier::TYPE_SOCIAL_SECURITY_NUMBER,
            value: $this->identifierSocialSecurityNumber,
        );
        $this->saveIdentifier(
            type: PersonIdentifier::TYPE_PENSION_INSURANCE_NUMBER,
            value: $this->identifierPensionInsuranceNumber,
        );
        $this->saveIdentifier(
            type: PersonIdentifier::TYPE_RESIDENCE_PERMIT_NUMBER,
            value: $this->identifierResidencePermitNumber,
        );

        $this->person->refresh();
        $this->fillIdentificationFromPerson();

        $managementActivity->personUpdated(
            person: $this->person,
            before: ['identifiers' => $before],
            after: ['identifiers' => $this->identifierSnapshot()],
            sourceComponent: static::class,
        );

        Flux::toast(
            heading: __('Person updated'),
            text: __('The person record has been updated.'),
            variant: 'success',
            duration: 3500,
        );

        $this->finishEditingField();
    }

    private function saveIdentifier(
        string $type,
        ?string $value,
        ?string $issuingAuthority = null,
        bool $isPrimary = false,
    ): void {
        $normalizedValue = $this->normalizeIdentifierValue($value);
        $identifier = $this->identifierForType($type);

        if ($normalizedValue === null) {
            $identifier?->delete();

            return;
        }

        $attributes = [
            'type' => $type,
            'value' => $normalizedValue,
            'value_hash' => hash('sha256', $normalizedValue),
            'issuing_authority' => filled($issuingAuthority) ? trim((string) $issuingAuthority) : null,
            'is_primary' => $isPrimary,
        ];

        if ($identifier === null) {
            PersonIdentifier::query()->create(array_merge($attributes, [
                'person_id' => $this->person->id,
            ]));

            return;
        }

        $identifier->update($attributes);
    }

    private function identifierForType(string $type): ?PersonIdentifier
    {
        return $this->person
            ->identifierRows()
            ->where('type', $type)
            ->orderByDesc('is_primary')
            ->latest('id')
            ->first();
    }

    private function identifierSnapshot(): array
    {
        return $this->person
            ->identifierRows()
            ->whereIn('type', [
                PersonIdentifier::TYPE_NATIONAL_ID,
                PersonIdentifier::TYPE_TAX_ID,
                PersonIdentifier::TYPE_SOCIAL_SECURITY_NUMBER,
                PersonIdentifier::TYPE_PENSION_INSURANCE_NUMBER,
                PersonIdentifier::TYPE_RESIDENCE_PERMIT_NUMBER,
            ])
            ->orderBy('type')
            ->get(['type', 'value', 'issuing_authority', 'is_primary'])
            ->map(fn(PersonIdentifier $identifier): array => [
                'type' => $identifier->type,
                'value' => $identifier->value,
                'issuing_authority' => $identifier->issuing_authority,
                'is_primary' => $identifier->is_primary,
            ])
            ->values()
            ->all();
    }

    private function normalizeIdentifierValue(?string $value): ?string
    {
        if (! filled($value)) {
            return null;
        }

        return trim((string) $value);
    }

    private function fillHealthInsuranceFromPerson(): void
    {
        $healthInsurance = $this->primaryHealthInsurance();

        $this->healthInsuranceProviderId = $healthInsurance?->insurance_provider_id;
        $this->healthInsuranceNumber = (string) ($healthInsurance?->insurance_number ?? '');
    }

    private function saveHealthInsurance(ManagementActivity $managementActivity): void
    {
        $before = $this->primaryHealthInsurance()?->only([
            'insurance_provider_id',
            'insurance_number',
            'is_primary',
        ]) ?? [];

        $providerId = $this->healthInsuranceProviderId;
        $insuranceNumber = $this->normalizeIdentifierValue($this->healthInsuranceNumber);
        $healthInsurance = $this->primaryHealthInsurance();

        if ($providerId === null && $insuranceNumber === null) {
            $healthInsurance?->delete();
            $this->saveIdentifier(
                type: PersonIdentifier::TYPE_HEALTH_INSURANCE_NUMBER,
                value: null,
            );
        } elseif ($healthInsurance === null) {
            PersonHealthInsurance::query()->create([
                'person_id' => $this->person->id,
                'insurance_provider_id' => $providerId,
                'insurance_number' => $insuranceNumber,
                'is_primary' => true,
            ]);
        } else {
            $healthInsurance->update([
                'insurance_provider_id' => $providerId,
                'insurance_number' => $insuranceNumber,
                'is_primary' => true,
            ]);
        }

        if ($providerId !== null || $insuranceNumber !== null) {
            $this->saveIdentifier(
                type: PersonIdentifier::TYPE_HEALTH_INSURANCE_NUMBER,
                value: $insuranceNumber,
            );
        }

        $this->person->refresh();
        $this->fillHealthInsuranceFromPerson();

        $managementActivity->personUpdated(
            person: $this->person,
            before: ['health_insurance' => $before],
            after: ['health_insurance' => $this->primaryHealthInsurance()?->only([
                'insurance_provider_id',
                'insurance_number',
                'is_primary',
            ]) ?? []],
            sourceComponent: static::class,
        );

        Flux::toast(
            heading: __('Person updated'),
            text: __('The person record has been updated.'),
            variant: 'success',
            duration: 3500,
        );

        $this->finishEditingField();
    }

    private function primaryHealthInsurance(): ?PersonHealthInsurance
    {
        return $this->person
            ->healthInsuranceRows()
            ->with('insuranceProvider')
            ->orderByDesc('is_primary')
            ->latest('id')
            ->first();
    }

    private function fillDocumentFromPerson(): void
    {
        $this->fillDocumentState($this->currentDocument());
    }

    private function fillDocumentState(?PersonDocument $document): void
    {
        $this->documentId = $document?->id;
        $this->documentType = (string) ($document?->type ?? '');
        $this->documentCategory = (string) ($document?->category ?? '');
        $this->documentTitle = (string) ($document?->title ?? '');
        $this->documentNumber = (string) ($document?->document_number ?? '');
        $this->documentIssuingAuthority = (string) ($document?->issuing_authority ?? '');
        $this->documentIssuedAt = $document?->issued_at?->toDateString();
        $this->documentExpiresAt = $document?->expires_at?->toDateString();
    }

    private function currentDocument(): ?PersonDocument
    {
        if ($this->documentId !== null) {
            $document = $this->person->documentRows()->whereKey($this->documentId)->first();

            if ($document !== null) {
                return $document;
            }
        }

        return $this->person
            ->documentRows()
            ->latest('id')
            ->first();
    }

    private function documentOptions()
    {
        return $this->person
            ->documentRows()
            ->latest('id')
            ->get(['id', 'type', 'title', 'document_number', 'original_filename', 'mime_type', 'created_at']);
    }

    private function documentArchiveRows()
    {
        $query = $this->documentArchiveQuery($this->documentArchiveFilter);

        $this->applyDocumentArchiveSorting($query);

        return $query->get([
            'id',
            'person_correspondence_id',
            'type',
            'status',
            'category',
            'source',
            'direction',
            'title',
            'document_number',
            'original_filename',
            'document_date',
            'issued_at',
            'expires_at',
            'valid_until',
            'is_current',
            'created_at',
        ]);
    }

    /**
     * @return array<string, int>
     */
    private function documentArchiveCounts(): array
    {
        return collect(['current', 'all', 'expired', 'replaced', 'correspondence', 'archived'])
            ->mapWithKeys(fn(string $filter): array => [$filter => $this->documentArchiveQuery($filter)->count()])
            ->all();
    }

    private function firstAvailableDocumentArchiveFilter(): string
    {
        foreach ($this->documentArchiveCounts() as $filter => $count) {
            if ($count > 0) {
                return $filter;
            }
        }

        return 'current';
    }

    /**
     * @return array<string, string>
     */
    private function documentArchiveSortableFields(): array
    {
        return [
            'category' => 'category',
            'title' => "COALESCE(NULLIF(title, ''), NULLIF(original_filename, ''), type)",
            'type' => 'type',
            'number' => 'document_number',
            'date' => 'COALESCE(document_date, issued_at, created_at)',
            'valid_until' => 'COALESCE(valid_until, expires_at)',
            'source' => 'source',
        ];
    }

    private function applyDocumentArchiveSorting(HasMany $query): void
    {
        $expression = $this->documentArchiveSortableFields()[$this->documentArchiveSortField] ?? null;
        $direction = $this->documentArchiveSortDirection === 'asc' ? 'asc' : 'desc';

        if ($expression === null) {
            $query
                ->latest('created_at')
                ->latest('id');

            return;
        }

        if (str_contains($expression, '(')) {
            $query->orderByRaw("{$expression} {$direction} NULLS LAST");
        } else {
            $query->orderBy($expression, $direction);
        }

        $query->orderBy('id', $direction);
    }

    private function documentArchiveQuery(string $filter): HasMany
    {
        return $this->person
            ->documentRows()
            ->when($filter === 'current', fn($query) => $query
                ->where('is_current', true)
                ->where('status', '!=', PersonDocument::STATUS_ARCHIVED))
            ->when($filter === 'expired', fn($query) => $query
                ->where(function ($query): void {
                    $query
                        ->where('status', PersonDocument::STATUS_EXPIRED)
                        ->orWhereDate('valid_until', '<', now()->toDateString())
                        ->orWhereDate('expires_at', '<', now()->toDateString());
                }))
            ->when($filter === 'replaced', fn($query) => $query
                ->where('status', PersonDocument::STATUS_REPLACED))
            ->when($filter === 'correspondence', fn($query) => $query
                ->where(function ($query): void {
                    $query
                        ->where('category', PersonDocument::CATEGORY_CORRESPONDENCE)
                        ->orWhereNotNull('person_correspondence_id');
                }))
            ->when($filter === 'archived', fn($query) => $query
                ->where('status', PersonDocument::STATUS_ARCHIVED));
    }

    private function resetAddDocumentState(): void
    {
        $this->newDocumentType = '';
        $this->newDocumentCategory = '';
        $this->newDocumentTitle = '';
        $this->newDocumentNumber = '';
        $this->newDocumentIssuingAuthority = '';
        $this->newDocumentIssuedAt = null;
        $this->newDocumentExpiresAt = null;
        $this->newDocumentUpload = [];
        $this->resetValidation([
            'newDocumentType',
            'newDocumentCategory',
            'newDocumentTitle',
            'newDocumentNumber',
            'newDocumentIssuingAuthority',
            'newDocumentIssuedAt',
            'newDocumentExpiresAt',
            'newDocumentUpload',
            'newDocumentUpload.*',
        ]);
    }

    private function newDocumentUploads(mixed $uploads): \Illuminate\Support\Collection
    {
        if ($uploads instanceof TemporaryUploadedFile) {
            return collect([$uploads]);
        }

        if (is_array($uploads)) {
            return collect($uploads);
        }

        return collect();
    }

    /**
     * @return array{
     *     file_disk?: string,
     *     file_path?: string,
     *     original_filename?: string,
     *     mime_type?: string|null,
     *     file_size?: int
     * }
     */
    private function storeDocumentUpload(mixed $upload): array
    {
        if (! $upload instanceof TemporaryUploadedFile) {
            return [];
        }

        $extension = $upload->getClientOriginalExtension();
        $originalFilename = $upload->getClientOriginalName();
        $mimeType = $upload->getMimeType();
        $fileSize = $upload->getSize();
        $uuid = (string) Str::uuid();

        if ($extension === '') {
            $extension = $upload->extension();
        }

        PersonDocumentPath::ensureLocalDiskDirectoryExists($uuid);

        $relativePath = PersonDocumentPath::relativePath($uuid, $extension);
        $path = $upload->storeAs(dirname($relativePath), basename($relativePath), 'local');

        return [
            'file_disk' => 'local',
            'file_path' => $path,
            'original_filename' => $originalFilename,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
        ];
    }

    private function titleForAddedDocument(?string $title, TemporaryUploadedFile $upload, int $index): ?string
    {
        $title = $this->normalizeNullableAddressInput($title);

        if ($title !== null && $index === 0) {
            return $title;
        }

        if ($title !== null) {
            return "{$title} #" . ($index + 1);
        }

        return $upload->getClientOriginalName();
    }

    private function documentIssuingAuthorityOptions()
    {
        $options = PersonDocument::query()
            ->whereNotNull('issuing_authority')
            ->where('issuing_authority', '!=', '')
            ->distinct()
            ->orderBy('issuing_authority')
            ->limit(100)
            ->pluck('issuing_authority');

        $current = trim($this->documentIssuingAuthority);

        if ($current === '' || $options->containsStrict($current)) {
            return $options;
        }

        return $options
            ->prepend($current)
            ->unique()
            ->values();
    }

    private function personNumberOptions()
    {
        return Person::query()
            ->whereKeyNot($this->person->id)
            ->orderBy('person_number')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->limit(100)
            ->get(['id', 'person_number', 'first_name', 'last_name', 'avatar_path']);
    }

    private function saveDocument(ManagementActivity $managementActivity): void
    {
        $document = $this->currentDocument();
        $before = $document?->only([
            'type',
            'category',
            'title',
            'document_number',
            'issuing_authority',
            'issued_at',
            'expires_at',
        ]) ?? [];

        $attributes = [
            'type' => $this->documentType !== '' ? $this->documentType : PersonDocument::TYPE_OTHER,
            'status' => PersonDocument::STATUS_ACTIVE,
            'category' => $this->documentCategory !== ''
                ? $this->documentCategory
                : $this->documentCategoryForType($this->documentType !== '' ? $this->documentType : PersonDocument::TYPE_OTHER),
            'source' => $document?->source ?? PersonDocument::SOURCE_MANUAL,
            'direction' => $document?->direction ?? PersonDocument::DIRECTION_NONE,
            'title' => $this->normalizeNullableAddressInput($this->documentTitle),
            'document_number' => $this->normalizeNullableAddressInput($this->documentNumber),
            'issuing_authority' => $this->normalizeNullableAddressInput($this->documentIssuingAuthority),
            'issued_at' => $this->documentIssuedAt ?: null,
            'expires_at' => $this->documentExpiresAt ?: null,
            'valid_from' => $this->documentIssuedAt ?: null,
            'valid_until' => $this->documentExpiresAt ?: null,
            'is_current' => true,
        ];

        if ($document === null) {
            $document = PersonDocument::query()->create(array_merge($attributes, [
                'person_id' => $this->person->id,
            ]));
        } else {
            $document->update($attributes);
        }

        $this->person->refresh();
        $this->fillDocumentState($document->refresh());

        $managementActivity->personUpdated(
            person: $this->person,
            before: ['document' => $before],
            after: ['document' => $document->only(array_keys($attributes))],
            sourceComponent: static::class,
        );

        Flux::toast(
            heading: __('Document updated'),
            text: __('The document information has been saved.'),
            variant: 'success',
            duration: 3500,
        );

        $this->finishEditingField();
    }

    private function documentCategoryForType(string $type): string
    {
        return PersonDocumentType::categoryFor($type);
    }

    private function fillEmergencyContactFromPerson(): void
    {
        $contact = $this->primaryEmergencyContact();

        $this->emergencyContactName = (string) ($contact?->name ?? '');
        $this->emergencyContactRelationship = (string) ($contact?->relationship ?? '');
        $this->emergencyContactPhone = (string) ($contact?->phone ?? '');
        $this->emergencyContactEmail = (string) ($contact?->email ?? '');
        $this->emergencyContactPersonId = $contact?->related_person_id;
    }

    private function saveEmergencyContact(ManagementActivity $managementActivity): void
    {
        $contact = $this->primaryEmergencyContact();
        $before = $contact?->only([
            'relationship',
            'related_person_id',
            'name',
            'phone',
            'email',
            'is_primary',
            'is_emergency_contact',
        ]) ?? [];

        $attributes = [
            'relationship' => $this->normalizeNullableAddressInput($this->emergencyContactRelationship),
            'related_person_id' => $this->emergencyContactPersonId,
            'name' => $this->normalizeNullableAddressInput($this->emergencyContactName),
            'phone' => $this->normalizeNullableAddressInput($this->emergencyContactPhone),
            'email' => $this->normalizeNullableAddressInput($this->emergencyContactEmail),
            'is_primary' => true,
            'is_emergency_contact' => true,
            'is_authorized_representative' => false,
        ];

        $hasInput = filled($attributes['relationship'])
            || filled($attributes['related_person_id'])
            || filled($attributes['name'])
            || filled($attributes['phone'])
            || filled($attributes['email']);

        if (! $hasInput) {
            $contact?->delete();
            $this->person->refresh();
            $this->fillEmergencyContactFromPerson();

            $after = [];
        } elseif ($contact === null) {
            $contact = PersonContact::query()->create(array_merge($attributes, [
                'person_id' => $this->person->id,
                'type' => PersonContact::TYPE_EMERGENCY,
            ]));
            $after = $contact->only(array_keys($attributes));
        } else {
            $contact->update(array_merge($attributes, [
                'type' => PersonContact::TYPE_EMERGENCY,
            ]));
            $after = $contact->refresh()->only(array_keys($attributes));
        }

        $this->person->refresh();
        $this->fillEmergencyContactFromPerson();

        $managementActivity->personUpdated(
            person: $this->person,
            before: ['emergency_contact' => $before],
            after: ['emergency_contact' => $after],
            sourceComponent: static::class,
        );

        Flux::toast(
            heading: __('Person updated'),
            text: __('The person record has been updated.'),
            variant: 'success',
            duration: 3500,
        );

        $this->finishEditingField();
    }

    private function primaryEmergencyContact(): ?PersonContact
    {
        return $this->person
            ->contactRows()
            ->where('type', PersonContact::TYPE_EMERGENCY)
            ->orderByDesc('is_primary')
            ->latest('id')
            ->first();
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function personAttributesFromValidated(array $validated): array
    {
        $attributes = [];

        if (array_key_exists('salutation', $validated)) {
            $attributes['salutation'] = $validated['salutation'] ?: null;
        }

        if (array_key_exists('nameTitle', $validated)) {
            $attributes['name_title'] = $validated['nameTitle'] ?: null;
        }

        if (array_key_exists('gender', $validated)) {
            $attributes['gender'] = $validated['gender'] ?: null;
        }

        if (array_key_exists('maritalStatus', $validated)) {
            $attributes['marital_status'] = $validated['maritalStatus'] ?: null;
        }

        if (array_key_exists('firstName', $validated)) {
            $attributes['first_name'] = $validated['firstName'];
        }

        if (array_key_exists('middleName', $validated)) {
            $attributes['middle_name'] = $validated['middleName'] ?: null;
        }

        if (array_key_exists('preferredName', $validated)) {
            $attributes['preferred_name'] = $validated['preferredName'] ?: null;
        }

        if (array_key_exists('lastName', $validated)) {
            $attributes['last_name'] = $validated['lastName'];
        }

        if (array_key_exists('birthName', $validated)) {
            $attributes['birth_name'] = $validated['birthName'] ?: null;
        }

        if (array_key_exists('dateOfBirth', $validated)) {
            $attributes['date_of_birth'] = $validated['dateOfBirth'];
        }

        if (array_key_exists('birthCountryId', $validated)) {
            $attributes['birth_country_id'] = $validated['birthCountryId'];
        }

        if (array_key_exists('birthPlaceText', $validated)) {
            $attributes['birth_place_text'] = $validated['birthPlaceText'];
        }

        if (array_key_exists('phone', $validated)) {
            $attributes['phone'] = $validated['phone'] ?: null;
        }

        if (array_key_exists('mobile', $validated)) {
            $attributes['mobile'] = $validated['mobile'] ?: null;
        }

        if (array_key_exists('emailPrivate', $validated)) {
            $attributes['email_private'] = $validated['emailPrivate'] ?: null;
        }

        if (array_key_exists('emailWork', $validated)) {
            $attributes['email_work'] = $validated['emailWork'] ?: null;
        }

        return $attributes;
    }
}
