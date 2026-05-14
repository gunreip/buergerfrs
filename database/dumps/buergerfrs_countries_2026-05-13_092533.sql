--
-- PostgreSQL database dump
--

\restrict J4yYZZL9OuPgsU9Su8t02lvwufflIPciboVfSmNfZvK9rj45avcbuaPgYDAO5uG

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
    updated_at timestamp(0) without time zone
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
-- Name: countries countries_pkey; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.countries
    ADD CONSTRAINT countries_pkey PRIMARY KEY (id);


--
-- Name: countries_is_active_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX countries_is_active_index ON public.countries USING btree (is_active);


--
-- Name: countries_name_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX countries_name_index ON public.countries USING btree (name);


--
-- Name: countries_sort_order_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX countries_sort_order_index ON public.countries USING btree (sort_order);


--
-- PostgreSQL database dump complete
--

\unrestrict J4yYZZL9OuPgsU9Su8t02lvwufflIPciboVfSmNfZvK9rj45avcbuaPgYDAO5uG

