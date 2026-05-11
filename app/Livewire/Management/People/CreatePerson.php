<?php

// app/Livewire/Management/People/CreatePerson.php

namespace App\Livewire\Management\People;

use App\Models\Address;
use App\Models\Country;
use App\Models\InsuranceProvider;
use App\Models\Language;
use App\Models\Person;
use App\Models\PersonHealthInsurance;
use App\Models\PersonAddress;
use App\Models\PersonContact;
use App\Models\PersonDocument;
use App\Models\PersonIdentifier;
use App\Models\PersonLanguage;
use App\Models\PersonNationality;
use App\Models\User;
use App\Support\Audit\ManagementActivity;
use App\Support\Auth\GeneratedPasswordLogger;
use App\Support\Avatar\AvatarPath;
use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use RuntimeException;
use Spatie\Permission\Models\Role;

class CreatePerson extends Component
{
    use WithFileUploads;

    private const PERSON_NUMBER_ALPHABET = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

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

    public $avatarUpload = null;

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

    public ?int $primaryNationalityCountryId = null;
    public ?int $primaryLanguageId = null;

    public string $nationalIdNumber = '';
    public string $nationalIdIssuingAuthority = '';
    public string $taxId = '';
    public string $socialSecurityNumber = '';

    public string $pensionInsuranceNumber = '';
    public string $healthInsuranceNumber = '';

    public ?int $healthInsuranceProviderId = null;

    public string $residencePermitNumber = '';

    public string $documentType = '';
    public string $documentTitle = '';
    public string $documentNumber = '';
    public string $documentIssuingAuthority = '';
    public ?string $documentIssuedAt = null;
    public ?string $documentExpiresAt = null;

    public $documentUpload = null;

    public string $emergencyContactName = '';
    public string $emergencyContactRelationship = '';
    public string $emergencyContactPhone = '';
    public string $emergencyContactEmail = '';

    public string $email = '';

    public ?int $createdPersonId = null;
    public ?int $createdUserId = null;
    public ?int $createdDocumentId = null;
    public string $generatedPassword = '';
    public string $createdPersonNumber = '';

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

    public array $maritalStatusOptions = [
        'single' => 'Single',
        'married' => 'Married',
        'registered_partnership' => 'Registered partnership',
        'divorced' => 'Divorced',
        'widowed' => 'Widowed',
        'separated' => 'Separated',
        'unknown' => 'Unknown',
    ];

    public array $documentTypeOptions = [
        PersonDocument::TYPE_ID_CARD_COPY => 'ID card copy',
        PersonDocument::TYPE_PASSPORT_COPY => 'Passport copy',
        PersonDocument::TYPE_RESIDENCE_PERMIT_COPY => 'Residence permit copy',
        PersonDocument::TYPE_HEALTH_INSURANCE_PROOF => 'Health insurance proof',
        PersonDocument::TYPE_TAX_DOCUMENT => 'Tax document',
        PersonDocument::TYPE_OTHER => 'Other',
    ];

    public array $requiredFields = [
        'salutation' => false,
        'nameTitle' => false,
        'gender' => false,
        'maritalStatus' => false,
        'firstName' => true,
        'middleName' => false,
        'preferredName' => false,
        'lastName' => true,
        'birthName' => false,
        'dateOfBirth' => true,
        'avatarUpload' => false,
        'birthCountryId' => false,
        'birthPlaceText' => false,
        'phone' => false,
        'mobile' => false,
        'emailPrivate' => false,
        'emailWork' => false,
        'addressCountryId' => false,
        'addressPostalCode' => false,
        'addressCity' => false,
        'addressStreet' => false,
        'addressHouseNumber' => false,
        'addressLine2' => false,
        'primaryNationalityCountryId' => false,
        'primaryLanguageId' => false,
        'nationalIdNumber' => false,
        'nationalIdIssuingAuthority' => false,
        'taxId' => false,
        'socialSecurityNumber' => false,
        'pensionInsuranceNumber' => false,
        'healthInsuranceNumber' => false,
        'healthInsuranceProviderId' => false,
        'residencePermitNumber' => false,
        'emergencyContactName' => false,
        'emergencyContactRelationship' => false,
        'emergencyContactPhone' => false,
        'emergencyContactEmail' => false,
        'email' => true,
        'documentType' => false,
        'documentTitle' => false,
        'documentNumber' => false,
        'documentIssuingAuthority' => false,
        'documentIssuedAt' => false,
        'documentExpiresAt' => false,
        'documentUpload' => false,
    ];

