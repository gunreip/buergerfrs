<?php

use App\Livewire\Management\People\EditPerson;
use App\Models\Address;
use App\Models\AddressLocality;
use App\Models\Country;
use App\Models\InsuranceProvider;
use App\Models\Language;
use App\Models\Person;
use App\Models\PersonAddress;
use App\Models\PersonContact;
use App\Models\PersonDocument;
use App\Models\PersonHealthInsurance;
use App\Models\PersonIdentifier;
use App\Models\PersonLanguage;
use App\Models\PersonNationality;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('authenticated users can open the edit person page', function (): void {
    $user = User::factory()->create();
    $person = Person::query()->create([
        'person_number' => 'P-EDIT-001',
        'first_name' => 'Old',
        'last_name' => 'Name',
    ]);

    $this->actingAs($user)
        ->get(route('management.people.edit', $person))
        ->assertOk()
        ->assertSeeLivewire(EditPerson::class);
});

test('edit person form is prefilled from the person record', function (): void {
    $user = User::factory()->create();
    $country = Country::query()->create([
        'iso2' => 'DE',
        'iso3' => 'DEU',
        'name' => 'Germany',
        'native_name' => 'Deutschland',
        'is_active' => true,
    ]);
    $person = Person::query()->create([
        'person_number' => 'P-EDIT-002',
        'is_test_data' => true,
        'salutation' => 'mrs',
        'gender' => 'female',
        'marital_status' => 'single',
        'first_name' => 'Anna',
        'last_name' => 'Muster',
        'date_of_birth' => '1988-04-12',
        'birth_country_id' => $country->id,
        'birth_place_text' => 'Berlin',
        'email_private' => 'anna@example.test',
    ]);

    Livewire::actingAs($user)
        ->test(EditPerson::class, ['person' => $person])
        ->assertSet('salutation', 'mrs')
        ->assertSet('gender', 'female')
        ->assertSet('firstName', 'Anna')
        ->assertSet('lastName', 'Muster')
        ->assertSet('birthCountryId', $country->id)
        ->assertSet('birthPlaceText', 'Berlin')
        ->assertSet('emailPrivate', 'anna@example.test');
});

test('edit person field button closes an unchanged field', function (): void {
    $user = User::factory()->create();
    $person = Person::query()->create([
        'person_number' => 'P-EDIT-FIELD-001',
        'first_name' => 'Anna',
        'last_name' => 'Muster',
    ]);

    Livewire::actingAs($user)
        ->test(EditPerson::class, ['person' => $person])
        ->call('editField', 'firstName')
        ->assertSet('editingField', 'firstName')
        ->call('editField', 'firstName')
        ->assertSet('editingField', null)
        ->assertSet('editingFieldInitialValue', null);
});

test('edit person field button saves a newly filled empty value when closing the field', function (): void {
    $user = User::factory()->create();
    $person = Person::query()->create([
        'person_number' => 'P-EDIT-FIELD-002',
        'first_name' => 'Anna',
        'last_name' => 'Muster',
        'middle_name' => null,
    ]);

    Livewire::actingAs($user)
        ->test(EditPerson::class, ['person' => $person])
        ->call('editField', 'middleName')
        ->set('middleName', 'Maria')
        ->call('editField', 'middleName')
        ->assertHasNoErrors()
        ->assertSet('editingField', null);

    expect($person->refresh()->middle_name)->toBe('Maria');
});

test('edit person field button saves a changed existing value when closing the field', function (): void {
    $user = User::factory()->create();
    $person = Person::query()->create([
        'person_number' => 'P-EDIT-FIELD-003',
        'first_name' => 'Anna',
        'last_name' => 'Muster',
    ]);

    Livewire::actingAs($user)
        ->test(EditPerson::class, ['person' => $person])
        ->call('editField', 'firstName')
        ->set('firstName', 'Anne')
        ->call('editField', 'firstName')
        ->assertHasNoErrors()
        ->assertSet('editingField', null);

    expect($person->refresh()->first_name)->toBe('Anne');
});

test('edit person field button closes an unchanged field before opening another field', function (): void {
    $user = User::factory()->create();
    $person = Person::query()->create([
        'person_number' => 'P-EDIT-FIELD-004',
        'first_name' => 'Anna',
        'last_name' => 'Muster',
    ]);

    Livewire::actingAs($user)
        ->test(EditPerson::class, ['person' => $person])
        ->call('editField', 'firstName')
        ->call('editField', 'lastName')
        ->assertSet('editingField', 'lastName')
        ->assertSet('editingFieldInitialValue', 'Muster');
});

test('edit person field button dispatches focus event for the selected field', function (): void {
    $user = User::factory()->create();
    $person = Person::query()->create([
        'person_number' => 'P-EDIT-FIELD-005',
        'first_name' => 'Anna',
        'last_name' => 'Muster',
    ]);

    Livewire::actingAs($user)
        ->test(EditPerson::class, ['person' => $person])
        ->call('editField', 'firstName')
        ->assertDispatched('buergerfrs:focus-field', function (string $event, array $params): bool {
            return ($params['inputId'] ?? null) === 'edit-person-first-name'
                && ($params['tab'] ?? null) === 'person';
        });
});

