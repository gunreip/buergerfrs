--
-- PostgreSQL database dump
--

\restrict xxSKBFvBNhHnLl8d00P7x0O5PrUzgStIzuyRy4pwKV07wdMeD8q4B0oeTLHMC1h

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
-- Name: translation_usages; Type: TABLE; Schema: public; Owner: gunreip
--

CREATE TABLE public.translation_usages (
    id bigint NOT NULL,
    translation_key_id bigint NOT NULL,
    fingerprint character varying(64) NOT NULL,
    file character varying(255) NOT NULL,
    line integer,
    function character varying(64),
    classification character varying(32) NOT NULL,
    reason character varying(255),
    raw text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.translation_usages OWNER TO gunreip;

--
-- Name: translation_usages_id_seq; Type: SEQUENCE; Schema: public; Owner: gunreip
--

CREATE SEQUENCE public.translation_usages_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.translation_usages_id_seq OWNER TO gunreip;

--
-- Name: translation_usages_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gunreip
--

ALTER SEQUENCE public.translation_usages_id_seq OWNED BY public.translation_usages.id;


--
-- Name: translation_usages id; Type: DEFAULT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.translation_usages ALTER COLUMN id SET DEFAULT nextval('public.translation_usages_id_seq'::regclass);


--
-- Name: translation_usages translation_usages_fingerprint_unique; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.translation_usages
    ADD CONSTRAINT translation_usages_fingerprint_unique UNIQUE (fingerprint);


--
-- Name: translation_usages translation_usages_pkey; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.translation_usages
    ADD CONSTRAINT translation_usages_pkey PRIMARY KEY (id);


--
-- Name: translation_usages_classification_file_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX translation_usages_classification_file_index ON public.translation_usages USING btree (classification, file);


--
-- Name: translation_usages_classification_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX translation_usages_classification_index ON public.translation_usages USING btree (classification);


--
-- Name: translation_usages_file_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX translation_usages_file_index ON public.translation_usages USING btree (file);


--
-- Name: translation_usages_file_line_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX translation_usages_file_line_index ON public.translation_usages USING btree (file, line);


--
-- Name: translation_usages translation_usages_translation_key_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.translation_usages
    ADD CONSTRAINT translation_usages_translation_key_id_foreign FOREIGN KEY (translation_key_id) REFERENCES public.translation_keys(id) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

\unrestrict xxSKBFvBNhHnLl8d00P7x0O5PrUzgStIzuyRy4pwKV07wdMeD8q4B0oeTLHMC1h