    private function validationFieldMeta(): array
    {
        return [
            'salutation' => [
                'label' => __('Salutation'),
                'input_id' => 'create-person-salutation',
            ],
            'nameTitle' => [
                'label' => __('Title'),
                'input_id' => 'create-person-name-title',
            ],
            'gender' => [
                'label' => __('Gender'),
                'input_id' => 'create-person-gender',
            ],
            'maritalStatus' => [
                'label' => __('Marital status'),
                'input_id' => 'create-person-marital-status',
            ],
            'firstName' => [
                'label' => __('First name'),
                'input_id' => 'create-person-first-name',
            ],
            'middleName' => [
                'label' => __('Middle name'),
                'input_id' => 'create-person-middle-name',
            ],
            'preferredName' => [
                'label' => __('Preferred name'),
                'input_id' => 'create-person-preferred-name',
            ],
            'lastName' => [
                'label' => __('Last name'),
                'input_id' => 'create-person-last-name',
            ],
            'dateOfBirth' => [
                'label' => __('Date of birth'),
                'input_id' => 'create-person-date-of-birth',
            ],
            'avatarUpload' => [
                'label' => __('Avatar / passphoto'),
                'input_id' => 'create-person-avatar-upload',
            ],
            'birthCountryId' => [
                'label' => __('Birth country'),
                'input_id' => 'create-person-birth-country',
            ],
            'birthPlaceText' => [
                'label' => __('Birth place'),
                'input_id' => 'create-person-birth-place',
            ],
            'birthName' => [
                'label' => __('Birth name'),
                'input_id' => 'create-person-birth-name',
            ],
            'phone' => [
                'label' => __('Phone'),
                'input_id' => 'create-person-phone',
            ],
            'mobile' => [
                'label' => __('Mobile'),
                'input_id' => 'create-person-mobile',
            ],
            'emailPrivate' => [
                'label' => __('Private email'),
                'input_id' => 'create-person-email-private',
            ],
            'emailWork' => [
                'label' => __('Work email'),
                'input_id' => 'create-person-email-work',
            ],
            'addressCountryId' => [
                'label' => __('Address country'),
                'input_id' => 'create-person-address-country',
            ],
            'addressPostalCode' => [
                'label' => __('Postal code'),
                'input_id' => 'create-person-address-postal-code',
            ],
            'addressCity' => [
                'label' => __('City'),
                'input_id' => 'create-person-address-city',
            ],
            'addressStreet' => [
                'label' => __('Street'),
                'input_id' => 'create-person-address-street',
            ],
            'addressHouseNumber' => [
                'label' => __('House number'),
                'input_id' => 'create-person-address-house-number',
            ],
            'addressLine2' => [
                'label' => __('Address line 2'),
                'input_id' => 'create-person-address-line-2',
            ],
            'email' => [
                'label' => __('Email'),
                'input_id' => 'create-person-email',
            ],
            'primaryNationalityCountryId' => [
                'label' => __('Primary nationality'),
                'input_id' => 'create-person-primary-nationality',
            ],
            'primaryLanguageId' => [
                'label' => __('Primary language'),
                'input_id' => 'create-person-primary-language',
            ],
            'nationalIdNumber' => [
                'label' => __('National ID number'),
                'input_id' => 'create-person-national-id-number',
            ],
            'nationalIdIssuingAuthority' => [
                'label' => __('Issuing authority'),
                'input_id' => 'create-person-national-id-issuing-authority',
            ],
            'taxId' => [
                'label' => __('Tax ID'),
                'input_id' => 'create-person-tax-id',
            ],
            'socialSecurityNumber' => [
                'label' => __('Social security number'),
                'input_id' => 'create-person-social-security-number',
            ],
            'emergencyContactName' => [
                'label' => __('Emergency contact name'),
                'input_id' => 'create-person-emergency-contact-name',
            ],
            'emergencyContactRelationship' => [
                'label' => __('Emergency contact relationship'),
                'input_id' => 'create-person-emergency-contact-relationship',
            ],
            'emergencyContactPhone' => [
                'label' => __('Emergency contact phone'),
                'input_id' => 'create-person-emergency-contact-phone',
            ],
            'emergencyContactEmail' => [
                'label' => __('Emergency contact email'),
                'input_id' => 'create-person-emergency-contact-email',
            ],
            'pensionInsuranceNumber' => [
                'label' => __('Pension insurance number'),
                'input_id' => 'create-person-pension-insurance-number',
            ],
            'healthInsuranceNumber' => [
                'label' => __('Health insurance number'),
                'input_id' => 'create-person-health-insurance-number',
            ],
            'healthInsuranceProviderId' => [
                'label' => __('Health insurance provider'),
                'input_id' => 'create-person-health-insurance-provider',
            ],
            'residencePermitNumber' => [
                'label' => __('Residence permit number'),
                'input_id' => 'create-person-residence-permit-number',
            ],
            'documentType' => [
                'label' => __('Document type'),
                'input_id' => 'create-person-document-type',
            ],
            'documentTitle' => [
                'label' => __('Document title'),
                'input_id' => 'create-person-document-title',
            ],
            'documentNumber' => [
                'label' => __('Document number'),
                'input_id' => 'create-person-document-number',
            ],
            'documentIssuingAuthority' => [
                'label' => __('Document issuing authority'),
                'input_id' => 'create-person-document-issuing-authority',
            ],
            'documentIssuedAt' => [
                'label' => __('Document issued at'),
                'input_id' => 'create-person-document-issued-at',
            ],
            'documentExpiresAt' => [
                'label' => __('Document expires at'),
                'input_id' => 'create-person-document-expires-at',
            ],
            'documentUpload' => [
                'label' => __('Document file'),
                'input_id' => 'create-person-document-upload',
            ],
        ];
    }