test('edit person field button dispatches focus event for select fields', function (): void {
    $user = User::factory()->create();
    $country = Country::query()->create([
        'iso2' => 'DE',
        'iso3' => 'DEU',
        'name' => 'Germany',
        'native_name' => 'Deutschland',
        'is_active' => true,
    ]);
    $person = Person::query()->create([
        'person_number' => 'P-EDIT-FIELD-006',
        'first_name' => 'Anna',
        'last_name' => 'Muster',
        'birth_country_id' => $country->id,
    ]);

    Livewire::actingAs($user)
        ->test(EditPerson::class, ['person' => $person])
        ->call('editField', 'birthCountryId')
        ->assertDispatched('buergerfrs:focus-field', function (string $event, array $params): bool {
            return ($params['inputId'] ?? null) === 'edit-person-birth-country'
                && ($params['tab'] ?? null) === 'person';
        });
});

test('edit person page shows the person avatar in the page header', function (): void {
    Storage::fake('public');

    $user = User::factory()->create();
    $avatarPath = 'avatars/test/person-avatar.jpg';

    Storage::disk('public')->put($avatarPath, 'avatar-image');

    $person = Person::query()->create([
        'person_number' => 'P-EDIT-AVATAR',
        'first_name' => 'Avatar',
        'last_name' => 'Person',
        'avatar_path' => $avatarPath,
    ]);

    $this->actingAs($user)
        ->get(route('management.people.edit', $person))
        ->assertOk()
        ->assertSee(Storage::disk('public')->url($avatarPath), false)
        ->assertSee('Avatar for Avatar Person');
});

test('edit person saves core and contact fields and logs activity', function (): void {
    $user = User::factory()->create();
    $country = Country::query()->create([
        'iso2' => 'DE',
        'iso3' => 'DEU',
        'name' => 'Germany',
        'native_name' => 'Deutschland',
        'is_active' => true,
    ]);
    $person = Person::query()->create([
        'person_number' => 'P-EDIT-003',
        'is_test_data' => false,
        'salutation' => 'mr',
        'gender' => 'male',
        'marital_status' => 'single',
        'first_name' => 'Old',
        'last_name' => 'Name',
        'date_of_birth' => '1980-01-01',
        'birth_country_id' => $country->id,
        'birth_place_text' => 'Berlin',
    ]);

    Livewire::actingAs($user)
        ->test(EditPerson::class, ['person' => $person])
        ->call('editField', 'firstName')
        ->set('firstName', 'New')
        ->call('save')
        ->call('editField', 'lastName')
        ->set('lastName', 'Person')
        ->call('save')
        ->call('editField', 'birthPlaceText')
        ->set('birthPlaceText', 'Hamburg')
        ->call('save')
        ->call('editField', 'emailPrivate')
        ->set('emailPrivate', 'new@example.test')
        ->call('save')
        ->assertHasNoErrors();

    $person->refresh();

    expect($person->is_test_data)->toBeFalse()
        ->and($person->first_name)->toBe('New')
        ->and($person->last_name)->toBe('Person')
        ->and($person->birth_place_text)->toBe('Hamburg')
        ->and($person->email_private)->toBe('new@example.test');

    $activity = DB::table('activity_log')
        ->where('event', 'management.person.updated')
        ->where('subject_id', $person->id)
        ->first();

    expect($activity)->not->toBeNull();
});

test('edit person can save contact fields without editing incomplete core data', function (): void {
    $user = User::factory()->create();
    $person = Person::query()->create([
        'person_number' => 'P-EDIT-CONTACT',
        'first_name' => 'Contact',
        'last_name' => 'Only',
    ]);

    Livewire::actingAs($user)
        ->test(EditPerson::class, ['person' => $person])
        ->call('editField', 'emailPrivate')
        ->set('emailPrivate', 'contact.only@example.test')
        ->call('save')
        ->assertHasNoErrors();

    $person->refresh();

    expect($person->email_private)->toBe('contact.only@example.test');
});

test('edit person loads and saves the primary address fields', function (): void {
    $user = User::factory()->create();
    $country = Country::query()->create([
        'iso2' => 'DE',
        'iso3' => 'DEU',
        'name' => 'Germany',
        'native_name' => 'Deutschland',
        'is_active' => true,
    ]);
    $person = Person::query()->create([
        'person_number' => 'P-EDIT-ADDRESS',
        'first_name' => 'Address',
        'last_name' => 'Person',
    ]);
    $address = Address::query()->create([
        'country_id' => $country->id,
        'postal_code' => '10115',
        'city' => 'Berlin',
        'street' => 'Invalidenstrasse',
        'house_number' => '12',
    ]);

    PersonAddress::query()->create([
        'person_id' => $person->id,
        'address_id' => $address->id,
        'type' => PersonAddress::TYPE_HOME,
        'is_primary' => true,
    ]);

    Livewire::actingAs($user)
        ->test(EditPerson::class, ['person' => $person])
        ->assertSet('addressCountryId', $country->id)
        ->assertSet('addressPostalCode', '10115')
        ->assertSet('addressCity', 'Berlin')
        ->assertSet('addressStreet', 'Invalidenstrasse')
        ->assertSet('addressHouseNumber', '12')
        ->call('editField', 'addressHouseNumber')
        ->set('addressHouseNumber', '14')
        ->call('save')
        ->assertHasNoErrors();

    $personAddress = PersonAddress::query()
        ->with('address')
        ->where('person_id', $person->id)
        ->where('type', PersonAddress::TYPE_HOME)
        ->first();

    expect($personAddress?->address?->house_number)->toBe('14');
});

