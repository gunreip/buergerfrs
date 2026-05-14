--
-- PostgreSQL database dump
--

\restrict xOBNClUfyNf2YG6X4OGH1FQlfSbPzhlM7L5cPhSXVhtHuxF2THYihOsk8e5oyd7

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
-- Name: countries id; Type: DEFAULT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.countries ALTER COLUMN id SET DEFAULT nextval('public.countries_id_seq'::regclass);


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
-- PostgreSQL database dump complete
--

\unrestrict xOBNClUfyNf2YG6X4OGH1FQlfSbPzhlM7L5cPhSXVhtHuxF2THYihOsk8e5oyd7