    public function create(GeneratedPasswordLogger $passwordLogger, ManagementActivity $managementActivity): void
    {
        $this->resetCreatedState();

        $validated = $this->validateForCreate();

        $plainPassword = Str::password(16);
        $userName = $this->buildUserName($validated['firstName'], $validated['lastName']);

        $result = DB::transaction(function () use ($validated, $plainPassword, $userName): array {
            $person = Person::query()->create([
                'person_number' => $this->buildUniquePersonNumber(),
                'salutation' => $validated['salutation'] ?: null,
                'name_title' => $validated['nameTitle'] ?: null,
                'gender' => $validated['gender'] ?: null,
                'marital_status' => $validated['maritalStatus'] ?: null,
                'first_name' => $validated['firstName'],
                'middle_name' => $validated['middleName'] ?: null,
                'preferred_name' => $validated['preferredName'] ?: null,
                'last_name' => $validated['lastName'],
                'birth_name' => $validated['birthName'] ?: null,
                'date_of_birth' => $validated['dateOfBirth'] ?: null,
                'birth_country_id' => $validated['birthCountryId'] ?: null,
                'birth_place_text' => $validated['birthPlaceText'] ?: null,
                'phone' => $validated['phone'] ?: null,
                'mobile' => $validated['mobile'] ?: null,
                'email_private' => $validated['emailPrivate'] ?: null,
                'email_work' => $validated['emailWork'] ?: null,
            ]);

            $avatarPath = $this->storeInitialAvatar($validated['avatarUpload'] ?? null);

            if ($avatarPath !== null) {
                $person->update([
                    'avatar_path' => $avatarPath,
                ]);
            }

            $user = User::query()->create([
                'person_id' => $person->id,
                'name' => $userName,
                'email' => $validated['email'],
                'password' => $plainPassword,
            ]);

            $roleName = Role::query()
                ->where('name', 'User')
                ->where('is_assignable', true)
                ->value('name');

            if ($roleName !== null) {
                $user->assignRole($roleName);
            }

            if ($this->hasAddressInput($validated)) {
                $this->createInitialAddress($person, $validated);
            }

            if (filled($validated['primaryNationalityCountryId'] ?? null)) {
                $this->createInitialNationality($person, $validated);
            }

            if (filled($validated['primaryLanguageId'] ?? null)) {
                $this->createInitialLanguage($person, $validated);
            }

            $this->createInitialIdentifiers($person, $validated);

            $this->createInitialHealthInsurance($person, $validated);

            $this->createInitialEmergencyContact($person, $validated);

            $this->createInitialDocument($person, $validated);

            return [
                'person' => $person,
                'user' => $user,
            ];
        });

        /** @var Person $person */
        $person = $result['person'];

        /** @var User $user */
        $user = $result['user'];

        $generatedPasswordLogged = $passwordLogger->write(
            user: $user,
            person: $person,
            password: $plainPassword,
            createdByUser: auth()->user() instanceof User ? auth()->user() : null,
        );

        $managementActivity->personCreated(
            person: $person,
            user: $user,
            generatedPasswordLogged: $generatedPasswordLogged,
            sourceComponent: static::class,
        );

        $this->createdPersonId = $person->id;
        $this->createdUserId = $user->id;
        $this->generatedPassword = $plainPassword;
        $this->createdPersonNumber = (string) $person->person_number;

        $this->salutation = '';
        $this->nameTitle = '';
        $this->gender = '';
        $this->maritalStatus = '';
        $this->firstName = '';
        $this->middleName = '';
        $this->preferredName = '';
        $this->lastName = '';
        $this->birthName = '';
        $this->dateOfBirth = null;
        $this->avatarUpload = null;
        $this->birthCountryId = null;
        $this->birthPlaceText = '';
        $this->phone = '';
        $this->mobile = '';
        $this->emailPrivate = '';
        $this->emailWork = '';
        $this->addressCountryId = null;
        $this->addressPostalCode = '';
        $this->addressCity = '';
        $this->addressStreet = '';
        $this->addressHouseNumber = '';
        $this->addressLine2 = '';
        $this->primaryNationalityCountryId = null;
        $this->primaryLanguageId = null;
        $this->nationalIdNumber = '';
        $this->nationalIdIssuingAuthority = '';
        $this->taxId = '';
        $this->socialSecurityNumber = '';
        $this->pensionInsuranceNumber = '';
        $this->healthInsuranceNumber = '';
        $this->healthInsuranceProviderId = null;
        $this->residencePermitNumber = '';
        $this->emergencyContactName = '';
        $this->emergencyContactRelationship = '';
        $this->emergencyContactPhone = '';
        $this->emergencyContactEmail = '';
        $this->documentType = '';
        $this->documentTitle = '';
        $this->documentNumber = '';
        $this->documentIssuingAuthority = '';
        $this->documentIssuedAt = null;
        $this->documentExpiresAt = null;
        $this->documentUpload = null;
        $this->email = '';

        Flux::toast(
            heading: __('Person created'),
            text: __('The person and login account have been created.'),
            variant: 'success',
            duration: 4000,
        );
    }

