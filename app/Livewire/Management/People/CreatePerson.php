<?php

// app/Livewire/Management/People/CreatePerson.php

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
use App\Models\User;
use App\Support\Audit\ManagementActivity;
use App\Support\Auth\GeneratedPasswordLogger;
use App\Support\Avatar\AvatarPath;
use App\Support\Documents\PersonDocumentPath;
use App\Support\Forms\FormFieldRegistry;
use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use RuntimeException;
use Spatie\Permission\Models\Role;

/**
 * Management form component for creating a person with initial related records.
 */
class CreatePerson extends Component
{
    use WithFileUploads;

    private const FORM_KEY = 'management.people.create-person';

    private const PERSON_NUMBER_ALPHABET = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    private const DEFAULT_DATE_OF_BIRTH_YEARS_AGO = 30;

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

    public array $primaryNationalityCountryId = [];

    public array $primaryLanguageId = [];

    public array $languageAbilities = [];

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

    public array $documentUpload = [];

    public string $emergencyContactName = '';

    public string $emergencyContactRelationship = '';

    public string $emergencyContactPhone = '';

    public string $emergencyContactEmail = '';

    public string $email = '';

    public string $activeFormTab = 'person';

    public ?int $createdPersonId = null;

    public ?int $createdUserId = null;

    public ?int $createdDocumentId = null;

    public string $generatedPassword = '';

    public string $createdPersonNumber = '';

    public bool $isTestData = false;

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

    public array $documentTypeOptions = [];

