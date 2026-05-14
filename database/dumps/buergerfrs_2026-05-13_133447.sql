--
-- PostgreSQL database dump
--

\restrict KJBY7lQrqMQKvbUPorusdBQUPN88HRxaTjSMbMyLmDZ8jmIddlmCux3JnlDdHn0

-- Dumped from database version 18.3 (Ubuntu 18.3-1.pgdg24.04+1)
-- Dumped by pg_dump version 18.3 (Ubuntu 18.3-1.pgdg24.04+1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: activity_log; Type: TABLE; Schema: public; Owner: gunreip
--

CREATE TABLE public.activity_log (
    id bigint NOT NULL,
    log_name character varying(255),
    description text NOT NULL,
    subject_type character varying(255),
    subject_id bigint,
    event character varying(255),
    causer_type character varying(255),
    causer_id bigint,
    attribute_changes json,
    properties json,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.activity_log OWNER TO gunreip;

--
-- Name: activity_log_id_seq; Type: SEQUENCE; Schema: public; Owner: gunreip
--

CREATE SEQUENCE public.activity_log_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.activity_log_id_seq OWNER TO gunreip;

--
-- Name: activity_log_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gunreip
--

ALTER SEQUENCE public.activity_log_id_seq OWNED BY public.activity_log.id;


--
-- Name: addresses; Type: TABLE; Schema: public; Owner: gunreip
--

CREATE TABLE public.addresses (
    id bigint NOT NULL,
    country_id bigint,
    postal_code character varying(255),
    city character varying(255),
    street character varying(255),
    house_number character varying(255),
    address_line_2 character varying(255),
    latitude numeric(10,7),
    longitude numeric(10,7),
    raw_input text,
    verified_at timestamp(0) without time zone,
    verified_by_user_id bigint,
    notes text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.addresses OWNER TO gunreip;

--
-- Name: addresses_id_seq; Type: SEQUENCE; Schema: public; Owner: gunreip
--

CREATE SEQUENCE public.addresses_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.addresses_id_seq OWNER TO gunreip;

--
-- Name: addresses_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gunreip
--

ALTER SEQUENCE public.addresses_id_seq OWNED BY public.addresses.id;


--
-- Name: cache; Type: TABLE; Schema: public; Owner: gunreip
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration integer NOT NULL
);


ALTER TABLE public.cache OWNER TO gunreip;

--
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: gunreip
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration integer NOT NULL
);


ALTER TABLE public.cache_locks OWNER TO gunreip;

--
-- Name: client_person; Type: TABLE; Schema: public; Owner: gunreip
--

CREATE TABLE public.client_person (
    id bigint NOT NULL,
    client_id bigint NOT NULL,
    person_id bigint NOT NULL,
    relationship_type character varying(255) DEFAULT 'member'::character varying NOT NULL,
    status character varying(255) DEFAULT 'pending'::character varying NOT NULL,
    is_primary boolean DEFAULT false NOT NULL,
    starts_at date,
    ends_at date,
    verified_at timestamp(0) without time zone,
    verified_by_user_id bigint,
    created_by_user_id bigint,
    notes text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.client_person OWNER TO gunreip;

--
-- Name: client_person_id_seq; Type: SEQUENCE; Schema: public; Owner: gunreip
--

CREATE SEQUENCE public.client_person_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.client_person_id_seq OWNER TO gunreip;

--
-- Name: client_person_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gunreip
--

ALTER SEQUENCE public.client_person_id_seq OWNED BY public.client_person.id;


--
-- Name: clients; Type: TABLE; Schema: public; Owner: gunreip
--

CREATE TABLE public.clients (
    id bigint NOT NULL,
    client_number character varying(255),
    name character varying(255) NOT NULL,
    legal_name character varying(255),
    type character varying(255),
    status character varying(255) DEFAULT 'pending'::character varying NOT NULL,
    description text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.clients OWNER TO gunreip;

--
-- Name: clients_id_seq; Type: SEQUENCE; Schema: public; Owner: gunreip
--

CREATE SEQUENCE public.clients_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.clients_id_seq OWNER TO gunreip;

--
-- Name: clients_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gunreip
--

ALTER SEQUENCE public.clients_id_seq OWNED BY public.clients.id;


--
-- Name: countries; Type: TABLE; Schema: public; Owner: gunreip
--

CREATE TABLE public.countries (
    id bigint NOT NULL,
    iso2 character(2) NOT NULL,
    iso3 character(3),
    name character varying(255) NOT NULL,
    native_name character varying(255),
    phone_code character varying(255),
    is_active boolean DEFAULT true NOT NULL,
    sort_order integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    iso_numeric character(3),
    official_name character varying(255),
    common_name character varying(255),
    capital character varying(255),
    continent_code character(2),
    region character varying(255),
    subregion character varying(255),
    latitude numeric(10,7),
    longitude numeric(10,7),
    emoji_flag character varying(16),
    tld character varying(16),
    is_independent boolean,
    is_eu_member boolean DEFAULT false NOT NULL,
    is_eea_member boolean DEFAULT false NOT NULL,
    is_schengen_member boolean DEFAULT false NOT NULL,
    postal_code_required boolean,
    postal_code_regex character varying(255),
    address_format_key character varying(255)
);


ALTER TABLE public.countries OWNER TO gunreip;

--
-- Name: countries_id_seq; Type: SEQUENCE; Schema: public; Owner: gunreip
--

CREATE SEQUENCE public.countries_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.countries_id_seq OWNER TO gunreip;

--
-- Name: countries_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gunreip
--

ALTER SEQUENCE public.countries_id_seq OWNED BY public.countries.id;


--
-- Name: country_names; Type: TABLE; Schema: public; Owner: gunreip
--

CREATE TABLE public.country_names (
    id bigint NOT NULL,
    country_id bigint NOT NULL,
    locale character varying(16) NOT NULL,
    name character varying(255) NOT NULL,
    official_name character varying(255),
    common_name character varying(255),
    source character varying(64),
    is_default boolean DEFAULT false NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.country_names OWNER TO gunreip;

--
-- Name: country_names_id_seq; Type: SEQUENCE; Schema: public; Owner: gunreip
--

CREATE SEQUENCE public.country_names_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.country_names_id_seq OWNER TO gunreip;

--
-- Name: country_names_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gunreip
--

ALTER SEQUENCE public.country_names_id_seq OWNED BY public.country_names.id;


--
-- Name: country_subdivisions; Type: TABLE; Schema: public; Owner: gunreip
--

CREATE TABLE public.country_subdivisions (
    id bigint NOT NULL,
    country_id bigint NOT NULL,
    parent_id bigint,
    code character varying(32) NOT NULL,
    iso_code character varying(64),
    type character varying(64),
    name character varying(255) NOT NULL,
    local_name character varying(255),
    postal_code_pattern character varying(255),
    is_active boolean DEFAULT true NOT NULL,
    sort_order integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.country_subdivisions OWNER TO gunreip;

--
-- Name: country_subdivisions_id_seq; Type: SEQUENCE; Schema: public; Owner: gunreip
--

CREATE SEQUENCE public.country_subdivisions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.country_subdivisions_id_seq OWNER TO gunreip;

--
-- Name: country_subdivisions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gunreip
--

ALTER SEQUENCE public.country_subdivisions_id_seq OWNED BY public.country_subdivisions.id;


--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: gunreip
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.failed_jobs OWNER TO gunreip;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: gunreip
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.failed_jobs_id_seq OWNER TO gunreip;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gunreip
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: fallback_reports; Type: TABLE; Schema: public; Owner: gunreip
--

CREATE TABLE public.fallback_reports (
    id bigint NOT NULL,
    type character varying(255) NOT NULL,
    key character varying(255) NOT NULL,
    fallback character varying(255),
    fingerprint character varying(64) NOT NULL,
    context jsonb,
    count integer DEFAULT 1 NOT NULL,
    first_seen_at timestamp(0) without time zone,
    last_seen_at timestamp(0) without time zone,
    reviewed boolean DEFAULT false NOT NULL,
    reviewed_at timestamp(0) without time zone,
    reviewed_by_user_id bigint,
    review_note text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.fallback_reports OWNER TO gunreip;

--
-- Name: fallback_reports_id_seq; Type: SEQUENCE; Schema: public; Owner: gunreip
--

CREATE SEQUENCE public.fallback_reports_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.fallback_reports_id_seq OWNER TO gunreip;

--
-- Name: fallback_reports_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gunreip
--

ALTER SEQUENCE public.fallback_reports_id_seq OWNED BY public.fallback_reports.id;


--
-- Name: insurance_providers; Type: TABLE; Schema: public; Owner: gunreip
--

CREATE TABLE public.insurance_providers (
    id bigint NOT NULL,
    country_id bigint,
    type character varying(255) DEFAULT 'health'::character varying NOT NULL,
    name character varying(255) NOT NULL,
    short_name character varying(255),
    code character varying(255),
    website character varying(255),
    phone character varying(255),
    email character varying(255),
    is_active boolean DEFAULT true NOT NULL,
    sort_order integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.insurance_providers OWNER TO gunreip;

--
-- Name: insurance_providers_id_seq; Type: SEQUENCE; Schema: public; Owner: gunreip
--

CREATE SEQUENCE public.insurance_providers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.insurance_providers_id_seq OWNER TO gunreip;

--
-- Name: insurance_providers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gunreip
--

ALTER SEQUENCE public.insurance_providers_id_seq OWNED BY public.insurance_providers.id;


--
-- Name: job_batches; Type: TABLE; Schema: public; Owner: gunreip
--

CREATE TABLE public.job_batches (
    id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    total_jobs integer NOT NULL,
    pending_jobs integer NOT NULL,
    failed_jobs integer NOT NULL,
    failed_job_ids text NOT NULL,
    options text,
    cancelled_at integer,
    created_at integer NOT NULL,
    finished_at integer
);


ALTER TABLE public.job_batches OWNER TO gunreip;

--
-- Name: jobs; Type: TABLE; Schema: public; Owner: gunreip
--

CREATE TABLE public.jobs (
    id bigint NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);


ALTER TABLE public.jobs OWNER TO gunreip;

--
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: gunreip
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.jobs_id_seq OWNER TO gunreip;

--
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gunreip
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- Name: language_names; Type: TABLE; Schema: public; Owner: gunreip
--

CREATE TABLE public.language_names (
    id bigint NOT NULL,
    language_id bigint NOT NULL,
    locale character varying(16) NOT NULL,
    name character varying(255) NOT NULL,
    native_name character varying(255),
    source character varying(64),
    is_default boolean DEFAULT false NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.language_names OWNER TO gunreip;

--
-- Name: language_names_id_seq; Type: SEQUENCE; Schema: public; Owner: gunreip
--

CREATE SEQUENCE public.language_names_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.language_names_id_seq OWNER TO gunreip;

--
-- Name: language_names_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gunreip
--

ALTER SEQUENCE public.language_names_id_seq OWNED BY public.language_names.id;


--
-- Name: languages; Type: TABLE; Schema: public; Owner: gunreip
--

CREATE TABLE public.languages (
    id bigint NOT NULL,
    iso639_1 character(2),
    iso639_3 character(3),
    name character varying(255) NOT NULL,
    native_name character varying(255),
    is_active boolean DEFAULT true NOT NULL,
    sort_order integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    iso639_2_b character(3),
    iso639_2_t character(3),
    scope character varying(32),
    type character varying(32),
    macrolanguage_code character(3),
    default_script character(4)
);


ALTER TABLE public.languages OWNER TO gunreip;

--
-- Name: languages_id_seq; Type: SEQUENCE; Schema: public; Owner: gunreip
--

CREATE SEQUENCE public.languages_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.languages_id_seq OWNER TO gunreip;

--
-- Name: languages_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gunreip
--

ALTER SEQUENCE public.languages_id_seq OWNED BY public.languages.id;


--
-- Name: locales; Type: TABLE; Schema: public; Owner: gunreip
--

CREATE TABLE public.locales (
    id bigint NOT NULL,
    code character varying(32) NOT NULL,
    normalized_code character varying(32) NOT NULL,
    language_id bigint,
    country_id bigint,
    script_code character(4),
    variant character varying(32),
    display_name character varying(255),
    native_display_name character varying(255),
    is_active boolean DEFAULT true NOT NULL,
    is_default boolean DEFAULT false NOT NULL,
    sort_order integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.locales OWNER TO gunreip;

--
-- Name: locales_id_seq; Type: SEQUENCE; Schema: public; Owner: gunreip
--

CREATE SEQUENCE public.locales_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.locales_id_seq OWNER TO gunreip;

--
-- Name: locales_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gunreip
--

ALTER SEQUENCE public.locales_id_seq OWNED BY public.locales.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: gunreip
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO gunreip;

--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: gunreip
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.migrations_id_seq OWNER TO gunreip;

--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gunreip
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: model_has_permissions; Type: TABLE; Schema: public; Owner: gunreip
--

CREATE TABLE public.model_has_permissions (
    permission_id bigint NOT NULL,
    model_type character varying(255) NOT NULL,
    model_id bigint NOT NULL
);


ALTER TABLE public.model_has_permissions OWNER TO gunreip;

--
-- Name: model_has_roles; Type: TABLE; Schema: public; Owner: gunreip
--

CREATE TABLE public.model_has_roles (
    role_id bigint NOT NULL,
    model_type character varying(255) NOT NULL,
    model_id bigint NOT NULL
);


ALTER TABLE public.model_has_roles OWNER TO gunreip;

--
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: gunreip
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.password_reset_tokens OWNER TO gunreip;

--
-- Name: people; Type: TABLE; Schema: public; Owner: gunreip
--

CREATE TABLE public.people (
    id bigint NOT NULL,
    person_number character varying(255),
    first_name character varying(255) NOT NULL,
    last_name character varying(255) NOT NULL,
    date_of_birth date,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    salutation character varying(255),
    gender character varying(255),
    middle_name character varying(255),
    preferred_name character varying(255),
    phone character varying(255),
    mobile character varying(255),
    email_private character varying(255),
    email_work character varying(255),
    birth_country_id bigint,
    birth_place_text character varying(255),
    marital_status character varying(255),
    birth_name character varying(255),
    avatar_path character varying(255),
    name_title character varying(255)
);


ALTER TABLE public.people OWNER TO gunreip;

--
-- Name: people_id_seq; Type: SEQUENCE; Schema: public; Owner: gunreip
--

CREATE SEQUENCE public.people_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.people_id_seq OWNER TO gunreip;

--
-- Name: people_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gunreip
--

ALTER SEQUENCE public.people_id_seq OWNED BY public.people.id;


--
-- Name: permissions; Type: TABLE; Schema: public; Owner: gunreip
--

CREATE TABLE public.permissions (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    guard_name character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    category character varying(255),
    sort_order integer DEFAULT 100 NOT NULL,
    description text,
    is_system boolean DEFAULT false NOT NULL
);


ALTER TABLE public.permissions OWNER TO gunreip;

--
-- Name: permissions_id_seq; Type: SEQUENCE; Schema: public; Owner: gunreip
--

CREATE SEQUENCE public.permissions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.permissions_id_seq OWNER TO gunreip;

--
-- Name: permissions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gunreip
--

ALTER SEQUENCE public.permissions_id_seq OWNED BY public.permissions.id;


--
-- Name: person_addresses; Type: TABLE; Schema: public; Owner: gunreip
--

CREATE TABLE public.person_addresses (
    id bigint NOT NULL,
    person_id bigint NOT NULL,
    address_id bigint NOT NULL,
    type character varying(255) DEFAULT 'home'::character varying NOT NULL,
    is_primary boolean DEFAULT false NOT NULL,
    starts_at date,
    ends_at date,
    verified_at timestamp(0) without time zone,
    verified_by_user_id bigint,
    notes text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.person_addresses OWNER TO gunreip;

--
-- Name: person_addresses_id_seq; Type: SEQUENCE; Schema: public; Owner: gunreip
--

CREATE SEQUENCE public.person_addresses_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.person_addresses_id_seq OWNER TO gunreip;

--
-- Name: person_addresses_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gunreip
--

ALTER SEQUENCE public.person_addresses_id_seq OWNED BY public.person_addresses.id;


--
-- Name: person_contacts; Type: TABLE; Schema: public; Owner: gunreip
--

CREATE TABLE public.person_contacts (
    id bigint NOT NULL,
    person_id bigint NOT NULL,
    related_person_id bigint,
    type character varying(255) DEFAULT 'emergency'::character varying NOT NULL,
    relationship character varying(255),
    name character varying(255),
    phone character varying(255),
    email character varying(255),
    is_primary boolean DEFAULT false NOT NULL,
    is_emergency_contact boolean DEFAULT false NOT NULL,
    is_authorized_representative boolean DEFAULT false NOT NULL,
    verified_at timestamp(0) without time zone,
    verified_by_user_id bigint,
    notes text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.person_contacts OWNER TO gunreip;

--
-- Name: person_contacts_id_seq; Type: SEQUENCE; Schema: public; Owner: gunreip
--

CREATE SEQUENCE public.person_contacts_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.person_contacts_id_seq OWNER TO gunreip;

--
-- Name: person_contacts_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gunreip
--

ALTER SEQUENCE public.person_contacts_id_seq OWNED BY public.person_contacts.id;


--
-- Name: person_documents; Type: TABLE; Schema: public; Owner: gunreip
--

CREATE TABLE public.person_documents (
    id bigint NOT NULL,
    person_id bigint NOT NULL,
    person_identifier_id bigint,
    issuing_country_id bigint,
    type character varying(255) NOT NULL,
    title character varying(255),
    document_number character varying(255),
    issuing_authority character varying(255),
    issued_at date,
    expires_at date,
    file_disk character varying(255),
    file_path character varying(255),
    original_filename character varying(255),
    mime_type character varying(255),
    file_size bigint,
    verified_at timestamp(0) without time zone,
    verified_by_user_id bigint,
    notes text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.person_documents OWNER TO gunreip;

--
-- Name: person_documents_id_seq; Type: SEQUENCE; Schema: public; Owner: gunreip
--

CREATE SEQUENCE public.person_documents_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.person_documents_id_seq OWNER TO gunreip;

--
-- Name: person_documents_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gunreip
--

ALTER SEQUENCE public.person_documents_id_seq OWNED BY public.person_documents.id;


--
-- Name: person_health_insurances; Type: TABLE; Schema: public; Owner: gunreip
--

CREATE TABLE public.person_health_insurances (
    id bigint NOT NULL,
    person_id bigint NOT NULL,
    insurance_provider_id bigint,
    insurance_number character varying(255),
    starts_at date,
    ends_at date,
    is_primary boolean DEFAULT false NOT NULL,
    verified_at timestamp(0) without time zone,
    verified_by_user_id bigint,
    notes text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.person_health_insurances OWNER TO gunreip;

--
-- Name: person_health_insurances_id_seq; Type: SEQUENCE; Schema: public; Owner: gunreip
--

CREATE SEQUENCE public.person_health_insurances_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.person_health_insurances_id_seq OWNER TO gunreip;

--
-- Name: person_health_insurances_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gunreip
--

ALTER SEQUENCE public.person_health_insurances_id_seq OWNED BY public.person_health_insurances.id;


--
-- Name: person_identifiers; Type: TABLE; Schema: public; Owner: gunreip
--

CREATE TABLE public.person_identifiers (
    id bigint NOT NULL,
    person_id bigint NOT NULL,
    issuing_country_id bigint,
    type character varying(255) NOT NULL,
    value character varying(255) NOT NULL,
    value_hash character(64),
    issuing_authority character varying(255),
    issued_at date,
    expires_at date,
    is_primary boolean DEFAULT false NOT NULL,
    verified_at timestamp(0) without time zone,
    verified_by_user_id bigint,
    notes text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.person_identifiers OWNER TO gunreip;

--
-- Name: person_identifiers_id_seq; Type: SEQUENCE; Schema: public; Owner: gunreip
--

CREATE SEQUENCE public.person_identifiers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.person_identifiers_id_seq OWNER TO gunreip;

--
-- Name: person_identifiers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gunreip
--

ALTER SEQUENCE public.person_identifiers_id_seq OWNED BY public.person_identifiers.id;


--
-- Name: person_languages; Type: TABLE; Schema: public; Owner: gunreip
--

CREATE TABLE public.person_languages (
    id bigint NOT NULL,
    person_id bigint NOT NULL,
    language_id bigint NOT NULL,
    proficiency character varying(255) DEFAULT 'unknown'::character varying NOT NULL,
    is_native boolean DEFAULT false NOT NULL,
    is_primary boolean DEFAULT false NOT NULL,
    preferred_for_communication boolean DEFAULT false NOT NULL,
    starts_at date,
    ends_at date,
    verified_at timestamp(0) without time zone,
    verified_by_user_id bigint,
    notes text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.person_languages OWNER TO gunreip;

--
-- Name: person_languages_id_seq; Type: SEQUENCE; Schema: public; Owner: gunreip
--

CREATE SEQUENCE public.person_languages_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.person_languages_id_seq OWNER TO gunreip;

--
-- Name: person_languages_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gunreip
--

ALTER SEQUENCE public.person_languages_id_seq OWNED BY public.person_languages.id;


--
-- Name: person_nationalities; Type: TABLE; Schema: public; Owner: gunreip
--

CREATE TABLE public.person_nationalities (
    id bigint NOT NULL,
    person_id bigint NOT NULL,
    country_id bigint NOT NULL,
    is_primary boolean DEFAULT false NOT NULL,
    starts_at date,
    ends_at date,
    verified_at timestamp(0) without time zone,
    verified_by_user_id bigint,
    notes text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.person_nationalities OWNER TO gunreip;

--
-- Name: person_nationalities_id_seq; Type: SEQUENCE; Schema: public; Owner: gunreip
--

CREATE SEQUENCE public.person_nationalities_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.person_nationalities_id_seq OWNER TO gunreip;

--
-- Name: person_nationalities_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gunreip
--

ALTER SEQUENCE public.person_nationalities_id_seq OWNED BY public.person_nationalities.id;


--
-- Name: personal_access_tokens; Type: TABLE; Schema: public; Owner: gunreip
--

CREATE TABLE public.personal_access_tokens (
    id bigint NOT NULL,
    tokenable_type character varying(255) NOT NULL,
    tokenable_id bigint NOT NULL,
    name text NOT NULL,
    token character varying(64) NOT NULL,
    abilities text,
    last_used_at timestamp(0) without time zone,
    expires_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.personal_access_tokens OWNER TO gunreip;

--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE; Schema: public; Owner: gunreip
--

CREATE SEQUENCE public.personal_access_tokens_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.personal_access_tokens_id_seq OWNER TO gunreip;

--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gunreip
--

ALTER SEQUENCE public.personal_access_tokens_id_seq OWNED BY public.personal_access_tokens.id;


--
-- Name: role_has_permissions; Type: TABLE; Schema: public; Owner: gunreip
--

CREATE TABLE public.role_has_permissions (
    permission_id bigint NOT NULL,
    role_id bigint NOT NULL
);


ALTER TABLE public.role_has_permissions OWNER TO gunreip;

--
-- Name: roles; Type: TABLE; Schema: public; Owner: gunreip
--

CREATE TABLE public.roles (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    guard_name character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    category character varying(255),
    sort_order smallint DEFAULT '100'::smallint NOT NULL,
    description text,
    is_system boolean DEFAULT false NOT NULL,
    is_assignable boolean DEFAULT true NOT NULL
);


ALTER TABLE public.roles OWNER TO gunreip;

--
-- Name: roles_id_seq; Type: SEQUENCE; Schema: public; Owner: gunreip
--

CREATE SEQUENCE public.roles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.roles_id_seq OWNER TO gunreip;

--
-- Name: roles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gunreip
--

ALTER SEQUENCE public.roles_id_seq OWNED BY public.roles.id;


--
-- Name: sessions; Type: TABLE; Schema: public; Owner: gunreip
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


ALTER TABLE public.sessions OWNER TO gunreip;

--
-- Name: settings; Type: TABLE; Schema: public; Owner: gunreip
--

CREATE TABLE public.settings (
    id bigint NOT NULL,
    "group" character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    locked boolean DEFAULT false NOT NULL,
    payload json NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.settings OWNER TO gunreip;

--
-- Name: settings_id_seq; Type: SEQUENCE; Schema: public; Owner: gunreip
--

CREATE SEQUENCE public.settings_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.settings_id_seq OWNER TO gunreip;

--
-- Name: settings_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gunreip
--

ALTER SEQUENCE public.settings_id_seq OWNED BY public.settings.id;


--
-- Name: telescope_entries; Type: TABLE; Schema: public; Owner: gunreip
--

CREATE TABLE public.telescope_entries (
    sequence bigint NOT NULL,
    uuid uuid NOT NULL,
    batch_id uuid NOT NULL,
    family_hash character varying(255),
    should_display_on_index boolean DEFAULT true NOT NULL,
    type character varying(20) NOT NULL,
    content text NOT NULL,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.telescope_entries OWNER TO gunreip;

--
-- Name: telescope_entries_sequence_seq; Type: SEQUENCE; Schema: public; Owner: gunreip
--

CREATE SEQUENCE public.telescope_entries_sequence_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.telescope_entries_sequence_seq OWNER TO gunreip;

--
-- Name: telescope_entries_sequence_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gunreip
--

ALTER SEQUENCE public.telescope_entries_sequence_seq OWNED BY public.telescope_entries.sequence;


--
-- Name: telescope_entries_tags; Type: TABLE; Schema: public; Owner: gunreip
--

CREATE TABLE public.telescope_entries_tags (
    entry_uuid uuid NOT NULL,
    tag character varying(255) NOT NULL
);


ALTER TABLE public.telescope_entries_tags OWNER TO gunreip;

--
-- Name: telescope_monitoring; Type: TABLE; Schema: public; Owner: gunreip
--

CREATE TABLE public.telescope_monitoring (
    tag character varying(255) NOT NULL
);


ALTER TABLE public.telescope_monitoring OWNER TO gunreip;

--
-- Name: users; Type: TABLE; Schema: public; Owner: gunreip
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    two_factor_secret text,
    two_factor_recovery_codes text,
    two_factor_confirmed_at timestamp(0) without time zone,
    settings jsonb,
    person_id bigint
);


ALTER TABLE public.users OWNER TO gunreip;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: gunreip
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.users_id_seq OWNER TO gunreip;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gunreip
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: activity_log id; Type: DEFAULT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.activity_log ALTER COLUMN id SET DEFAULT nextval('public.activity_log_id_seq'::regclass);


--
-- Name: addresses id; Type: DEFAULT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.addresses ALTER COLUMN id SET DEFAULT nextval('public.addresses_id_seq'::regclass);


--
-- Name: client_person id; Type: DEFAULT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.client_person ALTER COLUMN id SET DEFAULT nextval('public.client_person_id_seq'::regclass);


--
-- Name: clients id; Type: DEFAULT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.clients ALTER COLUMN id SET DEFAULT nextval('public.clients_id_seq'::regclass);


--
-- Name: countries id; Type: DEFAULT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.countries ALTER COLUMN id SET DEFAULT nextval('public.countries_id_seq'::regclass);


--
-- Name: country_names id; Type: DEFAULT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.country_names ALTER COLUMN id SET DEFAULT nextval('public.country_names_id_seq'::regclass);


--
-- Name: country_subdivisions id; Type: DEFAULT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.country_subdivisions ALTER COLUMN id SET DEFAULT nextval('public.country_subdivisions_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: fallback_reports id; Type: DEFAULT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.fallback_reports ALTER COLUMN id SET DEFAULT nextval('public.fallback_reports_id_seq'::regclass);


--
-- Name: insurance_providers id; Type: DEFAULT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.insurance_providers ALTER COLUMN id SET DEFAULT nextval('public.insurance_providers_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- Name: language_names id; Type: DEFAULT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.language_names ALTER COLUMN id SET DEFAULT nextval('public.language_names_id_seq'::regclass);


--
-- Name: languages id; Type: DEFAULT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.languages ALTER COLUMN id SET DEFAULT nextval('public.languages_id_seq'::regclass);


--
-- Name: locales id; Type: DEFAULT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.locales ALTER COLUMN id SET DEFAULT nextval('public.locales_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: people id; Type: DEFAULT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.people ALTER COLUMN id SET DEFAULT nextval('public.people_id_seq'::regclass);


--
-- Name: permissions id; Type: DEFAULT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.permissions ALTER COLUMN id SET DEFAULT nextval('public.permissions_id_seq'::regclass);


--
-- Name: person_addresses id; Type: DEFAULT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.person_addresses ALTER COLUMN id SET DEFAULT nextval('public.person_addresses_id_seq'::regclass);


--
-- Name: person_contacts id; Type: DEFAULT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.person_contacts ALTER COLUMN id SET DEFAULT nextval('public.person_contacts_id_seq'::regclass);


--
-- Name: person_documents id; Type: DEFAULT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.person_documents ALTER COLUMN id SET DEFAULT nextval('public.person_documents_id_seq'::regclass);


--
-- Name: person_health_insurances id; Type: DEFAULT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.person_health_insurances ALTER COLUMN id SET DEFAULT nextval('public.person_health_insurances_id_seq'::regclass);


--
-- Name: person_identifiers id; Type: DEFAULT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.person_identifiers ALTER COLUMN id SET DEFAULT nextval('public.person_identifiers_id_seq'::regclass);


--
-- Name: person_languages id; Type: DEFAULT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.person_languages ALTER COLUMN id SET DEFAULT nextval('public.person_languages_id_seq'::regclass);


--
-- Name: person_nationalities id; Type: DEFAULT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.person_nationalities ALTER COLUMN id SET DEFAULT nextval('public.person_nationalities_id_seq'::regclass);


--
-- Name: personal_access_tokens id; Type: DEFAULT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.personal_access_tokens ALTER COLUMN id SET DEFAULT nextval('public.personal_access_tokens_id_seq'::regclass);


--
-- Name: roles id; Type: DEFAULT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.roles ALTER COLUMN id SET DEFAULT nextval('public.roles_id_seq'::regclass);


--
-- Name: settings id; Type: DEFAULT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.settings ALTER COLUMN id SET DEFAULT nextval('public.settings_id_seq'::regclass);


--
-- Name: telescope_entries sequence; Type: DEFAULT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.telescope_entries ALTER COLUMN sequence SET DEFAULT nextval('public.telescope_entries_sequence_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Name: activity_log activity_log_pkey; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.activity_log
    ADD CONSTRAINT activity_log_pkey PRIMARY KEY (id);


--
-- Name: addresses addresses_pkey; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.addresses
    ADD CONSTRAINT addresses_pkey PRIMARY KEY (id);


--
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- Name: client_person client_person_client_id_person_id_relationship_type_unique; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.client_person
    ADD CONSTRAINT client_person_client_id_person_id_relationship_type_unique UNIQUE (client_id, person_id, relationship_type);


--
-- Name: client_person client_person_pkey; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.client_person
    ADD CONSTRAINT client_person_pkey PRIMARY KEY (id);


--
-- Name: clients clients_client_number_unique; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.clients
    ADD CONSTRAINT clients_client_number_unique UNIQUE (client_number);


--
-- Name: clients clients_pkey; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.clients
    ADD CONSTRAINT clients_pkey PRIMARY KEY (id);


--
-- Name: countries countries_iso2_unique; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.countries
    ADD CONSTRAINT countries_iso2_unique UNIQUE (iso2);


--
-- Name: countries countries_iso3_unique; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.countries
    ADD CONSTRAINT countries_iso3_unique UNIQUE (iso3);


--
-- Name: countries countries_iso_numeric_unique; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.countries
    ADD CONSTRAINT countries_iso_numeric_unique UNIQUE (iso_numeric);


--
-- Name: countries countries_pkey; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.countries
    ADD CONSTRAINT countries_pkey PRIMARY KEY (id);


--
-- Name: country_names country_names_country_id_locale_unique; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.country_names
    ADD CONSTRAINT country_names_country_id_locale_unique UNIQUE (country_id, locale);


--
-- Name: country_names country_names_pkey; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.country_names
    ADD CONSTRAINT country_names_pkey PRIMARY KEY (id);


--
-- Name: country_subdivisions country_subdivisions_country_id_code_unique; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.country_subdivisions
    ADD CONSTRAINT country_subdivisions_country_id_code_unique UNIQUE (country_id, code);


--
-- Name: country_subdivisions country_subdivisions_pkey; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.country_subdivisions
    ADD CONSTRAINT country_subdivisions_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: fallback_reports fallback_reports_pkey; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.fallback_reports
    ADD CONSTRAINT fallback_reports_pkey PRIMARY KEY (id);


--
-- Name: insurance_providers insurance_providers_country_id_type_name_unique; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.insurance_providers
    ADD CONSTRAINT insurance_providers_country_id_type_name_unique UNIQUE (country_id, type, name);


--
-- Name: insurance_providers insurance_providers_pkey; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.insurance_providers
    ADD CONSTRAINT insurance_providers_pkey PRIMARY KEY (id);


--
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- Name: language_names language_names_language_id_locale_unique; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.language_names
    ADD CONSTRAINT language_names_language_id_locale_unique UNIQUE (language_id, locale);


--
-- Name: language_names language_names_pkey; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.language_names
    ADD CONSTRAINT language_names_pkey PRIMARY KEY (id);


--
-- Name: languages languages_iso639_1_unique; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.languages
    ADD CONSTRAINT languages_iso639_1_unique UNIQUE (iso639_1);


--
-- Name: languages languages_iso639_2_b_unique; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.languages
    ADD CONSTRAINT languages_iso639_2_b_unique UNIQUE (iso639_2_b);


--
-- Name: languages languages_iso639_2_t_unique; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.languages
    ADD CONSTRAINT languages_iso639_2_t_unique UNIQUE (iso639_2_t);


--
-- Name: languages languages_iso639_3_unique; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.languages
    ADD CONSTRAINT languages_iso639_3_unique UNIQUE (iso639_3);


--
-- Name: languages languages_pkey; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.languages
    ADD CONSTRAINT languages_pkey PRIMARY KEY (id);


--
-- Name: locales locales_code_unique; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.locales
    ADD CONSTRAINT locales_code_unique UNIQUE (code);


--
-- Name: locales locales_normalized_code_unique; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.locales
    ADD CONSTRAINT locales_normalized_code_unique UNIQUE (normalized_code);


--
-- Name: locales locales_pkey; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.locales
    ADD CONSTRAINT locales_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: model_has_permissions model_has_permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.model_has_permissions
    ADD CONSTRAINT model_has_permissions_pkey PRIMARY KEY (permission_id, model_id, model_type);


--
-- Name: model_has_roles model_has_roles_pkey; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.model_has_roles
    ADD CONSTRAINT model_has_roles_pkey PRIMARY KEY (role_id, model_id, model_type);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: people people_person_number_unique; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.people
    ADD CONSTRAINT people_person_number_unique UNIQUE (person_number);


--
-- Name: people people_pkey; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.people
    ADD CONSTRAINT people_pkey PRIMARY KEY (id);


--
-- Name: permissions permissions_name_guard_name_unique; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT permissions_name_guard_name_unique UNIQUE (name, guard_name);


--
-- Name: permissions permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT permissions_pkey PRIMARY KEY (id);


--
-- Name: person_addresses person_addresses_person_id_address_id_type_unique; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.person_addresses
    ADD CONSTRAINT person_addresses_person_id_address_id_type_unique UNIQUE (person_id, address_id, type);


--
-- Name: person_addresses person_addresses_pkey; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.person_addresses
    ADD CONSTRAINT person_addresses_pkey PRIMARY KEY (id);


--
-- Name: person_contacts person_contacts_pkey; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.person_contacts
    ADD CONSTRAINT person_contacts_pkey PRIMARY KEY (id);


--
-- Name: person_documents person_documents_pkey; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.person_documents
    ADD CONSTRAINT person_documents_pkey PRIMARY KEY (id);


--
-- Name: person_health_insurances person_health_insurances_pkey; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.person_health_insurances
    ADD CONSTRAINT person_health_insurances_pkey PRIMARY KEY (id);


--
-- Name: person_identifiers person_identifiers_person_id_type_value_hash_unique; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.person_identifiers
    ADD CONSTRAINT person_identifiers_person_id_type_value_hash_unique UNIQUE (person_id, type, value_hash);


--
-- Name: person_identifiers person_identifiers_pkey; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.person_identifiers
    ADD CONSTRAINT person_identifiers_pkey PRIMARY KEY (id);


--
-- Name: person_languages person_languages_person_id_language_id_unique; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.person_languages
    ADD CONSTRAINT person_languages_person_id_language_id_unique UNIQUE (person_id, language_id);


--
-- Name: person_languages person_languages_pkey; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.person_languages
    ADD CONSTRAINT person_languages_pkey PRIMARY KEY (id);


--
-- Name: person_nationalities person_nationalities_person_id_country_id_unique; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.person_nationalities
    ADD CONSTRAINT person_nationalities_person_id_country_id_unique UNIQUE (person_id, country_id);


--
-- Name: person_nationalities person_nationalities_pkey; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.person_nationalities
    ADD CONSTRAINT person_nationalities_pkey PRIMARY KEY (id);


--
-- Name: personal_access_tokens personal_access_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_pkey PRIMARY KEY (id);


--
-- Name: personal_access_tokens personal_access_tokens_token_unique; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_token_unique UNIQUE (token);


--
-- Name: role_has_permissions role_has_permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.role_has_permissions
    ADD CONSTRAINT role_has_permissions_pkey PRIMARY KEY (permission_id, role_id);


--
-- Name: roles roles_name_guard_name_unique; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_name_guard_name_unique UNIQUE (name, guard_name);


--
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: settings settings_group_name_unique; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.settings
    ADD CONSTRAINT settings_group_name_unique UNIQUE ("group", name);


--
-- Name: settings settings_pkey; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.settings
    ADD CONSTRAINT settings_pkey PRIMARY KEY (id);


--
-- Name: telescope_entries telescope_entries_pkey; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.telescope_entries
    ADD CONSTRAINT telescope_entries_pkey PRIMARY KEY (sequence);


--
-- Name: telescope_entries_tags telescope_entries_tags_pkey; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.telescope_entries_tags
    ADD CONSTRAINT telescope_entries_tags_pkey PRIMARY KEY (entry_uuid, tag);


--
-- Name: telescope_entries telescope_entries_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.telescope_entries
    ADD CONSTRAINT telescope_entries_uuid_unique UNIQUE (uuid);


--
-- Name: telescope_monitoring telescope_monitoring_pkey; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.telescope_monitoring
    ADD CONSTRAINT telescope_monitoring_pkey PRIMARY KEY (tag);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_person_id_unique; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_person_id_unique UNIQUE (person_id);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: activity_log_log_name_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX activity_log_log_name_index ON public.activity_log USING btree (log_name);


--
-- Name: addresses_city_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX addresses_city_index ON public.addresses USING btree (city);


--
-- Name: addresses_country_id_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX addresses_country_id_index ON public.addresses USING btree (country_id);


--
-- Name: addresses_postal_code_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX addresses_postal_code_index ON public.addresses USING btree (postal_code);


--
-- Name: addresses_street_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX addresses_street_index ON public.addresses USING btree (street);


--
-- Name: addresses_verified_at_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX addresses_verified_at_index ON public.addresses USING btree (verified_at);


--
-- Name: cache_expiration_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX cache_expiration_index ON public.cache USING btree (expiration);


--
-- Name: cache_locks_expiration_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX cache_locks_expiration_index ON public.cache_locks USING btree (expiration);


--
-- Name: causer; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX causer ON public.activity_log USING btree (causer_type, causer_id);


--
-- Name: client_person_ends_at_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX client_person_ends_at_index ON public.client_person USING btree (ends_at);


--
-- Name: client_person_is_primary_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX client_person_is_primary_index ON public.client_person USING btree (is_primary);


--
-- Name: client_person_relationship_type_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX client_person_relationship_type_index ON public.client_person USING btree (relationship_type);


--
-- Name: client_person_starts_at_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX client_person_starts_at_index ON public.client_person USING btree (starts_at);


--
-- Name: client_person_status_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX client_person_status_index ON public.client_person USING btree (status);


--
-- Name: clients_legal_name_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX clients_legal_name_index ON public.clients USING btree (legal_name);


--
-- Name: clients_name_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX clients_name_index ON public.clients USING btree (name);


--
-- Name: clients_status_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX clients_status_index ON public.clients USING btree (status);


--
-- Name: clients_type_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX clients_type_index ON public.clients USING btree (type);


--
-- Name: countries_address_format_key_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX countries_address_format_key_index ON public.countries USING btree (address_format_key);


--
-- Name: countries_continent_code_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX countries_continent_code_index ON public.countries USING btree (continent_code);


--
-- Name: countries_is_active_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX countries_is_active_index ON public.countries USING btree (is_active);


--
-- Name: countries_is_eea_member_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX countries_is_eea_member_index ON public.countries USING btree (is_eea_member);


--
-- Name: countries_is_eu_member_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX countries_is_eu_member_index ON public.countries USING btree (is_eu_member);


--
-- Name: countries_is_independent_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX countries_is_independent_index ON public.countries USING btree (is_independent);


--
-- Name: countries_is_schengen_member_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX countries_is_schengen_member_index ON public.countries USING btree (is_schengen_member);


--
-- Name: countries_name_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX countries_name_index ON public.countries USING btree (name);


--
-- Name: countries_region_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX countries_region_index ON public.countries USING btree (region);


--
-- Name: countries_sort_order_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX countries_sort_order_index ON public.countries USING btree (sort_order);


--
-- Name: countries_subregion_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX countries_subregion_index ON public.countries USING btree (subregion);


--
-- Name: country_names_is_default_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX country_names_is_default_index ON public.country_names USING btree (is_default);


--
-- Name: country_names_locale_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX country_names_locale_index ON public.country_names USING btree (locale);


--
-- Name: country_names_name_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX country_names_name_index ON public.country_names USING btree (name);


--
-- Name: country_subdivisions_is_active_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX country_subdivisions_is_active_index ON public.country_subdivisions USING btree (is_active);


--
-- Name: country_subdivisions_iso_code_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX country_subdivisions_iso_code_index ON public.country_subdivisions USING btree (iso_code);


--
-- Name: country_subdivisions_name_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX country_subdivisions_name_index ON public.country_subdivisions USING btree (name);


--
-- Name: country_subdivisions_parent_id_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX country_subdivisions_parent_id_index ON public.country_subdivisions USING btree (parent_id);


--
-- Name: country_subdivisions_sort_order_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX country_subdivisions_sort_order_index ON public.country_subdivisions USING btree (sort_order);


--
-- Name: country_subdivisions_type_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX country_subdivisions_type_index ON public.country_subdivisions USING btree (type);


--
-- Name: fallback_reports_fingerprint_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX fallback_reports_fingerprint_index ON public.fallback_reports USING btree (fingerprint);


--
-- Name: fallback_reports_fingerprint_reviewed_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX fallback_reports_fingerprint_reviewed_index ON public.fallback_reports USING btree (fingerprint, reviewed);


--
-- Name: fallback_reports_key_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX fallback_reports_key_index ON public.fallback_reports USING btree (key);


--
-- Name: fallback_reports_reviewed_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX fallback_reports_reviewed_index ON public.fallback_reports USING btree (reviewed);


--
-- Name: fallback_reports_type_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX fallback_reports_type_index ON public.fallback_reports USING btree (type);


--
-- Name: insurance_providers_code_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX insurance_providers_code_index ON public.insurance_providers USING btree (code);


--
-- Name: insurance_providers_country_id_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX insurance_providers_country_id_index ON public.insurance_providers USING btree (country_id);


--
-- Name: insurance_providers_is_active_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX insurance_providers_is_active_index ON public.insurance_providers USING btree (is_active);


--
-- Name: insurance_providers_name_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX insurance_providers_name_index ON public.insurance_providers USING btree (name);


--
-- Name: insurance_providers_short_name_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX insurance_providers_short_name_index ON public.insurance_providers USING btree (short_name);


--
-- Name: insurance_providers_sort_order_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX insurance_providers_sort_order_index ON public.insurance_providers USING btree (sort_order);


--
-- Name: insurance_providers_type_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX insurance_providers_type_index ON public.insurance_providers USING btree (type);


--
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- Name: language_names_is_default_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX language_names_is_default_index ON public.language_names USING btree (is_default);


--
-- Name: language_names_locale_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX language_names_locale_index ON public.language_names USING btree (locale);


--
-- Name: language_names_name_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX language_names_name_index ON public.language_names USING btree (name);


--
-- Name: languages_default_script_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX languages_default_script_index ON public.languages USING btree (default_script);


--
-- Name: languages_is_active_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX languages_is_active_index ON public.languages USING btree (is_active);


--
-- Name: languages_macrolanguage_code_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX languages_macrolanguage_code_index ON public.languages USING btree (macrolanguage_code);


--
-- Name: languages_name_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX languages_name_index ON public.languages USING btree (name);


--
-- Name: languages_scope_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX languages_scope_index ON public.languages USING btree (scope);


--
-- Name: languages_sort_order_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX languages_sort_order_index ON public.languages USING btree (sort_order);


--
-- Name: languages_type_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX languages_type_index ON public.languages USING btree (type);


--
-- Name: locales_is_active_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX locales_is_active_index ON public.locales USING btree (is_active);


--
-- Name: locales_is_default_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX locales_is_default_index ON public.locales USING btree (is_default);


--
-- Name: locales_script_code_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX locales_script_code_index ON public.locales USING btree (script_code);


--
-- Name: locales_sort_order_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX locales_sort_order_index ON public.locales USING btree (sort_order);


--
-- Name: locales_variant_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX locales_variant_index ON public.locales USING btree (variant);


--
-- Name: model_has_permissions_model_id_model_type_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX model_has_permissions_model_id_model_type_index ON public.model_has_permissions USING btree (model_id, model_type);


--
-- Name: model_has_roles_model_id_model_type_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX model_has_roles_model_id_model_type_index ON public.model_has_roles USING btree (model_id, model_type);


--
-- Name: people_avatar_path_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX people_avatar_path_index ON public.people USING btree (avatar_path);


--
-- Name: people_birth_country_id_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX people_birth_country_id_index ON public.people USING btree (birth_country_id);


--
-- Name: people_birth_name_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX people_birth_name_index ON public.people USING btree (birth_name);


--
-- Name: people_birth_place_text_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX people_birth_place_text_index ON public.people USING btree (birth_place_text);


--
-- Name: people_date_of_birth_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX people_date_of_birth_index ON public.people USING btree (date_of_birth);


--
-- Name: people_email_private_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX people_email_private_index ON public.people USING btree (email_private);


--
-- Name: people_email_work_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX people_email_work_index ON public.people USING btree (email_work);


--
-- Name: people_first_name_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX people_first_name_index ON public.people USING btree (first_name);


--
-- Name: people_gender_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX people_gender_index ON public.people USING btree (gender);


--
-- Name: people_last_name_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX people_last_name_index ON public.people USING btree (last_name);


--
-- Name: people_marital_status_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX people_marital_status_index ON public.people USING btree (marital_status);


--
-- Name: people_middle_name_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX people_middle_name_index ON public.people USING btree (middle_name);


--
-- Name: people_mobile_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX people_mobile_index ON public.people USING btree (mobile);


--
-- Name: people_name_title_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX people_name_title_index ON public.people USING btree (name_title);


--
-- Name: people_phone_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX people_phone_index ON public.people USING btree (phone);


--
-- Name: people_preferred_name_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX people_preferred_name_index ON public.people USING btree (preferred_name);


--
-- Name: people_salutation_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX people_salutation_index ON public.people USING btree (salutation);


--
-- Name: person_addresses_address_id_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_addresses_address_id_index ON public.person_addresses USING btree (address_id);


--
-- Name: person_addresses_ends_at_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_addresses_ends_at_index ON public.person_addresses USING btree (ends_at);


--
-- Name: person_addresses_is_primary_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_addresses_is_primary_index ON public.person_addresses USING btree (is_primary);


--
-- Name: person_addresses_person_id_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_addresses_person_id_index ON public.person_addresses USING btree (person_id);


--
-- Name: person_addresses_starts_at_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_addresses_starts_at_index ON public.person_addresses USING btree (starts_at);


--
-- Name: person_addresses_type_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_addresses_type_index ON public.person_addresses USING btree (type);


--
-- Name: person_addresses_verified_at_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_addresses_verified_at_index ON public.person_addresses USING btree (verified_at);


--
-- Name: person_contacts_is_authorized_representative_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_contacts_is_authorized_representative_index ON public.person_contacts USING btree (is_authorized_representative);


--
-- Name: person_contacts_is_emergency_contact_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_contacts_is_emergency_contact_index ON public.person_contacts USING btree (is_emergency_contact);


--
-- Name: person_contacts_is_primary_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_contacts_is_primary_index ON public.person_contacts USING btree (is_primary);


--
-- Name: person_contacts_person_id_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_contacts_person_id_index ON public.person_contacts USING btree (person_id);


--
-- Name: person_contacts_related_person_id_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_contacts_related_person_id_index ON public.person_contacts USING btree (related_person_id);


--
-- Name: person_contacts_relationship_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_contacts_relationship_index ON public.person_contacts USING btree (relationship);


--
-- Name: person_contacts_type_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_contacts_type_index ON public.person_contacts USING btree (type);


--
-- Name: person_contacts_verified_at_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_contacts_verified_at_index ON public.person_contacts USING btree (verified_at);


--
-- Name: person_documents_document_number_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_documents_document_number_index ON public.person_documents USING btree (document_number);


--
-- Name: person_documents_expires_at_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_documents_expires_at_index ON public.person_documents USING btree (expires_at);


--
-- Name: person_documents_file_disk_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_documents_file_disk_index ON public.person_documents USING btree (file_disk);


--
-- Name: person_documents_issued_at_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_documents_issued_at_index ON public.person_documents USING btree (issued_at);


--
-- Name: person_documents_issuing_country_id_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_documents_issuing_country_id_index ON public.person_documents USING btree (issuing_country_id);


--
-- Name: person_documents_person_id_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_documents_person_id_index ON public.person_documents USING btree (person_id);


--
-- Name: person_documents_person_identifier_id_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_documents_person_identifier_id_index ON public.person_documents USING btree (person_identifier_id);


--
-- Name: person_documents_type_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_documents_type_index ON public.person_documents USING btree (type);


--
-- Name: person_documents_verified_at_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_documents_verified_at_index ON public.person_documents USING btree (verified_at);


--
-- Name: person_health_insurances_ends_at_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_health_insurances_ends_at_index ON public.person_health_insurances USING btree (ends_at);


--
-- Name: person_health_insurances_insurance_number_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_health_insurances_insurance_number_index ON public.person_health_insurances USING btree (insurance_number);


--
-- Name: person_health_insurances_insurance_provider_id_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_health_insurances_insurance_provider_id_index ON public.person_health_insurances USING btree (insurance_provider_id);


--
-- Name: person_health_insurances_is_primary_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_health_insurances_is_primary_index ON public.person_health_insurances USING btree (is_primary);


--
-- Name: person_health_insurances_person_id_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_health_insurances_person_id_index ON public.person_health_insurances USING btree (person_id);


--
-- Name: person_health_insurances_starts_at_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_health_insurances_starts_at_index ON public.person_health_insurances USING btree (starts_at);


--
-- Name: person_health_insurances_verified_at_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_health_insurances_verified_at_index ON public.person_health_insurances USING btree (verified_at);


--
-- Name: person_identifiers_expires_at_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_identifiers_expires_at_index ON public.person_identifiers USING btree (expires_at);


--
-- Name: person_identifiers_is_primary_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_identifiers_is_primary_index ON public.person_identifiers USING btree (is_primary);


--
-- Name: person_identifiers_issued_at_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_identifiers_issued_at_index ON public.person_identifiers USING btree (issued_at);


--
-- Name: person_identifiers_issuing_country_id_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_identifiers_issuing_country_id_index ON public.person_identifiers USING btree (issuing_country_id);


--
-- Name: person_identifiers_person_id_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_identifiers_person_id_index ON public.person_identifiers USING btree (person_id);


--
-- Name: person_identifiers_type_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_identifiers_type_index ON public.person_identifiers USING btree (type);


--
-- Name: person_identifiers_value_hash_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_identifiers_value_hash_index ON public.person_identifiers USING btree (value_hash);


--
-- Name: person_identifiers_verified_at_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_identifiers_verified_at_index ON public.person_identifiers USING btree (verified_at);


--
-- Name: person_languages_ends_at_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_languages_ends_at_index ON public.person_languages USING btree (ends_at);


--
-- Name: person_languages_is_native_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_languages_is_native_index ON public.person_languages USING btree (is_native);


--
-- Name: person_languages_is_primary_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_languages_is_primary_index ON public.person_languages USING btree (is_primary);


--
-- Name: person_languages_language_id_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_languages_language_id_index ON public.person_languages USING btree (language_id);


--
-- Name: person_languages_person_id_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_languages_person_id_index ON public.person_languages USING btree (person_id);


--
-- Name: person_languages_preferred_for_communication_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_languages_preferred_for_communication_index ON public.person_languages USING btree (preferred_for_communication);


--
-- Name: person_languages_proficiency_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_languages_proficiency_index ON public.person_languages USING btree (proficiency);


--
-- Name: person_languages_starts_at_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_languages_starts_at_index ON public.person_languages USING btree (starts_at);


--
-- Name: person_languages_verified_at_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_languages_verified_at_index ON public.person_languages USING btree (verified_at);


--
-- Name: person_nationalities_country_id_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_nationalities_country_id_index ON public.person_nationalities USING btree (country_id);


--
-- Name: person_nationalities_ends_at_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_nationalities_ends_at_index ON public.person_nationalities USING btree (ends_at);


--
-- Name: person_nationalities_is_primary_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_nationalities_is_primary_index ON public.person_nationalities USING btree (is_primary);


--
-- Name: person_nationalities_person_id_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_nationalities_person_id_index ON public.person_nationalities USING btree (person_id);


--
-- Name: person_nationalities_starts_at_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_nationalities_starts_at_index ON public.person_nationalities USING btree (starts_at);


--
-- Name: person_nationalities_verified_at_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX person_nationalities_verified_at_index ON public.person_nationalities USING btree (verified_at);


--
-- Name: personal_access_tokens_expires_at_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX personal_access_tokens_expires_at_index ON public.personal_access_tokens USING btree (expires_at);


--
-- Name: personal_access_tokens_tokenable_type_tokenable_id_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX personal_access_tokens_tokenable_type_tokenable_id_index ON public.personal_access_tokens USING btree (tokenable_type, tokenable_id);


--
-- Name: roles_category_sort_order_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX roles_category_sort_order_index ON public.roles USING btree (category, sort_order);


--
-- Name: roles_is_assignable_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX roles_is_assignable_index ON public.roles USING btree (is_assignable);


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- Name: subject; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX subject ON public.activity_log USING btree (subject_type, subject_id);


--
-- Name: telescope_entries_batch_id_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX telescope_entries_batch_id_index ON public.telescope_entries USING btree (batch_id);


--
-- Name: telescope_entries_created_at_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX telescope_entries_created_at_index ON public.telescope_entries USING btree (created_at);


--
-- Name: telescope_entries_family_hash_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX telescope_entries_family_hash_index ON public.telescope_entries USING btree (family_hash);


--
-- Name: telescope_entries_tags_tag_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX telescope_entries_tags_tag_index ON public.telescope_entries_tags USING btree (tag);


--
-- Name: telescope_entries_type_should_display_on_index_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX telescope_entries_type_should_display_on_index_index ON public.telescope_entries USING btree (type, should_display_on_index);


--
-- Name: addresses addresses_country_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.addresses
    ADD CONSTRAINT addresses_country_id_foreign FOREIGN KEY (country_id) REFERENCES public.countries(id) ON DELETE SET NULL;


--
-- Name: addresses addresses_verified_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.addresses
    ADD CONSTRAINT addresses_verified_by_user_id_foreign FOREIGN KEY (verified_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: client_person client_person_client_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.client_person
    ADD CONSTRAINT client_person_client_id_foreign FOREIGN KEY (client_id) REFERENCES public.clients(id) ON DELETE CASCADE;


--
-- Name: client_person client_person_created_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.client_person
    ADD CONSTRAINT client_person_created_by_user_id_foreign FOREIGN KEY (created_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: client_person client_person_person_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.client_person
    ADD CONSTRAINT client_person_person_id_foreign FOREIGN KEY (person_id) REFERENCES public.people(id) ON DELETE CASCADE;


--
-- Name: client_person client_person_verified_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.client_person
    ADD CONSTRAINT client_person_verified_by_user_id_foreign FOREIGN KEY (verified_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: country_names country_names_country_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.country_names
    ADD CONSTRAINT country_names_country_id_foreign FOREIGN KEY (country_id) REFERENCES public.countries(id) ON DELETE CASCADE;


--
-- Name: country_subdivisions country_subdivisions_country_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.country_subdivisions
    ADD CONSTRAINT country_subdivisions_country_id_foreign FOREIGN KEY (country_id) REFERENCES public.countries(id) ON DELETE CASCADE;


--
-- Name: country_subdivisions country_subdivisions_parent_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.country_subdivisions
    ADD CONSTRAINT country_subdivisions_parent_id_foreign FOREIGN KEY (parent_id) REFERENCES public.country_subdivisions(id) ON DELETE SET NULL;


--
-- Name: fallback_reports fallback_reports_reviewed_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.fallback_reports
    ADD CONSTRAINT fallback_reports_reviewed_by_user_id_foreign FOREIGN KEY (reviewed_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: insurance_providers insurance_providers_country_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.insurance_providers
    ADD CONSTRAINT insurance_providers_country_id_foreign FOREIGN KEY (country_id) REFERENCES public.countries(id) ON DELETE SET NULL;


--
-- Name: language_names language_names_language_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.language_names
    ADD CONSTRAINT language_names_language_id_foreign FOREIGN KEY (language_id) REFERENCES public.languages(id) ON DELETE CASCADE;


--
-- Name: locales locales_country_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.locales
    ADD CONSTRAINT locales_country_id_foreign FOREIGN KEY (country_id) REFERENCES public.countries(id) ON DELETE SET NULL;


--
-- Name: locales locales_language_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.locales
    ADD CONSTRAINT locales_language_id_foreign FOREIGN KEY (language_id) REFERENCES public.languages(id) ON DELETE SET NULL;


--
-- Name: model_has_permissions model_has_permissions_permission_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.model_has_permissions
    ADD CONSTRAINT model_has_permissions_permission_id_foreign FOREIGN KEY (permission_id) REFERENCES public.permissions(id) ON DELETE CASCADE;


--
-- Name: model_has_roles model_has_roles_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.model_has_roles
    ADD CONSTRAINT model_has_roles_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE CASCADE;


--
-- Name: people people_birth_country_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.people
    ADD CONSTRAINT people_birth_country_id_foreign FOREIGN KEY (birth_country_id) REFERENCES public.countries(id) ON DELETE SET NULL;


--
-- Name: person_addresses person_addresses_address_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.person_addresses
    ADD CONSTRAINT person_addresses_address_id_foreign FOREIGN KEY (address_id) REFERENCES public.addresses(id) ON DELETE CASCADE;


--
-- Name: person_addresses person_addresses_person_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.person_addresses
    ADD CONSTRAINT person_addresses_person_id_foreign FOREIGN KEY (person_id) REFERENCES public.people(id) ON DELETE CASCADE;


--
-- Name: person_addresses person_addresses_verified_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.person_addresses
    ADD CONSTRAINT person_addresses_verified_by_user_id_foreign FOREIGN KEY (verified_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: person_contacts person_contacts_person_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.person_contacts
    ADD CONSTRAINT person_contacts_person_id_foreign FOREIGN KEY (person_id) REFERENCES public.people(id) ON DELETE CASCADE;


--
-- Name: person_contacts person_contacts_related_person_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.person_contacts
    ADD CONSTRAINT person_contacts_related_person_id_foreign FOREIGN KEY (related_person_id) REFERENCES public.people(id) ON DELETE SET NULL;


--
-- Name: person_contacts person_contacts_verified_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.person_contacts
    ADD CONSTRAINT person_contacts_verified_by_user_id_foreign FOREIGN KEY (verified_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: person_documents person_documents_issuing_country_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.person_documents
    ADD CONSTRAINT person_documents_issuing_country_id_foreign FOREIGN KEY (issuing_country_id) REFERENCES public.countries(id) ON DELETE SET NULL;


--
-- Name: person_documents person_documents_person_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.person_documents
    ADD CONSTRAINT person_documents_person_id_foreign FOREIGN KEY (person_id) REFERENCES public.people(id) ON DELETE CASCADE;


--
-- Name: person_documents person_documents_person_identifier_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.person_documents
    ADD CONSTRAINT person_documents_person_identifier_id_foreign FOREIGN KEY (person_identifier_id) REFERENCES public.person_identifiers(id) ON DELETE SET NULL;


--
-- Name: person_documents person_documents_verified_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.person_documents
    ADD CONSTRAINT person_documents_verified_by_user_id_foreign FOREIGN KEY (verified_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: person_health_insurances person_health_insurances_insurance_provider_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.person_health_insurances
    ADD CONSTRAINT person_health_insurances_insurance_provider_id_foreign FOREIGN KEY (insurance_provider_id) REFERENCES public.insurance_providers(id) ON DELETE SET NULL;


--
-- Name: person_health_insurances person_health_insurances_person_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.person_health_insurances
    ADD CONSTRAINT person_health_insurances_person_id_foreign FOREIGN KEY (person_id) REFERENCES public.people(id) ON DELETE CASCADE;


--
-- Name: person_health_insurances person_health_insurances_verified_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.person_health_insurances
    ADD CONSTRAINT person_health_insurances_verified_by_user_id_foreign FOREIGN KEY (verified_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: person_identifiers person_identifiers_issuing_country_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.person_identifiers
    ADD CONSTRAINT person_identifiers_issuing_country_id_foreign FOREIGN KEY (issuing_country_id) REFERENCES public.countries(id) ON DELETE SET NULL;


--
-- Name: person_identifiers person_identifiers_person_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.person_identifiers
    ADD CONSTRAINT person_identifiers_person_id_foreign FOREIGN KEY (person_id) REFERENCES public.people(id) ON DELETE CASCADE;


--
-- Name: person_identifiers person_identifiers_verified_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.person_identifiers
    ADD CONSTRAINT person_identifiers_verified_by_user_id_foreign FOREIGN KEY (verified_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: person_languages person_languages_language_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.person_languages
    ADD CONSTRAINT person_languages_language_id_foreign FOREIGN KEY (language_id) REFERENCES public.languages(id) ON DELETE RESTRICT;


--
-- Name: person_languages person_languages_person_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.person_languages
    ADD CONSTRAINT person_languages_person_id_foreign FOREIGN KEY (person_id) REFERENCES public.people(id) ON DELETE CASCADE;


--
-- Name: person_languages person_languages_verified_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.person_languages
    ADD CONSTRAINT person_languages_verified_by_user_id_foreign FOREIGN KEY (verified_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: person_nationalities person_nationalities_country_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.person_nationalities
    ADD CONSTRAINT person_nationalities_country_id_foreign FOREIGN KEY (country_id) REFERENCES public.countries(id) ON DELETE RESTRICT;


--
-- Name: person_nationalities person_nationalities_person_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.person_nationalities
    ADD CONSTRAINT person_nationalities_person_id_foreign FOREIGN KEY (person_id) REFERENCES public.people(id) ON DELETE CASCADE;


--
-- Name: person_nationalities person_nationalities_verified_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.person_nationalities
    ADD CONSTRAINT person_nationalities_verified_by_user_id_foreign FOREIGN KEY (verified_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: role_has_permissions role_has_permissions_permission_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.role_has_permissions
    ADD CONSTRAINT role_has_permissions_permission_id_foreign FOREIGN KEY (permission_id) REFERENCES public.permissions(id) ON DELETE CASCADE;


--
-- Name: role_has_permissions role_has_permissions_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.role_has_permissions
    ADD CONSTRAINT role_has_permissions_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE CASCADE;


--
-- Name: telescope_entries_tags telescope_entries_tags_entry_uuid_foreign; Type: FK CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.telescope_entries_tags
    ADD CONSTRAINT telescope_entries_tags_entry_uuid_foreign FOREIGN KEY (entry_uuid) REFERENCES public.telescope_entries(uuid) ON DELETE CASCADE;


--
-- Name: users users_person_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_person_id_foreign FOREIGN KEY (person_id) REFERENCES public.people(id) ON DELETE SET NULL;


--
-- PostgreSQL database dump complete
--

\unrestrict KJBY7lQrqMQKvbUPorusdBQUPN88HRxaTjSMbMyLmDZ8jmIddlmCux3JnlDdHn0