    public function clearGeneratedPassword(): void
    {
        $this->generatedPassword = '';
    }

    public function focusValidationField(string $field): void
    {
        $meta = $this->validationFieldMeta()[$field] ?? null;

        if (! is_array($meta) || ! isset($meta['input_id'])) {
            return;
        }

        $this->dispatch('buergerfrs:focus-field', inputId: $meta['input_id']);
    }

    public function render()
    {
        return view('components.management.people.⚡create-person', [
            'birthCountryOptions' => Country::query()
                ->active()
                ->ordered()
                ->get(['id', 'iso2', 'name', 'native_name']),
            'addressCountryOptions' => Country::query()
                ->active()
                ->ordered()
                ->get(['id', 'iso2', 'name', 'native_name']),
            'nationalityCountryOptions' => Country::query()
                ->active()
                ->ordered()
                ->get(['id', 'iso2', 'name', 'native_name']),
            'languageOptions' => Language::query()
                ->active()
                ->ordered()
                ->get(['id', 'iso639_1', 'iso639_3', 'name', 'native_name']),
            'healthInsuranceProviderOptions' => InsuranceProvider::query()
                ->active()
                ->where('type', InsuranceProvider::TYPE_HEALTH)
                ->ordered()
                ->get(['id', 'name', 'short_name', 'code']),
        ]);
    }

    private function resetCreatedState(): void
    {
        $this->createdPersonId = null;
        $this->createdUserId = null;
        $this->createdDocumentId = null;
        $this->generatedPassword = '';
        $this->createdPersonNumber = '';
    }