    public function mount(): void
    {
        $this->refreshDocumentTypeOptions();
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

    public function updated(string $propertyName): void
    {
        $field = $this->validationFieldForProperty($propertyName);

        if ($field === null) {
            return;
        }

        $this->validateFieldIfItHasError($field);
    }

    public function updatedIsTestData(mixed $value): void
    {
        $this->isTestData = filter_var($value, FILTER_VALIDATE_BOOLEAN);

        if (! $this->isTestData) {
            return;
        }

        $this->fillTestDataFields();
    }

    public function updatedAddressCountryId(): void
    {
        $this->addressPostalCode = '';
        $this->addressCity = '';
        $this->addressStreet = '';
    }

    public function updatedBirthCountryId(): void
    {
        $this->birthPlaceText = '';
        $this->resetValidation('birthPlaceText');
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

    public function updatedBirthPlaceText(mixed $value): void
    {
        $this->birthPlaceText = trim((string) $value);
    }

    public function updatedDocumentIssuingAuthority(mixed $value): void
    {
        $this->documentIssuingAuthority = trim((string) $value);
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

    public function useCreatedBirthPlaceText(mixed $value): void
    {
        if ($this->birthCountryId === null) {
            $this->birthPlaceText = '';

            return;
        }

        $this->updatedBirthPlaceText($value);
        $this->validateFieldIfItHasError('birthPlaceText');
    }

    public function useCreatedDocumentIssuingAuthority(mixed $value): void
    {
        $this->updatedDocumentIssuingAuthority($value);
    }

    public function removeDocumentUpload(int $index): void
    {
        unset($this->documentUpload[$index]);

        $this->documentUpload = array_values($this->documentUpload);
    }

    public function removeAvatarUpload(): void
    {
        $this->avatarUpload = null;
    }

    public function updatedPrimaryNationalityCountryId(mixed $value): void
    {
        $this->primaryNationalityCountryId = $this->normalizeSelectedIds($value);
    }

    public function updatedPrimaryLanguageId(mixed $value): void
    {
        $this->primaryLanguageId = $this->normalizeSelectedIds($value);
        $this->syncLanguageAbilities();
    }

    public function fillTestDataFields(): void
    {
        $faker = fake('de_DE');
        $country = $this->testDataCountry();
        $language = $this->testDataLanguage();

        if ($country === null || $language === null) {
            Flux::toast(
                heading: __('Test data could not be filled'),
                text: __('Please seed countries and languages before generating form test data.'),
                variant: 'warning',
                duration: 5000,
            );

            return;
        }

        $gender = $faker->randomElement(array_keys($this->genderOptions));
        $firstName = match ($gender) {
            'female' => $faker->firstNameFemale(),
            'male' => $faker->firstNameMale(),
            default => $faker->firstName(),
        };
        $lastName = $faker->lastName();
        $addressReference = $this->testDataAddressReference($country);
        $birthPlace = $this->testDataBirthPlace($country) ?: ($addressReference['city'] ?? $faker->city());
        $emailToken = Str::lower(Str::random(10));

        $this->isTestData = true;
        $this->salutation = match ($gender) {
            'female' => 'mrs',
            'male' => 'mr',
            default => 'mx',
        };
        $this->gender = $gender;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->maritalStatus = $faker->randomElement(array_keys($this->maritalStatusOptions));
        $this->birthCountryId = $country->id;
        $this->birthPlaceText = $birthPlace;
        $this->dateOfBirth = $faker->dateTimeBetween('-82 years', '-18 years')->format('Y-m-d');
        $this->email = "test.manual.{$emailToken}@example.test";

        $this->addressCountryId = $country->id;
        $this->addressPostalCode = $addressReference['postal_code'] ?? $faker->postcode();
        $this->addressCity = $addressReference['city'] ?? $faker->city();
        $this->addressStreet = $addressReference['street'] ?? $faker->streetName();
        $this->addressHouseNumber = (string) $faker->numberBetween(1, 220);

        $this->primaryNationalityCountryId = [$country->id];
        $this->primaryLanguageId = [$language->id];
        $this->syncLanguageAbilities();

        $this->nationalIdNumber = 'TEST-ID-'.Str::upper(Str::random(10));

        $this->nameTitle = $faker->boolean(12) ? 'Dr.' : '';
        $this->middleName = $faker->boolean(35) ? $faker->firstName() : '';
        $this->preferredName = $faker->boolean(25) ? $firstName : '';
        $this->birthName = $faker->boolean(20) ? $faker->lastName() : '';
        $this->phone = $faker->boolean(55) ? $faker->phoneNumber() : '';
        $this->mobile = $faker->boolean(75) ? $faker->phoneNumber() : '';
        $this->emailPrivate = $faker->boolean(65) ? "private.{$emailToken}@example.test" : '';
        $this->emailWork = $faker->boolean(35) ? "work.{$emailToken}@example.test" : '';
        $this->addressLine2 = $faker->boolean(20) ? __('Test address note') : '';
        $this->nationalIdIssuingAuthority = $faker->boolean(60) ? __('Test authority') : '';
        $this->taxId = $faker->boolean(35) ? 'TEST-TAX-'.Str::upper(Str::random(8)) : '';
        $this->socialSecurityNumber = $faker->boolean(35) ? 'TEST-SSN-'.Str::upper(Str::random(8)) : '';
        $this->pensionInsuranceNumber = $faker->boolean(25) ? 'TEST-PEN-'.Str::upper(Str::random(8)) : '';
        $this->residencePermitNumber = $faker->boolean(20) ? 'TEST-RP-'.Str::upper(Str::random(8)) : '';

        $this->fillOptionalHealthInsuranceTestData($emailToken);
        $this->fillOptionalDocumentTestData();
        $this->fillOptionalEmergencyContactTestData($faker);

        $this->resetValidation();
    }

    private function validationFieldMeta(): array
    {
        return [
            'salutation' => [
                'label' => __('Salutation'),
                'input_id' => 'create-person-salutation',
            ],
            'isTestData' => [
                'label' => __('Test data'),
                'input_id' => 'create-person-is-test-data',
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

    /**
     * Create person, linked user and initial related entities in one transaction.
     */
    public function create(GeneratedPasswordLogger $passwordLogger, ManagementActivity $managementActivity): void
    {
        $this->resetCreatedState();

        $validated = $this->validateForCreate();

        $plainPassword = Str::password(16);
        $userName = $this->buildUserName($validated['firstName'], $validated['lastName']);

        $result = DB::transaction(function () use ($validated, $plainPassword, $userName): array {
            $person = Person::query()->create([
                'person_number' => $this->buildUniquePersonNumber(),
                'is_test_data' => (bool) ($validated['isTestData'] ?? false),
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

            if (filled($validated['primaryNationalityCountryId'] ?? [])) {
                $this->createInitialNationality($person, $validated);
            }

            if (filled($validated['primaryLanguageId'] ?? [])) {
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
            createdByUser: Auth::user() instanceof User ? Auth::user() : null,
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

        $this->resetValidation();

        Flux::toast(
            heading: __('Person created'),
            text: __('The person and login account have been created.'),
            variant: 'success',
            duration: 4000,
        );
    }

    /**
     * Reset form input, created-state indicators and validation errors.
     */
    public function resetForm(): void
    {
        $this->resetFormState();
        $this->resetCreatedState();
        $this->resetValidation();
    }

    private function resetFormState(): void
    {
        $this->activeFormTab = 'person';
        $this->isTestData = false;
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
        $this->primaryNationalityCountryId = [];
        $this->primaryLanguageId = [];
        $this->languageAbilities = [];
        $this->nationalIdNumber = '';
        $this->nationalIdIssuingAuthority = '';
        $this->taxId = '';
        $this->socialSecurityNumber = '';
        $this->pensionInsuranceNumber = '';
        $this->healthInsuranceNumber = '';
        $this->healthInsuranceProviderId = null;
        $this->residencePermitNumber = '';
        $this->documentType = '';
        $this->documentTitle = '';
        $this->documentNumber = '';
        $this->documentIssuingAuthority = '';
        $this->documentIssuedAt = null;
        $this->documentExpiresAt = null;
        $this->documentUpload = [];
        $this->emergencyContactName = '';
        $this->emergencyContactRelationship = '';
        $this->emergencyContactPhone = '';
        $this->emergencyContactEmail = '';
        $this->email = '';
    }

    /**
     * Clear only the displayed generated password value.
     */
    public function clearGeneratedPassword(): void
    {
        $this->generatedPassword = '';
    }

    /**
     * Determine whether a field is configured as required in the form registry.
     */
    public function isRequiredField(string $field): bool
    {
        return app(FormFieldRegistry::class)->isRequired(self::FORM_KEY, $field);
    }

    /**
     * Compute completion/error status for a form tab based on configured fields.
     *
     * @return array{total:int, filled:int, field_total:int, field_filled:int, optional_total:int, optional_filled:int, required_total:int, required_filled:int, has_errors:bool, status:string}
     */
    public function formTabStatus(string $tab): array
    {
        $registry = app(FormFieldRegistry::class);
        $fields = $registry->statusRelevantFieldsForTab(self::FORM_KEY, $tab);

        $total = count($fields);
        $filled = 0;
        $optionalTotal = 0;
        $optionalFilled = 0;
        $requiredTotal = 0;
        $requiredFilled = 0;
        $hasErrors = false;

        foreach ($fields as $field => $meta) {
            $isRequired = (bool) ($meta['required'] ?? false);
            $isFilled = $this->isFormFieldFilled($field);

            if ($isFilled) {
                $filled++;
            }

            if ($isRequired) {
                $requiredTotal++;

                if ($isFilled) {
                    $requiredFilled++;
                }
            } else {
                $optionalTotal++;

                if ($isFilled) {
                    $optionalFilled++;
                }
            }

            if ($this->getErrorBag()->has($field)) {
                $hasErrors = true;
            }
        }

        return [
            'total' => $total,
            'filled' => $filled,
            'field_total' => $total,
            'field_filled' => $filled,
            'optional_total' => $optionalTotal,
            'optional_filled' => $optionalFilled,
            'required_total' => $requiredTotal,
            'required_filled' => $requiredFilled,
            'has_errors' => $hasErrors,
            'status' => $this->resolveFormTabStatus(
                total: $total,
                filled: $filled,
                requiredTotal: $requiredTotal,
                requiredFilled: $requiredFilled,
                hasErrors: $hasErrors,
            ),
        ];
    }

    private function resolveFormTabStatus(
        int $total,
        int $filled,
        int $requiredTotal,
        int $requiredFilled,
        bool $hasErrors,
    ): string {
        if ($hasErrors) {
            return 'error';
        }

        if ($requiredFilled < $requiredTotal) {
            return 'missing-required';
        }

        if ($total > 0 && $filled === $total) {
            return 'complete';
        }

        if ($filled > 0) {
            return 'partial';
        }

        return 'empty';
    }

    private function isFormFieldFilled(string $field): bool
    {
        if (! property_exists($this, $field)) {
            return false;
        }

        $value = $this->{$field};

        if (is_string($value)) {
            return trim($value) !== '';
        }

        if (is_array($value)) {
            return $value !== [];
        }

        return $value !== null;
    }

    /**
     * Dispatch focus event for a validation field based on registry metadata.
     */
    public function focusValidationField(string $field): void
    {
        $meta = $this->validationFieldMeta()[$field] ?? null;

        if (! is_array($meta) || ! isset($meta['input_id'])) {
            return;
        }

        $tab = $this->formTabForField($field);

        if ($tab !== null) {
            $this->activeFormTab = $tab;
        }

        $this->dispatch('buergerfrs:focus-field', inputId: $meta['input_id'], tab: $tab);
    }

    /**
     * Render create-person form with select options.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('components.management.people.⚡create-person', [
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
            'documentIssuingAuthorityOptions' => $this->documentIssuingAuthorityOptions(),
            'healthInsuranceProviderOptions' => InsuranceProvider::query()
                ->active()
                ->where('type', InsuranceProvider::TYPE_HEALTH)
                ->ordered()
                ->get(['id', 'name', 'short_name', 'code']),
        ]);
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

        return $this->includeCurrentAddressOption($options, $this->birthPlaceText);
    }

    private function testDataCountry(): ?Country
    {
        return Country::query()
            ->active()
            ->where('iso2', 'DE')
            ->first()
            ?: Country::query()->active()->ordered()->first();
    }

    private function testDataLanguage(): ?Language
    {
        return Language::query()
            ->active()
            ->where('iso639_1', 'de')
            ->first()
            ?: Language::query()->active()->ordered()->first();
    }

    /**
     * @return array{postal_code: string, city: string, street: string}|array{}
     */
    private function testDataAddressReference(Country $country): array
    {
        $street = AddressStreet::query()
            ->with(['postalCode:id,postal_code', 'locality:id,name'])
            ->where('country_id', $country->id)
            ->inRandomOrder()
            ->first();

        if ($street !== null && $street->postalCode !== null && $street->locality !== null) {
            return [
                'postal_code' => $street->postalCode->postal_code,
                'city' => $street->locality->name,
                'street' => $street->name,
            ];
        }

        $locality = AddressLocality::query()
            ->with('postalCode:id,postal_code')
            ->where('country_id', $country->id)
            ->inRandomOrder()
            ->first();

        if ($locality !== null) {
            return [
                'postal_code' => $locality->postalCode?->postal_code ?? fake('de_DE')->postcode(),
                'city' => $locality->name,
                'street' => fake('de_DE')->streetName(),
            ];
        }

        return [];
    }

    private function testDataBirthPlace(Country $country): ?string
    {
        return AddressLocality::query()
            ->where('country_id', $country->id)
            ->inRandomOrder()
            ->value('name');
    }

    private function fillOptionalHealthInsuranceTestData(string $token): void
    {
        $provider = InsuranceProvider::query()
            ->active()
            ->where('type', InsuranceProvider::TYPE_HEALTH)
            ->inRandomOrder()
            ->first();

        if ($provider !== null && fake('de_DE')->boolean(35)) {
            $this->healthInsuranceProviderId = $provider->id;
            $this->healthInsuranceNumber = 'TEST-HI-'.Str::upper(Str::substr($token, 0, 8));

            return;
        }

        $this->healthInsuranceProviderId = null;
        $this->healthInsuranceNumber = '';
    }

    private function fillOptionalDocumentTestData(): void
    {
        if (! fake('de_DE')->boolean(40)) {
            $this->documentType = '';
            $this->documentTitle = '';
            $this->documentNumber = '';
            $this->documentIssuingAuthority = '';
            $this->documentIssuedAt = null;
            $this->documentExpiresAt = null;

            return;
        }

        $issuedAt = now()->subDays(fake('de_DE')->numberBetween(30, 2500));

        $this->documentType = fake('de_DE')->randomElement($this->documentTypeOptionKeys());
        $this->documentTitle = __('Test document');
        $this->documentNumber = 'TEST-DOC-'.Str::upper(Str::random(8));
        $this->documentIssuingAuthority = __('Test authority');
        $this->documentIssuedAt = $issuedAt->toDateString();
        $this->documentExpiresAt = $issuedAt->copy()->addYears(fake('de_DE')->numberBetween(2, 10))->toDateString();
    }

    private function fillOptionalEmergencyContactTestData(\Faker\Generator $faker): void
    {
        if (! $faker->boolean(45)) {
            $this->emergencyContactName = '';
            $this->emergencyContactRelationship = '';
            $this->emergencyContactPhone = '';
            $this->emergencyContactEmail = '';

            return;
        }

        $this->emergencyContactName = $faker->name();
        $this->emergencyContactRelationship = $faker->randomElement(array_keys($this->emergencyContactRelationshipOptions));
        $this->emergencyContactPhone = $faker->phoneNumber();
        $this->emergencyContactEmail = 'emergency.'.Str::lower(Str::random(10)).'@example.test';
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

    private function documentIssuingAuthorityOptions()
    {
        $options = PersonDocument::query()
            ->whereNotNull('issuing_authority')
            ->where('issuing_authority', '!=', '')
            ->distinct()
            ->orderBy('issuing_authority')
            ->limit(100)
            ->pluck('issuing_authority');

        return $this->includeCurrentAddressOption($options, $this->documentIssuingAuthority);
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

    /**
     * Reset post-create state fields used for UI feedback.
     */
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
     *     documentUpload: array<int, mixed>,
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
        $fieldKeys = $this->orderedValidationErrorFields($errors->keys());

        $firstErrorTab = $this->formTabForField((string) $fieldKeys->first());

        if ($firstErrorTab !== null) {
            $this->activeFormTab = $firstErrorTab;
        }

        $validationErrors = $fieldKeys
            ->map(function (string $field) use ($errors, $fieldMeta): array {
                $meta = $fieldMeta[$field] ?? [
                    'label' => $field,
                    'input_id' => null,
                ];
                $tab = $this->formTabForField($field);

                return [
                    'field' => $field,
                    'label' => $meta['label'],
                    'inputId' => $meta['input_id'],
                    'tab' => $tab,
                    'messages' => collect($errors->get($field))->unique()->values()->all(),
                ];
            })
            ->values();

        $this->dispatch('buergerfrs:validation-errors', errors: $validationErrors->all());
    }

    /**
     * @param  array<int, string>  $fields
     * @return \Illuminate\Support\Collection<int, string>
     */
    private function orderedValidationErrorFields(array $fields)
    {
        $fieldOrder = array_flip(array_keys(app(FormFieldRegistry::class)->fields(self::FORM_KEY)));

        return collect($fields)
            ->unique()
            ->sortBy(function (string $field) use ($fieldOrder): int {
                $rootField = Str::before($field, '.');

                return $fieldOrder[$rootField] ?? PHP_INT_MAX;
            })
            ->values();
    }

    private function formTabForField(string $field): ?string
    {
        return app(FormFieldRegistry::class)->tab(self::FORM_KEY, $field);
    }

    private function validationFieldForProperty(string $propertyName): ?string
    {
        $rules = $this->validationRules();

        if (array_key_exists($propertyName, $rules)) {
            return $propertyName;
        }

        $rootPropertyName = Str::before($propertyName, '.');

        if (array_key_exists($rootPropertyName, $rules)) {
            return $rootPropertyName;
        }

        return null;
    }

    private function validateFieldIfItHasError(string $field): void
    {
        if (! $this->getErrorBag()->has($field)) {
            return;
        }

        try {
            $this->validateOnly($field, $this->validationRules());
        } catch (ValidationException) {
            // Keep the existing field error until the changed value is valid.
        }
    }

    private function validationRules(): array
    {
        return [
            'salutation' => $this->validationRulesFor('salutation', ['string', Rule::in(array_keys($this->salutationOptions))]),
            'isTestData' => ['boolean'],
            'nameTitle' => $this->validationRulesFor('nameTitle', ['string', 'max:255']),
            'gender' => $this->validationRulesFor('gender', ['string', Rule::in(array_keys($this->genderOptions))]),
            'maritalStatus' => $this->validationRulesFor('maritalStatus', ['string', Rule::in(array_keys($this->maritalStatusOptions))]),
            'firstName' => $this->validationRulesFor('firstName', ['string', 'max:255']),
            'middleName' => $this->validationRulesFor('middleName', ['string', 'max:255']),
            'preferredName' => $this->validationRulesFor('preferredName', ['string', 'max:255']),
            'lastName' => $this->validationRulesFor('lastName', ['string', 'max:255']),
            'birthName' => $this->validationRulesFor('birthName', ['string', 'max:255']),
            'dateOfBirth' => $this->validationRulesFor('dateOfBirth', ['date']),
            'avatarUpload' => $this->validationRulesFor('avatarUpload', ['file', 'image', 'max:4096', 'mimes:jpg,jpeg,png,webp']),
            'birthCountryId' => $this->validationRulesFor('birthCountryId', ['integer', Rule::exists('countries', 'id')]),
            'birthPlaceText' => $this->validationRulesFor('birthPlaceText', ['string', 'max:255']),
            'phone' => $this->validationRulesFor('phone', ['string', 'max:255']),
            'mobile' => $this->validationRulesFor('mobile', ['string', 'max:255']),
            'emailPrivate' => $this->validationRulesFor('emailPrivate', ['email', 'max:255']),
            'emailWork' => $this->validationRulesFor('emailWork', ['email', 'max:255']),
            'email' => $this->validationRulesFor('email', ['email', 'max:255', Rule::unique('users', 'email')]),
            'addressCountryId' => $this->validationRulesFor('addressCountryId', ['integer', Rule::exists('countries', 'id')]),
            'addressPostalCode' => $this->validationRulesFor('addressPostalCode', ['string', 'max:255']),
            'addressCity' => $this->validationRulesFor('addressCity', ['string', 'max:255']),
            'addressStreet' => $this->validationRulesFor('addressStreet', ['string', 'max:255']),
            'addressHouseNumber' => $this->validationRulesFor('addressHouseNumber', ['string', 'max:255']),
            'addressLine2' => $this->validationRulesFor('addressLine2', ['string', 'max:255']),
            'primaryNationalityCountryId' => $this->validationRulesFor('primaryNationalityCountryId', ['array']),
            'primaryNationalityCountryId.*' => ['integer', Rule::exists('countries', 'id')],
            'primaryLanguageId' => $this->validationRulesFor('primaryLanguageId', ['array']),
            'primaryLanguageId.*' => ['integer', Rule::exists('languages', 'id')],
            'languageAbilities' => ['array'],
            'languageAbilities.*.speaking' => ['boolean'],
            'languageAbilities.*.reading' => ['boolean'],
            'languageAbilities.*.writing' => ['boolean'],
            'nationalIdNumber' => $this->validationRulesFor('nationalIdNumber', ['string', 'max:255']),
            'nationalIdIssuingAuthority' => $this->validationRulesFor('nationalIdIssuingAuthority', ['string', 'max:255']),
            'taxId' => $this->validationRulesFor('taxId', ['string', 'max:255']),
            'socialSecurityNumber' => $this->validationRulesFor('socialSecurityNumber', ['string', 'max:255']),
            'pensionInsuranceNumber' => $this->validationRulesFor('pensionInsuranceNumber', ['string', 'max:255']),
            'healthInsuranceNumber' => $this->validationRulesFor('healthInsuranceNumber', ['string', 'max:255']),
            'healthInsuranceProviderId' => $this->validationRulesFor('healthInsuranceProviderId', [
                'integer',
                Rule::exists('insurance_providers', 'id')
                    ->where('type', InsuranceProvider::TYPE_HEALTH)
                    ->where('is_active', true),
            ]),
            'residencePermitNumber' => $this->validationRulesFor('residencePermitNumber', ['string', 'max:255']),
            'documentType' => $this->validationRulesFor('documentType', ['string', Rule::in($this->documentTypeOptionKeys())]),
            'documentTitle' => $this->validationRulesFor('documentTitle', ['string', 'max:255']),
            'documentNumber' => $this->validationRulesFor('documentNumber', ['string', 'max:255']),
            'documentIssuingAuthority' => $this->validationRulesFor('documentIssuingAuthority', ['string', 'max:255']),
            'documentIssuedAt' => $this->validationRulesFor('documentIssuedAt', ['date']),
            'documentExpiresAt' => $this->validationRulesFor('documentExpiresAt', ['date', 'after_or_equal:documentIssuedAt']),
            'documentUpload' => $this->validationRulesFor('documentUpload', ['array']),
            'documentUpload.*' => ['file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,webp'],
            'emergencyContactName' => $this->validationRulesFor('emergencyContactName', ['string', 'max:255']),
            'emergencyContactRelationship' => $this->validationRulesFor('emergencyContactRelationship', ['string', Rule::in(array_keys($this->emergencyContactRelationshipOptions))]),
            'emergencyContactPhone' => $this->validationRulesFor('emergencyContactPhone', ['string', 'max:255']),
            'emergencyContactEmail' => $this->validationRulesFor('emergencyContactEmail', ['email', 'max:255']),
        ];
    }

    /**
     * @param  array<string, mixed>  $validated
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
     * @param  array<string, mixed>  $validated
     */
    private function createInitialAddress(Person $person, array $validated): void
    {
        $countryId = $validated['addressCountryId'] ?: null;
        $postalCodeValue = $validated['addressPostalCode'] ?: null;
        $cityValue = $validated['addressCity'] ?: null;
        $streetValue = $validated['addressStreet'] ?: null;

        $postalCode = $this->firstOrCreateAddressPostalCode($countryId, $postalCodeValue);
        $locality = $this->firstOrCreateAddressLocality($countryId, $postalCode, $cityValue);
        $street = $this->firstOrCreateAddressStreet($countryId, $postalCode, $locality, $streetValue);

        $address = Address::query()->firstOrCreate([
            'country_id' => $countryId,
            'postal_code_id' => $postalCode?->id,
            'locality_id' => $locality?->id,
            'street_id' => $street?->id,
            'postal_code' => $postalCodeValue,
            'city' => $cityValue,
            'street' => $streetValue,
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

    /**
     * @param  array<string, mixed>  $validated
     */
    private function createInitialNationality(Person $person, array $validated): void
    {
        foreach ($this->normalizeSelectedIds($validated['primaryNationalityCountryId'] ?? []) as $index => $countryId) {
            PersonNationality::query()->create([
                'person_id' => $person->id,
                'country_id' => $countryId,
                'is_primary' => $index === 0,
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function createInitialLanguage(Person $person, array $validated): void
    {
        foreach ($this->normalizeSelectedIds($validated['primaryLanguageId'] ?? []) as $index => $languageId) {
            $abilities = $validated['languageAbilities'][$languageId] ?? [];

            PersonLanguage::query()->create([
                'person_id' => $person->id,
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
    }

    /**
     * @param  array<string, mixed>  $validated
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
     * @param  array<string, mixed>  $validated
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
     * @param  array<string, mixed>  $validated
     */
    private function hasHealthInsuranceInput(array $validated): bool
    {
        return filled($validated['healthInsuranceProviderId'] ?? null)
            || filled($validated['healthInsuranceNumber'] ?? null);
    }

    /**
     * @param  array<string, mixed>  $validated
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
     * @param  array<string, mixed>  $validated
     */
    private function createInitialDocument(Person $person, array $validated): void
    {
        if (! $this->hasDocumentMetadataInput($validated)) {
            return;
        }

        $uploads = collect($validated['documentUpload'] ?? [])
            ->filter(fn(mixed $upload): bool => $upload instanceof TemporaryUploadedFile)
            ->values();

        $baseAttributes = [
            'person_id' => $person->id,
            'type' => $validated['documentType'] ?: PersonDocument::TYPE_OTHER,
            'status' => PersonDocument::STATUS_ACTIVE,
            'category' => $this->documentCategoryForType($validated['documentType'] ?: PersonDocument::TYPE_OTHER),
            'source' => $uploads->isEmpty() ? PersonDocument::SOURCE_MANUAL : PersonDocument::SOURCE_UPLOAD,
            'direction' => PersonDocument::DIRECTION_NONE,
            'title' => $validated['documentTitle'] ?: null,
            'document_number' => $validated['documentNumber'] ?: null,
            'issuing_authority' => $validated['documentIssuingAuthority'] ?: null,
            'issued_at' => $validated['documentIssuedAt'] ?: null,
            'expires_at' => $validated['documentExpiresAt'] ?: null,
            'valid_from' => $validated['documentIssuedAt'] ?: null,
            'valid_until' => $validated['documentExpiresAt'] ?: null,
            'is_current' => true,
        ];

        if ($uploads->isEmpty()) {
            PersonDocument::query()->create($baseAttributes);

            return;
        }

        $uploads->each(function (TemporaryUploadedFile $upload, int $index) use ($person, $baseAttributes): void {
            $uploadAttributes = $this->storeInitialDocumentUpload(
                person: $person,
                upload: $upload,
            );

            $document = PersonDocument::query()->create(array_merge($baseAttributes, [
                'title' => $index === 0
                    ? $baseAttributes['title']
                    : $this->documentTitleForAdditionalUpload($baseAttributes['title'], $upload, $index),
            ], $uploadAttributes));

            if ($index === 0 && filled($document->file_path)) {
                $this->createdDocumentId = $document->id;
            }
        });
    }

    private function documentTitleForAdditionalUpload(?string $baseTitle, TemporaryUploadedFile $upload, int $index): ?string
    {
        if (filled($baseTitle)) {
            return "{$baseTitle} #" . ($index + 1);
        }

        return $upload->getClientOriginalName();
    }

    private function documentCategoryForType(string $type): string
    {
        return PersonDocumentType::categoryFor($type);
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

    /**
     * @param  array<string, mixed>  $validated
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
     * @param  array<string, mixed>  $validated
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

    /**
     * Build validation rules from form metadata.
     *
     * Required fields always receive their domain rules. Optional fields only
     * receive those rules once the user has actually entered a value, so empty
     * optional fields do not fail rules like email/date/integer/file.
     *
     * @param  array<int, mixed>  $rules
     * @return array<int, mixed>
     */
    private function validationRulesFor(string $field, array $rules): array
    {
        if ($this->isRequiredField($field)) {
            return array_merge(['required'], $rules);
        }

        if (! $this->isFormFieldFilled($field)) {
            return ['nullable'];
        }

        return array_merge(['nullable'], $rules);
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