test('edit person loads and saves international fields', function (): void {
    $user = User::factory()->create();
    $germany = Country::query()->create([
        'iso2' => 'DE',
        'iso3' => 'DEU',
        'name' => 'Germany',
        'native_name' => 'Deutschland',
        'is_active' => true,
    ]);
    $france = Country::query()->create([
        'iso2' => 'FR',
        'iso3' => 'FRA',
        'name' => 'France',
        'native_name' => 'France',
        'is_active' => true,
    ]);
    $german = Language::query()->create([
        'iso639_1' => 'de',
        'iso639_3' => 'deu',
        'name' => 'German',
        'native_name' => 'Deutsch',
        'is_active' => true,
    ]);
    $french = Language::query()->create([
        'iso639_1' => 'fr',
        'iso639_3' => 'fra',
        'name' => 'French',
        'native_name' => 'Français',
        'is_active' => true,
    ]);
    $person = Person::query()->create([
        'person_number' => 'P-EDIT-INTERNATIONAL',
        'first_name' => 'International',
        'last_name' => 'Person',
    ]);

    PersonNationality::query()->create([
        'person_id' => $person->id,
        'country_id' => $germany->id,
        'is_primary' => true,
    ]);
    PersonLanguage::query()->create([
        'person_id' => $person->id,
        'language_id' => $german->id,
        'proficiency' => PersonLanguage::PROFICIENCY_UNKNOWN,
        'is_native' => false,
        'is_primary' => true,
        'preferred_for_communication' => true,
        'can_speak' => true,
        'can_read' => true,
        'can_write' => false,
    ]);

    Livewire::actingAs($user)
        ->test(EditPerson::class, ['person' => $person])
        ->assertSet('primaryNationalityCountryId', [$germany->id])
        ->assertSet('primaryLanguageId', [$german->id])
        ->assertSet("languageAbilities.{$german->id}.speaking", true)
        ->assertSet("languageAbilities.{$german->id}.reading", true)
        ->assertSet("languageAbilities.{$german->id}.writing", false)
        ->call('editField', 'primaryNationalityCountryId')
        ->set('primaryNationalityCountryId', [$france->id, $germany->id])
        ->call('save')
        ->call('editField', 'primaryLanguageId')
        ->set('primaryLanguageId', [$french->id])
        ->set("languageAbilities.{$french->id}.speaking", true)
        ->set("languageAbilities.{$french->id}.reading", false)
        ->set("languageAbilities.{$french->id}.writing", true)
        ->call('save')
        ->assertHasNoErrors();

    $person->refresh();

    expect($person->nationalityRows()->orderByDesc('is_primary')->pluck('country_id')->all())
        ->toBe([$france->id, $germany->id])
        ->and($person->languageRows()->pluck('language_id')->all())
        ->toBe([$french->id])
        ->and($person->languageRows()->first()?->preferred_for_communication)
        ->toBeTrue()
        ->and($person->languageRows()->first()?->can_speak)
        ->toBeTrue()
        ->and($person->languageRows()->first()?->can_read)
        ->toBeFalse()
        ->and($person->languageRows()->first()?->can_write)
        ->toBeTrue();
});

test('edit person loads and saves identification fields', function (): void {
    $user = User::factory()->create();
    $person = Person::query()->create([
        'person_number' => 'P-EDIT-IDENTIFICATION',
        'first_name' => 'Identifier',
        'last_name' => 'Person',
    ]);

    PersonIdentifier::query()->create([
        'person_id' => $person->id,
        'type' => PersonIdentifier::TYPE_NATIONAL_ID,
        'value' => 'OLD-ID-123',
        'value_hash' => hash('sha256', 'OLD-ID-123'),
        'issuing_authority' => 'Old authority',
        'is_primary' => true,
    ]);
    PersonIdentifier::query()->create([
        'person_id' => $person->id,
        'type' => PersonIdentifier::TYPE_TAX_ID,
        'value' => 'OLD-TAX-123',
        'value_hash' => hash('sha256', 'OLD-TAX-123'),
    ]);

    Livewire::actingAs($user)
        ->test(EditPerson::class, ['person' => $person])
        ->assertSet('identifierNationalIdNumber', 'OLD-ID-123')
        ->assertSet('identifierNationalIdIssuingAuthority', 'Old authority')
        ->assertSet('identifierTaxId', 'OLD-TAX-123')
        ->call('editField', 'identifierNationalIdNumber')
        ->set('identifierNationalIdNumber', 'NEW-ID-456')
        ->set('identifierNationalIdIssuingAuthority', 'New authority')
        ->call('save')
        ->call('editField', 'identifierResidencePermitNumber')
        ->set('identifierResidencePermitNumber', 'RP-987')
        ->call('save')
        ->assertHasNoErrors();

    $nationalId = PersonIdentifier::query()
        ->where('person_id', $person->id)
        ->where('type', PersonIdentifier::TYPE_NATIONAL_ID)
        ->first();
    $residencePermit = PersonIdentifier::query()
        ->where('person_id', $person->id)
        ->where('type', PersonIdentifier::TYPE_RESIDENCE_PERMIT_NUMBER)
        ->first();

    expect($nationalId?->value)->toBe('NEW-ID-456')
        ->and($nationalId?->value_hash)->toBe(hash('sha256', 'NEW-ID-456'))
        ->and($nationalId?->issuing_authority)->toBe('New authority')
        ->and($residencePermit?->value)->toBe('RP-987');
});

