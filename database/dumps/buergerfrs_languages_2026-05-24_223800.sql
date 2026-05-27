--
-- PostgreSQL database dump
--

\restrict DIirwOGwNpaZccUB5ybVOgPaO90MRCq0hoJL0c8PJ9KFcDV9q3wPLZ9mP0bHbmB

-- Dumped from database version 18.4 (Ubuntu 18.4-1.pgdg24.04+1)
-- Dumped by pg_dump version 18.4 (Ubuntu 18.4-1.pgdg24.04+1)

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
-- Name: languages id; Type: DEFAULT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.languages ALTER COLUMN id SET DEFAULT nextval('public.languages_id_seq'::regclass);


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
-- PostgreSQL database dump complete
--

\unrestrict DIirwOGwNpaZccUB5ybVOgPaO90MRCq0hoJL0c8PJ9KFcDV9q3wPLZ9mP0bHbmB