    /**
     * @return array{
     *     salutation: string|null,
     *     nameTitle: string|null,
     *     gender: string|null,
     *     maritalStatus: string|null,
     *     firstName: string,
     *     middleName: string|null,
     *     preferredName: string|null,
     *     lastName: string,
     *     birthName: string|null,
     *     dateOfBirth: string,
     *     avatarUpload: mixed,
     *     birthCountryId: int|null,
     *     birthPlaceText: string|null,
     *     phone: string|null,
     *     mobile: string|null,
     *     emailPrivate: string|null,
     *     emailWork: string|null,
     *     addressCountryId: int|null,
     *     addressPostalCode: string|null,
     *     addressCity: string|null,
     *     addressStreet: string|null,
     *     addressHouseNumber: string|null,
     *     addressLine2: string|null,
     *     primaryNationalityCountryId: int|null,
     *     primaryLanguageId: int|null,
     *     nationalIdNumber: string|null,
     *     nationalIdIssuingAuthority: string|null,
     *     taxId: string|null,
     *     socialSecurityNumber: string|null,
     *     pensionInsuranceNumber: string|null,
     *     healthInsuranceNumber: string|null,
     *     healthInsuranceProviderId: int|null,
     *     residencePermitNumber: string|null,
     *     documentType: string|null,
     *     documentTitle: string|null,
     *     documentNumber: string|null,
     *     documentIssuingAuthority: string|null,
     *     documentIssuedAt: string|null,
     *     documentExpiresAt: string|null,
     *     documentUpload: mixed,
     *     emergencyContactName: string|null,
     *     emergencyContactRelationship: string|null,
     *     emergencyContactPhone: string|null,
     *     emergencyContactEmail: string|null,
     *     email: string
     * }
     */
    private function validateForCreate(): array
    {
        try {
            return $this->validate($this->validationRules());
        } catch (ValidationException $exception) {
            $this->toastValidationErrors($exception);

            throw $exception;
        }
    }

    private function storeInitialAvatar(mixed $upload): ?string
    {
        if (! $upload instanceof TemporaryUploadedFile) {
            return null;
        }

        $extension = $upload->getClientOriginalExtension();

        if ($extension === '') {
            $extension = $upload->extension();
        }

        $uuid = (string) Str::uuid();
        $path = AvatarPath::relativePath($uuid, $extension);

        AvatarPath::ensurePublicDiskDirectoryExists($uuid);

        $upload->storeAs(
            dirname($path),
            basename($path),
            'public',
        );

        return $path;
    }

    private function toastValidationErrors(ValidationException $exception): void
    {
        $errors = $exception->validator->errors();
        $fieldMeta = $this->validationFieldMeta();
        $fieldKeys = collect($errors->keys())->unique()->values();

        $validationErrors = $fieldKeys
            ->map(function (string $field) use ($errors, $fieldMeta): array {
                $meta = $fieldMeta[$field] ?? [
                    'label' => $field,
                    'input_id' => null,
                ];

                return [
                    'field' => $field,
                    'label' => $meta['label'],
                    'inputId' => $meta['input_id'],
                    'messages' => collect($errors->get($field))->unique()->values()->all(),
                ];
            })
            ->values();

        $this->dispatch('buergerfrs:validation-errors', errors: $validationErrors->all());
    }