test('edit person loads and saves health insurance fields', function (): void {
    $user = User::factory()->create();
    $oldProvider = InsuranceProvider::query()->create([
        'type' => InsuranceProvider::TYPE_HEALTH,
        'name' => 'Old Health',
        'short_name' => 'OH',
        'code' => 'OLD',
        'is_active' => true,
    ]);
    $newProvider = InsuranceProvider::query()->create([
        'type' => InsuranceProvider::TYPE_HEALTH,
        'name' => 'New Health',
        'short_name' => 'NH',
        'code' => 'NEW',
        'is_active' => true,
    ]);
    $person = Person::query()->create([
        'person_number' => 'P-EDIT-HEALTH-INSURANCE',
        'first_name' => 'Health',
        'last_name' => 'Person',
    ]);

    PersonHealthInsurance::query()->create([
        'person_id' => $person->id,
        'insurance_provider_id' => $oldProvider->id,
        'insurance_number' => 'OLD-HI-123',
        'is_primary' => true,
    ]);

    Livewire::actingAs($user)
        ->test(EditPerson::class, ['person' => $person])
        ->assertSet('healthInsuranceProviderId', $oldProvider->id)
        ->assertSet('healthInsuranceNumber', 'OLD-HI-123')
        ->call('editField', 'healthInsuranceProviderId')
        ->set('healthInsuranceProviderId', $newProvider->id)
        ->set('healthInsuranceNumber', 'NEW-HI-456')
        ->call('save')
        ->assertHasNoErrors();

    $healthInsurance = PersonHealthInsurance::query()
        ->where('person_id', $person->id)
        ->first();
    $identifier = PersonIdentifier::query()
        ->where('person_id', $person->id)
        ->where('type', PersonIdentifier::TYPE_HEALTH_INSURANCE_NUMBER)
        ->first();

    expect($healthInsurance?->insurance_provider_id)->toBe($newProvider->id)
        ->and($healthInsurance?->insurance_number)->toBe('NEW-HI-456')
        ->and($identifier?->value)->toBe('NEW-HI-456')
        ->and($identifier?->value_hash)->toBe(hash('sha256', 'NEW-HI-456'));
});

test('edit person loads and saves document fields', function (): void {
    $user = User::factory()->create();
    $person = Person::query()->create([
        'person_number' => 'P-EDIT-DOCUMENT',
        'first_name' => 'Document',
        'last_name' => 'Person',
    ]);
    $document = PersonDocument::query()->create([
        'person_id' => $person->id,
        'type' => PersonDocument::TYPE_ID_CARD_COPY,
        'title' => 'Old document',
        'document_number' => 'OLD-DOC-123',
        'issuing_authority' => 'Old authority',
        'issued_at' => '2020-01-01',
        'expires_at' => '2030-01-01',
    ]);

    Livewire::actingAs($user)
        ->test(EditPerson::class, ['person' => $person])
        ->assertSet('documentId', $document->id)
        ->assertSet('documentType', PersonDocument::TYPE_ID_CARD_COPY)
        ->assertSet('documentTitle', 'Old document')
        ->assertSet('documentNumber', 'OLD-DOC-123')
        ->call('editField', 'documentTitle')
        ->set('documentTitle', 'New document')
        ->set('documentNumber', 'NEW-DOC-456')
        ->set('documentType', PersonDocument::TYPE_PASSPORT_COPY)
        ->call('save')
        ->assertHasNoErrors();

    $document->refresh();

    expect($document->title)->toBe('New document')
        ->and($document->document_number)->toBe('NEW-DOC-456')
        ->and($document->type)->toBe(PersonDocument::TYPE_PASSPORT_COPY);
});

test('edit person selected document field can be switched through field edit flow', function (): void {
    $user = User::factory()->create();
    $person = Person::query()->create([
        'person_number' => 'P-EDIT-DOCUMENT-SELECTION',
        'first_name' => 'Document',
        'last_name' => 'Selection',
    ]);
    $oldDocument = PersonDocument::query()->create([
        'person_id' => $person->id,
        'type' => PersonDocument::TYPE_ID_CARD_COPY,
        'title' => 'Old document',
        'document_number' => 'OLD-DOC-123',
    ]);
    $newDocument = PersonDocument::query()->create([
        'person_id' => $person->id,
        'type' => PersonDocument::TYPE_PASSPORT_COPY,
        'title' => 'New document',
        'document_number' => 'NEW-DOC-456',
    ]);

    Livewire::actingAs($user)
        ->test(EditPerson::class, ['person' => $person])
        ->assertSet('documentId', $newDocument->id)
        ->call('editField', 'documentId')
        ->assertSet('editingField', 'documentId')
        ->assertDispatched('buergerfrs:focus-field', function (string $event, array $params): bool {
            return ($params['inputId'] ?? null) === 'edit-person-document-selection'
                && ($params['tab'] ?? null) === 'documents';
        })
        ->set('documentId', $oldDocument->id)
        ->assertSet('editingField', null)
        ->assertSet('documentTitle', 'Old document')
        ->assertSet('documentNumber', 'OLD-DOC-123')
        ->assertSet('documentType', PersonDocument::TYPE_ID_CARD_COPY);
});

