--
-- PostgreSQL database dump
--

\restrict vrwf64Mo1Kqz1FXmk5u0W7zA2lfG64Emsq7jhPmbnUQ1spotuGkqWocAJMkIoKq

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
    updated_at timestamp(0) without time zone
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
-- Name: languages_is_active_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX languages_is_active_index ON public.languages USING btree (is_active);


--
-- Name: languages_name_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX languages_name_index ON public.languages USING btree (name);


--
-- Name: languages_sort_order_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX languages_sort_order_index ON public.languages USING btree (sort_order);


--
-- PostgreSQL database dump complete
--

\unrestrict vrwf64Mo1Kqz1FXmk5u0W7zA2lfG64Emsq7jhPmbnUQ1spotuGkqWocAJMkIoKq

