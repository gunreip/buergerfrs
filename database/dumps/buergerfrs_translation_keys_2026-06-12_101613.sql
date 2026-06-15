--
-- PostgreSQL database dump
--

\restrict OijGlgbWUTPOaLpnStXm13bQDM2NbdCWdOiCx4RD2FE7j80fi0aG2Q5Do7WcvTH

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
-- Name: translation_keys; Type: TABLE; Schema: public; Owner: gunreip
--

CREATE TABLE public.translation_keys (
    id bigint NOT NULL,
    fingerprint character varying(64) NOT NULL,
    key character varying(255),
    namespace character varying(255),
    "group" character varying(255),
    status character varying(32) NOT NULL,
    classification character varying(32) NOT NULL,
    source character varying(32) DEFAULT 'audit'::character varying NOT NULL,
    suggested_key character varying(255),
    native_text text,
    first_seen_at timestamp(0) without time zone,
    last_seen_at timestamp(0) without time zone,
    obsolete_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    workflow_status character varying(32) DEFAULT 'open'::character varying NOT NULL,
    reviewed_at timestamp(0) without time zone,
    reviewed_by_user_id bigint,
    review_note text,
    needs_new_key_marked_at timestamp(0) without time zone,
    needs_new_key_marked_by_user_id bigint,
    needs_new_key_note text,
    needs_new_key_resolved_at timestamp(0) without time zone
);


ALTER TABLE public.translation_keys OWNER TO gunreip;

--
-- Name: translation_keys_id_seq; Type: SEQUENCE; Schema: public; Owner: gunreip
--

CREATE SEQUENCE public.translation_keys_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.translation_keys_id_seq OWNER TO gunreip;

--
-- Name: translation_keys_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gunreip
--

ALTER SEQUENCE public.translation_keys_id_seq OWNED BY public.translation_keys.id;


--
-- Name: translation_keys id; Type: DEFAULT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.translation_keys ALTER COLUMN id SET DEFAULT nextval('public.translation_keys_id_seq'::regclass);


--
-- Name: translation_keys translation_keys_fingerprint_unique; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.translation_keys
    ADD CONSTRAINT translation_keys_fingerprint_unique UNIQUE (fingerprint);


--
-- Name: translation_keys translation_keys_pkey; Type: CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.translation_keys
    ADD CONSTRAINT translation_keys_pkey PRIMARY KEY (id);


--
-- Name: translation_keys_needs_new_key_marked_at_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX translation_keys_needs_new_key_marked_at_index ON public.translation_keys USING btree (needs_new_key_marked_at);


--
-- Name: translation_keys_needs_new_key_resolved_at_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX translation_keys_needs_new_key_resolved_at_index ON public.translation_keys USING btree (needs_new_key_resolved_at);


--
-- Name: translation_keys_reviewed_at_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX translation_keys_reviewed_at_index ON public.translation_keys USING btree (reviewed_at);


--
-- Name: translation_keys_workflow_status_index; Type: INDEX; Schema: public; Owner: gunreip
--

CREATE INDEX translation_keys_workflow_status_index ON public.translation_keys USING btree (workflow_status);


--
-- Name: translation_keys translation_keys_needs_new_key_marked_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.translation_keys
    ADD CONSTRAINT translation_keys_needs_new_key_marked_by_user_id_foreign FOREIGN KEY (needs_new_key_marked_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: translation_keys translation_keys_reviewed_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: gunreip
--

ALTER TABLE ONLY public.translation_keys
    ADD CONSTRAINT translation_keys_reviewed_by_user_id_foreign FOREIGN KEY (reviewed_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- PostgreSQL database dump complete
--

\unrestrict OijGlgbWUTPOaLpnStXm13bQDM2NbdCWdOiCx4RD2FE7j80fi0aG2Q5Do7WcvTH