test('edit person document archive modal opens and selects a document', function (): void {
    $user = User::factory()->create();
    $person = Person::query()->create([
        'person_number' => 'P-EDIT-DOCUMENT-ARCHIVE',
        'first_name' => 'Document',
        'last_name' => 'Archive',
    ]);
    $oldDocument = PersonDocument::query()->create([
        'person_id' => $person->id,
        'type' => PersonDocument::TYPE_ID_CARD_COPY,
        'status' => PersonDocument::STATUS_REPLACED,
        'category' => PersonDocument::CATEGORY_IDENTITY,
        'source' => PersonDocument::SOURCE_UPLOAD,
        'direction' => PersonDocument::DIRECTION_NONE,
        'title' => 'Old archive document',
        'document_number' => 'OLD-ARCHIVE-123',
        'is_current' => false,
    ]);
    $currentDocument = PersonDocument::query()->create([
        'person_id' => $person->id,
        'type' => PersonDocument::TYPE_PASSPORT_COPY,
        'status' => PersonDocument::STATUS_ACTIVE,
        'category' => PersonDocument::CATEGORY_IDENTITY,
        'source' => PersonDocument::SOURCE_UPLOAD,
        'direction' => PersonDocument::DIRECTION_NONE,
        'title' => 'Current archive document',
        'document_number' => 'CURRENT-ARCHIVE-456',
        'is_current' => true,
    ]);

    Livewire::actingAs($user)
        ->test(EditPerson::class, ['person' => $person])
        ->call('openDocumentArchive')
        ->assertSet('documentArchiveModalOpen', true)
        ->assertSee('Current archive document')
        ->assertDontSee('Old archive document')
        ->set('documentArchiveFilter', 'all')
        ->assertSee('Old archive document')
        ->call('selectDocumentFromArchive', $oldDocument->id)
        ->assertSet('documentArchiveModalOpen', false)
        ->assertSet('documentId', $oldDocument->id)
        ->assertSet('documentTitle', 'Old archive document')
        ->assertSet('documentNumber', 'OLD-ARCHIVE-123')
        ->assertSet('activeFormTab', 'documents');

    expect($currentDocument->refresh()->id)->toBe($currentDocument->id);
});

test('edit person document archive filter shows correspondence documents', function (): void {
    $user = User::factory()->create();
    $person = Person::query()->create([
        'person_number' => 'P-EDIT-DOCUMENT-CORR',
        'first_name' => 'Document',
        'last_name' => 'Correspondence',
    ]);
    PersonDocument::query()->create([
        'person_id' => $person->id,
        'type' => PersonDocument::TYPE_PASSPORT_COPY,
        'status' => PersonDocument::STATUS_ACTIVE,
        'category' => PersonDocument::CATEGORY_IDENTITY,
        'source' => PersonDocument::SOURCE_UPLOAD,
        'direction' => PersonDocument::DIRECTION_NONE,
        'title' => 'Identity document',
        'is_current' => true,
    ]);
    PersonDocument::query()->create([
        'person_id' => $person->id,
        'type' => PersonDocument::TYPE_OTHER,
        'status' => PersonDocument::STATUS_ACTIVE,
        'category' => PersonDocument::CATEGORY_CORRESPONDENCE,
        'source' => PersonDocument::SOURCE_CORRESPONDENCE,
        'direction' => PersonDocument::DIRECTION_INCOMING,
        'title' => 'Correspondence attachment',
        'is_current' => true,
    ]);

    Livewire::actingAs($user)
        ->test(EditPerson::class, ['person' => $person])
        ->call('openDocumentArchive')
        ->set('documentArchiveFilter', 'correspondence')
        ->assertSee('Correspondence attachment');
});

test('edit person add document modal opens and resets upload state', function (): void {
    $user = User::factory()->create();
    $person = Person::query()->create([
        'person_number' => 'P-EDIT-DOCUMENT-ADD-MODAL',
        'first_name' => 'Document',
        'last_name' => 'AddModal',
    ]);

    Livewire::actingAs($user)
        ->test(EditPerson::class, ['person' => $person])
        ->set('newDocumentTitle', 'Transient title')
        ->call('openAddDocumentModal')
        ->assertSet('addDocumentModalOpen', true)
        ->assertSet('newDocumentTitle', '')
        ->call('closeAddDocumentModal')
        ->assertSet('addDocumentModalOpen', false);
});

test('edit person add document button calls the add document action directly', function (): void {
    $user = User::factory()->create();
    $person = Person::query()->create([
        'person_number' => 'P-EDIT-DOCUMENT-ADD-BUTTON',
        'first_name' => 'Document',
        'last_name' => 'AddButton',
    ]);

    $this->actingAs($user)
        ->get(route('management.people.edit', $person))
        ->assertOk()
        ->assertSee('wire:click="addDocument"', false);
});

