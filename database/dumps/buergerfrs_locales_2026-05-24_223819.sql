--
-- PostgreSQL database dump
--

\restrict NsxFpAHboTmlxtdUTjMIjG0y7FwZjf57lgTgeAflo68tQpYpIc1aLBz6dqkdzm3

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
-- Name: locales id; Type: DEFAULT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.locales ALTER COLUMN id SET DEFAULT nextval('public.locales_id_seq'::regclass);


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
-- PostgreSQL database dump complete
--

\unrestrict NsxFpAHboTmlxtdUTjMIjG0y7FwZjf57lgTgeAflo68tQpYpIc1aLBz6dqkdzm3