    private function validationRules(): array
    {
        return [
            'salutation' => [$this->requiredRule('salutation'), 'string', Rule::in(array_keys($this->salutationOptions))],
            'nameTitle' => [$this->requiredRule('nameTitle'), 'string', 'max:255'],
            'gender' => [$this->requiredRule('gender'), 'string', Rule::in(array_keys($this->genderOptions))],
            'maritalStatus' => [$this->requiredRule('maritalStatus'), 'string', Rule::in(array_keys($this->maritalStatusOptions))],
            'firstName' => [$this->requiredRule('firstName'), 'string', 'max:255'],
            'middleName' => [$this->requiredRule('middleName'), 'string', 'max:255'],
            'preferredName' => [$this->requiredRule('preferredName'), 'string', 'max:255'],
            'lastName' => [$this->requiredRule('lastName'), 'string', 'max:255'],
            'birthName' => [$this->requiredRule('birthName'), 'string', 'max:255'],
            'dateOfBirth' => [$this->requiredRule('dateOfBirth'), 'date'],
            'avatarUpload' => [$this->requiredRule('avatarUpload'), 'file', 'image', 'max:4096', 'mimes:jpg,jpeg,png,webp'],
            'birthCountryId' => [$this->requiredRule('birthCountryId'), 'integer', Rule::exists('countries', 'id')],
            'birthPlaceText' => [$this->requiredRule('birthPlaceText'), 'string', 'max:255'],
            'phone' => [$this->requiredRule('phone'), 'string', 'max:255'],
            'mobile' => [$this->requiredRule('mobile'), 'string', 'max:255'],
            'emailPrivate' => [$this->requiredRule('emailPrivate'), 'email', 'max:255'],
            'emailWork' => [$this->requiredRule('emailWork'), 'email', 'max:255'],
            'email' => [$this->requiredRule('email'), 'email', 'max:255', Rule::unique('users', 'email')],
            'addressCountryId' => [$this->requiredRule('addressCountryId'), 'integer', Rule::exists('countries', 'id')],
            'addressPostalCode' => [$this->requiredRule('addressPostalCode'), 'string', 'max:255'],
            'addressCity' => [$this->requiredRule('addressCity'), 'string', 'max:255'],
            'addressStreet' => [$this->requiredRule('addressStreet'), 'string', 'max:255'],
            'addressHouseNumber' => [$this->requiredRule('addressHouseNumber'), 'string', 'max:255'],
            'addressLine2' => [$this->requiredRule('addressLine2'), 'string', 'max:255'],
            'primaryNationalityCountryId' => [$this->requiredRule('primaryNationalityCountryId'), 'integer', Rule::exists('countries', 'id')],
            'primaryLanguageId' => [$this->requiredRule('primaryLanguageId'), 'integer', Rule::exists('languages', 'id')],
            'nationalIdNumber' => [$this->requiredRule('nationalIdNumber'), 'string', 'max:255'],
            'nationalIdIssuingAuthority' => [$this->requiredRule('nationalIdIssuingAuthority'), 'string', 'max:255'],
            'taxId' => [$this->requiredRule('taxId'), 'string', 'max:255'],
            'socialSecurityNumber' => [$this->requiredRule('socialSecurityNumber'), 'string', 'max:255'],
            'pensionInsuranceNumber' => [$this->requiredRule('pensionInsuranceNumber'), 'string', 'max:255'],
            'healthInsuranceNumber' => [$this->requiredRule('healthInsuranceNumber'), 'string', 'max:255'],
            'healthInsuranceProviderId' => [
                $this->requiredRule('healthInsuranceProviderId'),
                'integer',
                Rule::exists('insurance_providers', 'id')
                    ->where('type', InsuranceProvider::TYPE_HEALTH)
                    ->where('is_active', true),
            ],
            'residencePermitNumber' => [$this->requiredRule('residencePermitNumber'), 'string', 'max:255'],
            'documentType' => [$this->requiredRule('documentType'), 'string', Rule::in(array_keys($this->documentTypeOptions))],
            'documentTitle' => [$this->requiredRule('documentTitle'), 'string', 'max:255'],
            'documentNumber' => [$this->requiredRule('documentNumber'), 'string', 'max:255'],
            'documentIssuingAuthority' => [$this->requiredRule('documentIssuingAuthority'), 'string', 'max:255'],
            'documentIssuedAt' => [$this->requiredRule('documentIssuedAt'), 'date'],
            'documentExpiresAt' => [$this->requiredRule('documentExpiresAt'), 'date', 'after_or_equal:documentIssuedAt'],
            'documentUpload' => [$this->requiredRule('documentUpload'), 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,webp'],
            'emergencyContactName' => [$this->requiredRule('emergencyContactName'), 'string', 'max:255'],
            'emergencyContactRelationship' => [$this->requiredRule('emergencyContactRelationship'), 'string', Rule::in(array_keys($this->emergencyContactRelationshipOptions))],
            'emergencyContactPhone' => [$this->requiredRule('emergencyContactPhone'), 'string', 'max:255'],
            'emergencyContactEmail' => [$this->requiredRule('emergencyContactEmail'), 'email', 'max:255'],
        ];
    }

    /**
     * @param array<string, mixed> $validated
     */
    private function hasAddressInput(array $validated): bool
    {
        return filled($validated['addressCountryId'] ?? null)
            || filled($validated['addressPostalCode'] ?? null)
            || filled($validated['addressCity'] ?? null)
            || filled($validated['addressStreet'] ?? null)
            || filled($validated['addressHouseNumber'] ?? null)
            || filled($validated['addressLine2'] ?? null);
    }

    /**
     * @param array<string, mixed> $validated
     */
    private function createInitialAddress(Person $person, array $validated): void
    {
        $address = Address::query()->create([
            'country_id' => $validated['addressCountryId'] ?: null,
            'postal_code' => $validated['addressPostalCode'] ?: null,
            'city' => $validated['addressCity'] ?: null,
            'street' => $validated['addressStreet'] ?: null,
            'house_number' => $validated['addressHouseNumber'] ?: null,
            'address_line_2' => $validated['addressLine2'] ?: null,
        ]);

        PersonAddress::query()->create([
            'person_id' => $person->id,
            'address_id' => $address->id,
            'type' => PersonAddress::TYPE_HOME,
            'is_primary' => true,
        ]);
    }

    /**
     * @param array<string, mixed> $validated
     */
    private function createInitialNationality(Person $person, array $validated): void
    {
        PersonNationality::query()->create([
            'person_id' => $person->id,
            'country_id' => $validated['primaryNationalityCountryId'],
            'is_primary' => true,
        ]);
    }

    /**
     * @param array<string, mixed> $validated
     */
    private function createInitialLanguage(Person $person, array $validated): void
    {
        PersonLanguage::query()->create([
            'person_id' => $person->id,
            'language_id' => $validated['primaryLanguageId'],
            'proficiency' => PersonLanguage::PROFICIENCY_UNKNOWN,
            'is_native' => false,
            'is_primary' => true,
            'preferred_for_communication' => true,
        ]);
    }

    /**
     * @param array<string, mixed> $validated
     */
    private function createInitialIdentifiers(Person $person, array $validated): void
    {
        $this->createInitialIdentifier(
            person: $person,
            type: PersonIdentifier::TYPE_NATIONAL_ID,
            value: $validated['nationalIdNumber'] ?? null,
            issuingAuthority: $validated['nationalIdIssuingAuthority'] ?? null,
            isPrimary: true,
        );

        $this->createInitialIdentifier(
            person: $person,
            type: PersonIdentifier::TYPE_TAX_ID,
            value: $validated['taxId'] ?? null,
        );

        $this->createInitialIdentifier(
            person: $person,
            type: PersonIdentifier::TYPE_SOCIAL_SECURITY_NUMBER,
            value: $validated['socialSecurityNumber'] ?? null,
        );

        $this->createInitialIdentifier(
            person: $person,
            type: PersonIdentifier::TYPE_PENSION_INSURANCE_NUMBER,
            value: $validated['pensionInsuranceNumber'] ?? null,
        );

        $this->createInitialIdentifier(
            person: $person,
            type: PersonIdentifier::TYPE_HEALTH_INSURANCE_NUMBER,
            value: $validated['healthInsuranceNumber'] ?? null,
        );

        $this->createInitialIdentifier(
            person: $person,
            type: PersonIdentifier::TYPE_RESIDENCE_PERMIT_NUMBER,
            value: $validated['residencePermitNumber'] ?? null,
        );
    }

    /**
     * @param array<string, mixed> $validated
     */
    private function createInitialHealthInsurance(Person $person, array $validated): void
    {
        if (! $this->hasHealthInsuranceInput($validated)) {
            return;
        }

        PersonHealthInsurance::query()->create([
            'person_id' => $person->id,
            'insurance_provider_id' => $validated['healthInsuranceProviderId'] ?: null,
            'insurance_number' => $validated['healthInsuranceNumber'] ?: null,
            'is_primary' => true,
        ]);
    }

    /**
     * @param array<string, mixed> $validated
     */
    private function hasHealthInsuranceInput(array $validated): bool
    {
        return filled($validated['healthInsuranceProviderId'] ?? null)
            || filled($validated['healthInsuranceNumber'] ?? null);
    }

    /**
     * @param array<string, mixed> $validated
     */
    private function createInitialEmergencyContact(Person $person, array $validated): void
    {
        if (! $this->hasEmergencyContactInput($validated)) {
            return;
        }

        PersonContact::query()->create([
            'person_id' => $person->id,
            'related_person_id' => null,
            'type' => PersonContact::TYPE_EMERGENCY,
            'relationship' => $validated['emergencyContactRelationship'] ?: null,
            'name' => $validated['emergencyContactName'] ?: null,
            'phone' => $validated['emergencyContactPhone'] ?: null,
            'email' => $validated['emergencyContactEmail'] ?: null,
            'is_primary' => true,
            'is_emergency_contact' => true,
        ]);
    }

    /**
     * @param array<string, mixed> $validated
     */
    private function createInitialDocument(Person $person, array $validated): void
    {
        if (! $this->hasDocumentMetadataInput($validated)) {
            return;
        }

        $uploadAttributes = $this->storeInitialDocumentUpload(
            person: $person,
            upload: $validated['documentUpload'] ?? null,
        );

        $document = PersonDocument::query()->create(array_merge([
            'person_id' => $person->id,
            'type' => $validated['documentType'] ?: PersonDocument::TYPE_OTHER,
            'title' => $validated['documentTitle'] ?: null,
            'document_number' => $validated['documentNumber'] ?: null,
            'issuing_authority' => $validated['documentIssuingAuthority'] ?: null,
            'issued_at' => $validated['documentIssuedAt'] ?: null,
            'expires_at' => $validated['documentExpiresAt'] ?: null,
        ], $uploadAttributes));

        if (filled($document->file_path)) {
            $this->createdDocumentId = $document->id;
        }
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
    private function storeInitialDocumentUpload(Person $person, mixed $upload): array
    {
        if (! $upload instanceof TemporaryUploadedFile) {
            return [];
        }

        $extension = $upload->getClientOriginalExtension();
        $originalFilename = $upload->getClientOriginalName();
        $mimeType = $upload->getMimeType();
        $fileSize = $upload->getSize();

        $storedFilename = (string) Str::uuid() . ($extension !== '' ? ".{$extension}" : '');
        $directory = "person-documents/{$person->id}";

        $path = $upload->storeAs($directory, $storedFilename, 'local');

        return [
            'file_disk' => 'local',
            'file_path' => $path,
            'original_filename' => $originalFilename,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
        ];
    }

    /**
     * @param array<string, mixed> $validated
     */
    private function hasDocumentMetadataInput(array $validated): bool
    {
        return filled($validated['documentType'] ?? null)
            || filled($validated['documentTitle'] ?? null)
            || filled($validated['documentNumber'] ?? null)
            || filled($validated['documentIssuingAuthority'] ?? null)
            || filled($validated['documentIssuedAt'] ?? null)
            || filled($validated['documentExpiresAt'] ?? null)
            || filled($validated['documentUpload'] ?? null);
    }

    /**
     * @param array<string, mixed> $validated
     */
    private function hasEmergencyContactInput(array $validated): bool
    {
        return filled($validated['emergencyContactName'] ?? null)
            || filled($validated['emergencyContactRelationship'] ?? null)
            || filled($validated['emergencyContactPhone'] ?? null)
            || filled($validated['emergencyContactEmail'] ?? null);
    }

    private function createInitialIdentifier(
        Person $person,
        string $type,
        ?string $value,
        ?string $issuingAuthority = null,
        bool $isPrimary = false,
    ): void {
        $normalizedValue = $this->normalizeIdentifierValue($value);

        if ($normalizedValue === null) {
            return;
        }

        PersonIdentifier::query()->create([
            'person_id' => $person->id,
            'type' => $type,
            'value' => $normalizedValue,
            'value_hash' => hash('sha256', $normalizedValue),
            'issuing_authority' => filled($issuingAuthority) ? trim((string) $issuingAuthority) : null,
            'is_primary' => $isPrimary,
        ]);
    }

    private function normalizeIdentifierValue(?string $value): ?string
    {
        if (! filled($value)) {
            return null;
        }

        return trim((string) $value);
    }

    private function requiredRule(string $field): string
    {
        return ($this->requiredFields[$field] ?? false) ? 'required' : 'nullable';
    }

    private function buildUserName(string $firstName, string $lastName): string
    {
        return trim($firstName . ' ' . $lastName);
    }

    private function buildUniquePersonNumber(): string
    {
        for ($attempt = 1; $attempt <= 10; $attempt++) {
            $personNumber = $this->buildPersonNumber();

            if (! Person::query()->where('person_number', $personNumber)->exists()) {
                return $personNumber;
            }
        }

        throw new RuntimeException('Unable to generate a unique person number.');
    }

    private function buildPersonNumber(): string
    {
        $code = $this->buildPersonNumberCode(18);
        $checkCharacter = $this->buildPersonNumberCheckCharacter($code);

        return sprintf(
            'P-%s-%s',
            $this->formatPersonNumberCode($code),
            $checkCharacter,
        );
    }

    private function buildPersonNumberCode(int $length): string
    {
        $alphabetLength = strlen(self::PERSON_NUMBER_ALPHABET);
        $code = '';

        for ($position = 0; $position < $length; $position++) {
            $code .= self::PERSON_NUMBER_ALPHABET[random_int(0, $alphabetLength - 1)];
        }

        return $code;
    }

    private function buildPersonNumberCheckCharacter(string $code): string
    {
        $sum = 0;
        $length = strlen($code);

        for ($position = 0; $position < $length; $position++) {
            $value = strpos(self::PERSON_NUMBER_ALPHABET, $code[$position]);

            if ($value === false) {
                throw new RuntimeException('Unable to calculate person number check character.');
            }

            $sum += $value * ($position + 1);
        }

        return self::PERSON_NUMBER_ALPHABET[$sum % strlen(self::PERSON_NUMBER_ALPHABET)];
    }

    private function formatPersonNumberCode(string $code): string
    {
        return implode('-', str_split($code, 4));
    }
}