test('edit person document archive button is disabled without documents', function (): void {
    $user = User::factory()->create();
    $person = Person::query()->create([
        'person_number' => 'P-EDIT-DOCUMENT-ARCHIVE-EMPTY',
        'first_name' => 'Document',
        'last_name' => 'ArchiveEmpty',
    ]);

    $this->actingAs($user)
        ->get(route('management.people.edit', $person))
        ->assertOk()
        ->assertSee('aria-label="Open document archive"', false)
        ->assertSee('disabled', false)
        ->assertDontSee('aria-label="Images:', false)
        ->assertDontSee('aria-label="Documents:', false);
});

test('edit person document archive button shows document and image counters', function (): void {
    $user = User::factory()->create();
    $person = Person::query()->create([
        'person_number' => 'P-EDIT-DOCUMENT-ARCHIVE-COUNTERS',
        'first_name' => 'Document',
        'last_name' => 'ArchiveCounters',
    ]);
    PersonDocument::query()->create([
        'person_id' => $person->id,
        'type' => PersonDocument::TYPE_OTHER,
        'status' => PersonDocument::STATUS_ACTIVE,
        'category' => PersonDocument::CATEGORY_OTHER,
        'source' => PersonDocument::SOURCE_UPLOAD,
        'title' => 'Document file',
        'original_filename' => 'contract.pdf',
        'mime_type' => 'application/pdf',
        'is_current' => true,
    ]);
    PersonDocument::query()->create([
        'person_id' => $person->id,
        'type' => PersonDocument::TYPE_OTHER,
        'status' => PersonDocument::STATUS_ACTIVE,
        'category' => PersonDocument::CATEGORY_OTHER,
        'source' => PersonDocument::SOURCE_UPLOAD,
        'title' => 'Image file',
        'original_filename' => 'portrait.jpg',
        'mime_type' => 'image/jpeg',
        'is_current' => true,
    ]);

    $this->actingAs($user)
        ->get(route('management.people.edit', $person))
        ->assertOk()
        ->assertSee('aria-label="Images: 1"', false)
        ->assertSee('aria-label="Documents: 1"', false);
});

test('edit person can add uploaded documents from the document workflow', function (): void {
    Storage::fake('local');

    $user = User::factory()->create();
    $person = Person::query()->create([
        'person_number' => 'P-EDIT-DOCUMENT-ADD',
        'first_name' => 'Document',
        'last_name' => 'Add',
    ]);

    $upload = UploadedFile::fake()->create('passport.pdf', 128, 'application/pdf');

    Livewire::actingAs($user)
        ->test(EditPerson::class, ['person' => $person])
        ->call('openAddDocumentModal')
        ->set('newDocumentType', PersonDocument::TYPE_PASSPORT_COPY)
        ->set('newDocumentTitle', 'Uploaded passport')
        ->set('newDocumentNumber', 'PASS-123')
        ->set('newDocumentIssuedAt', '2024-01-01')
        ->set('newDocumentExpiresAt', '2034-01-01')
        ->set('newDocumentUpload', [$upload])
        ->call('addDocument')
        ->assertHasNoErrors()
        ->assertSet('addDocumentModalOpen', false)
        ->assertSet('documentTitle', 'Uploaded passport')
        ->assertSet('documentNumber', 'PASS-123')
        ->assertSet('activeFormTab', 'documents')
        ->assertDispatched('toast-show');

    $document = $person->documentRows()->first();

    expect($document)->not->toBeNull()
        ->and($document?->type)->toBe(PersonDocument::TYPE_PASSPORT_COPY)
        ->and($document?->status)->toBe(PersonDocument::STATUS_ACTIVE)
        ->and($document?->category)->toBe(PersonDocument::CATEGORY_IDENTITY)
        ->and($document?->source)->toBe(PersonDocument::SOURCE_UPLOAD)
        ->and($document?->is_current)->toBeTrue()
        ->and($document?->valid_from?->toDateString())->toBe('2024-01-01')
        ->and($document?->valid_until?->toDateString())->toBe('2034-01-01');

    Storage::disk('local')->assertExists((string) $document?->file_path);
});

test('edit person can add a single uploaded document from the document workflow', function (): void {
    Storage::fake('local');

    $user = User::factory()->create();
    $person = Person::query()->create([
        'person_number' => 'P-EDIT-DOCUMENT-ADD-SINGLE',
        'first_name' => 'Document',
        'last_name' => 'SingleAdd',
    ]);
    $upload = UploadedFile::fake()->create('contract.pdf', 96, 'application/pdf');

    Livewire::actingAs($user)
        ->test(EditPerson::class, ['person' => $person])
        ->call('openAddDocumentModal')
        ->set('newDocumentType', PersonDocument::TYPE_OTHER)
        ->set('newDocumentTitle', 'Uploaded contract')
        ->set('newDocumentIssuedAt', '2025-02-03')
        ->set('newDocumentUpload', $upload)
        ->call('addDocument')
        ->assertHasNoErrors()
        ->assertSet('addDocumentModalOpen', false)
        ->assertSet('documentTitle', 'Uploaded contract')
        ->assertSet('activeFormTab', 'documents')
        ->assertDispatched('toast-show')
        ->call('openDocumentArchive')
        ->assertSee('Uploaded contract');

    $document = $person->documentRows()->first();

    expect($document)->not->toBeNull()
        ->and($document?->type)->toBe(PersonDocument::TYPE_OTHER)
        ->and($document?->title)->toBe('Uploaded contract')
        ->and($document?->is_current)->toBeTrue();

    $activity = DB::table('activity_log')
        ->where('event', 'management.person.document.added')
        ->where('subject_id', $person->id)
        ->first();
    $properties = json_decode((string) $activity?->properties, true);

    expect($activity)->not->toBeNull()
        ->and($activity?->description)->toBe('Person document added')
        ->and($properties['documents_count'] ?? null)->toBe(1)
        ->and($properties['documents'][0]['id'] ?? null)->toBe($document?->id)
        ->and($properties['documents'][0]['title'] ?? null)->toBe('Uploaded contract')
        ->and($properties['documents'][0]['original_filename'] ?? null)->toBe('contract.pdf');

    Storage::disk('local')->assertExists((string) $document?->file_path);
});

test('edit person add document accepts only one uploaded file', function (): void {
    Storage::fake('local');

    $user = User::factory()->create();
    $person = Person::query()->create([
        'person_number' => 'P-EDIT-DOCUMENT-ADD-MULTIPLE',
        'first_name' => 'Document',
        'last_name' => 'Multiple',
    ]);

    Livewire::actingAs($user)
        ->test(EditPerson::class, ['person' => $person])
        ->call('openAddDocumentModal')
        ->set('newDocumentType', PersonDocument::TYPE_OTHER)
        ->set('newDocumentTitle', 'Multiple upload')
        ->set('newDocumentIssuedAt', '2025-02-03')
        ->set('newDocumentUpload', [
            UploadedFile::fake()->create('first.pdf', 64, 'application/pdf'),
            UploadedFile::fake()->create('second.pdf', 64, 'application/pdf'),
        ])
        ->call('addDocument')
        ->assertHasErrors(['newDocumentUpload'])
        ->assertSet('addDocumentModalOpen', true)
        ->assertDispatched('toast-show');

    expect($person->documentRows()->count())->toBe(0);
});

test('edit person can upload the same original filename for different people', function (): void {
    Storage::fake('local');

    $user = User::factory()->create();
    $firstPerson = Person::query()->create([
        'person_number' => 'P-EDIT-DOCUMENT-DUPLICATE-001',
        'first_name' => 'First',
        'last_name' => 'Person',
    ]);
    $secondPerson = Person::query()->create([
        'person_number' => 'P-EDIT-DOCUMENT-DUPLICATE-002',
        'first_name' => 'Second',
        'last_name' => 'Person',
    ]);

    foreach ([$firstPerson, $secondPerson] as $person) {
        Livewire::actingAs($user)
            ->test(EditPerson::class, ['person' => $person])
            ->call('openAddDocumentModal')
            ->set('newDocumentType', PersonDocument::TYPE_OTHER)
            ->set('newDocumentTitle', 'Repeated filename')
            ->set('newDocumentIssuedAt', '2025-02-03')
            ->set('newDocumentUpload', UploadedFile::fake()->create('same-name.pdf', 64, 'application/pdf'))
            ->call('addDocument')
            ->assertHasNoErrors()
            ->assertSet('addDocumentModalOpen', false);
    }

    $firstDocument = $firstPerson->documentRows()->first();
    $secondDocument = $secondPerson->documentRows()->first();

    expect($firstDocument?->original_filename)->toBe('same-name.pdf')
        ->and($secondDocument?->original_filename)->toBe('same-name.pdf')
        ->and($firstDocument?->file_path)->not->toBe($secondDocument?->file_path)
        ->and($firstDocument?->file_path)->not->toContain("person-documents/{$firstPerson->id}/")
        ->and($secondDocument?->file_path)->not->toContain("person-documents/{$secondPerson->id}/");
});

test('edit person add document requires type title issued date and file', function (): void {
    Storage::fake('local');

    $user = User::factory()->create();
    $person = Person::query()->create([
        'person_number' => 'P-EDIT-DOCUMENT-ADD-REQUIRED',
        'first_name' => 'Document',
        'last_name' => 'Required',
    ]);
    $upload = UploadedFile::fake()->create('required.pdf', 64, 'application/pdf');

    Livewire::actingAs($user)
        ->test(EditPerson::class, ['person' => $person])
        ->call('openAddDocumentModal')
        ->set('newDocumentUpload', $upload)
        ->call('addDocument')
        ->assertHasErrors([
            'newDocumentType' => 'required',
            'newDocumentTitle' => 'required',
            'newDocumentIssuedAt' => 'required',
        ])
        ->assertSet('addDocumentModalOpen', true);

    expect($person->documentRows()->count())->toBe(0);
});

test('edit person can create a custom document type in the add document workflow', function (): void {
    $user = User::factory()->create();
    $person = Person::query()->create([
        'person_number' => 'P-EDIT-DOCUMENT-CUSTOM-TYPE',
        'first_name' => 'Document',
        'last_name' => 'CustomType',
    ]);

    Livewire::actingAs($user)
        ->test(EditPerson::class, ['person' => $person])
        ->call('openAddDocumentModal')
        ->call('createNewDocumentType', 'Ärztliche Bescheinigung')
        ->assertSet('newDocumentType', 'aerztliche_bescheinigung')
        ->assertSet('documentTypeOptions.aerztliche_bescheinigung', 'Ärztliche Bescheinigung');
});

test('edit person can save an uploaded document with a custom document type', function (): void {
    Storage::fake('local');

    $user = User::factory()->create();
    $person = Person::query()->create([
        'person_number' => 'P-EDIT-DOCUMENT-CUSTOM-UPLOAD',
        'first_name' => 'Document',
        'last_name' => 'CustomUpload',
    ]);
    $upload = UploadedFile::fake()->create('medical-note.pdf', 64, 'application/pdf');

    Livewire::actingAs($user)
        ->test(EditPerson::class, ['person' => $person])
        ->call('openAddDocumentModal')
        ->call('createNewDocumentType', 'Ärztliche Bescheinigung')
        ->set('newDocumentTitle', 'Uploaded medical note')
        ->set('newDocumentIssuedAt', '2025-03-04')
        ->set('newDocumentUpload', [$upload])
        ->call('addDocument')
        ->assertHasNoErrors()
        ->assertSet('addDocumentModalOpen', false)
        ->assertSet('documentType', 'aerztliche_bescheinigung')
        ->assertSet('documentTitle', 'Uploaded medical note')
        ->assertDispatched('toast-show');

    $document = $person->documentRows()->first();

    expect($document)->not->toBeNull()
        ->and($document?->type)->toBe('aerztliche_bescheinigung')
        ->and($document?->category)->toBe(PersonDocument::CATEGORY_OTHER)
        ->and($document?->source)->toBe(PersonDocument::SOURCE_UPLOAD);

    Storage::disk('local')->assertExists((string) $document?->file_path);
});

test('edit person loads and saves emergency contact fields', function (): void {
    $user = User::factory()->create();
    $person = Person::query()->create([
        'person_number' => 'P-EDIT-EMERGENCY',
        'first_name' => 'Emergency',
        'last_name' => 'Person',
    ]);
    $oldRelatedPerson = Person::query()->create([
        'person_number' => 'P-RELATED-OLD',
        'first_name' => 'Old',
        'last_name' => 'Related',
    ]);
    $newRelatedPerson = Person::query()->create([
        'person_number' => 'P-RELATED-NEW',
        'first_name' => 'New',
        'last_name' => 'Related',
    ]);

    PersonContact::query()->create([
        'person_id' => $person->id,
        'related_person_id' => $oldRelatedPerson->id,
        'type' => PersonContact::TYPE_EMERGENCY,
        'relationship' => PersonContact::RELATIONSHIP_PARENT,
        'name' => 'Old Contact',
        'phone' => '111',
        'email' => 'old.contact@example.test',
        'is_primary' => true,
        'is_emergency_contact' => true,
    ]);

    Livewire::actingAs($user)
        ->test(EditPerson::class, ['person' => $person])
        ->assertSet('emergencyContactPersonId', $oldRelatedPerson->id)
        ->assertSet('emergencyContactName', 'Old Contact')
        ->assertSet('emergencyContactRelationship', PersonContact::RELATIONSHIP_PARENT)
        ->assertSet('emergencyContactPhone', '111')
        ->assertSet('emergencyContactEmail', 'old.contact@example.test')
        ->call('editField', 'emergencyContactPersonId')
        ->set('emergencyContactPersonId', $newRelatedPerson->id)
        ->call('save')
        ->call('editField', 'emergencyContactName')
        ->set('emergencyContactName', 'New Contact')
        ->set('emergencyContactRelationship', PersonContact::RELATIONSHIP_SPOUSE)
        ->set('emergencyContactPhone', '222')
        ->set('emergencyContactEmail', 'new.contact@example.test')
        ->call('save')
        ->assertHasNoErrors();

    $contact = PersonContact::query()
        ->where('person_id', $person->id)
        ->where('type', PersonContact::TYPE_EMERGENCY)
        ->first();

    expect($contact?->name)->toBe('New Contact')
        ->and($contact?->related_person_id)->toBe($newRelatedPerson->id)
        ->and($contact?->relationship)->toBe(PersonContact::RELATIONSHIP_SPOUSE)
        ->and($contact?->phone)->toBe('222')
        ->and($contact?->email)->toBe('new.contact@example.test')
        ->and($contact?->is_primary)->toBeTrue()
        ->and($contact?->is_emergency_contact)->toBeTrue();
});

test('edit person can use a newly entered birth place when birth country is selected', function (): void {
    $user = User::factory()->create();
    $country = Country::query()->create([
        'iso2' => 'DE',
        'iso3' => 'DEU',
        'name' => 'Germany',
        'native_name' => 'Deutschland',
        'is_active' => true,
    ]);
    AddressLocality::query()->create([
        'country_id' => $country->id,
        'name' => 'Berlin',
        'normalized_name' => 'berlin',
        'is_verified' => true,
        'source' => 'test',
    ]);
    $person = Person::query()->create([
        'person_number' => 'P-EDIT-004',
        'salutation' => 'mr',
        'gender' => 'male',
        'marital_status' => 'single',
        'first_name' => 'Birth',
        'last_name' => 'Place',
        'date_of_birth' => '1980-01-01',
        'birth_country_id' => $country->id,
        'birth_place_text' => 'Berlin',
    ]);

    Livewire::actingAs($user)
        ->test(EditPerson::class, ['person' => $person])
        ->call('useCreatedBirthPlaceText', 'Hamburg')
        ->assertSet('birthPlaceText', 'Hamburg');
});
