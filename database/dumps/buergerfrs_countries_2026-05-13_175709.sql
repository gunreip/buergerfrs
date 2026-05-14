--
-- PostgreSQL database dump
--

\restrict C5iU0SerhkhnjygFVQcGeCE5muFy7adMpJh5ktxVdhjteamoBRwFZe5Esh8FDsM

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
-- Data for Name: countries; Type: TABLE DATA; Schema: public; Owner: gunreip
--

COPY public.countries (id, iso2, iso3, name, native_name, phone_code, is_active, sort_order, created_at, updated_at, iso_numeric, official_name, common_name, capital, continent_code, region, subregion, latitude, longitude, emoji_flag, tld, is_independent, is_eu_member, is_eea_member, is_schengen_member, postal_code_required, postal_code_regex, address_format_key) FROM stdin;
250	AD	AND	Andorra	Andorra	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	020	\N	Andorra	\N	\N	\N	\N	\N	\N	🇦🇩	.ad	\N	f	f	f	\N	\N	\N
251	AE	ARE	United Arab Emirates	United Arab Emirates	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	784	\N	United Arab Emirates	\N	\N	\N	\N	\N	\N	🇦🇪	.ae	\N	f	f	f	\N	\N	\N
252	AF	AFG	Afghanistan	Afganistan	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	004	\N	Afghanistan	\N	\N	\N	\N	\N	\N	🇦🇫	.af	\N	f	f	f	\N	\N	\N
253	AG	ATG	Antigua & Barbuda	Antigua & Barbuda	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	028	\N	Antigua & Barbuda	\N	\N	\N	\N	\N	\N	🇦🇬	.ag	\N	f	f	f	\N	\N	\N
254	AI	AIA	Anguilla	Anguilla	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	660	\N	Anguilla	\N	\N	\N	\N	\N	\N	🇦🇮	.ai	\N	f	f	f	\N	\N	\N
255	AL	ALB	Albania	Albania	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	008	\N	Albania	\N	\N	\N	\N	\N	\N	🇦🇱	.al	\N	f	f	f	\N	\N	\N
256	AM	ARM	Armenia	አርሜኒያ	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	051	\N	Armenia	\N	\N	\N	\N	\N	\N	🇦🇲	.am	\N	f	f	f	\N	\N	\N
430	RS	SRB	Serbia	Serbia	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	688	\N	Serbia	\N	\N	\N	\N	\N	\N	🇷🇸	.rs	\N	f	f	f	\N	\N	\N
431	RU	RUS	Russia	Россия	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	643	\N	Russia	\N	\N	\N	\N	\N	\N	🇷🇺	.ru	\N	f	f	f	\N	\N	\N
432	RW	RWA	Rwanda	U Rwanda	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	646	\N	Rwanda	\N	\N	\N	\N	\N	\N	🇷🇼	.rw	\N	f	f	f	\N	\N	\N
433	SA	SAU	Saudi Arabia	Saudi Arabia	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	682	\N	Saudi Arabia	\N	\N	\N	\N	\N	\N	🇸🇦	.sa	\N	f	f	f	\N	\N	\N
257	AO	AGO	Angola	Angola	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	024	\N	Angola	\N	\N	\N	\N	\N	\N	🇦🇴	.ao	\N	f	f	f	\N	\N	\N
258	AQ	ATA	Antarctica	Antarctica	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	010	\N	Antarctica	\N	\N	\N	\N	\N	\N	🇦🇶	.aq	\N	f	f	f	\N	\N	\N
259	AR	ARG	Argentina	الأرجنتين	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	032	\N	Argentina	\N	\N	\N	\N	\N	\N	🇦🇷	.ar	\N	f	f	f	\N	\N	\N
260	AS	ASM	American Samoa	আমেৰিকান চামোৱা	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	016	\N	American Samoa	\N	\N	\N	\N	\N	\N	🇦🇸	.as	\N	f	f	f	\N	\N	\N
374	LT	LTU	Lithuania	Lietuva	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	440	\N	Lithuania	\N	\N	\N	\N	\N	\N	🇱🇹	.lt	\N	t	t	t	\N	\N	\N
9	AT	AUT	Austria	Österreich	+43	t	90	2026-05-08 10:00:43	2026-05-13 12:09:59	040	\N	Austria	Vienna	EU	Europe	Central Europe	47.3333333	13.3333333	🇦🇹	.at	t	t	t	t	\N	\N	\N
261	AU	AUS	Australia	Australia	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	036	\N	Australia	\N	\N	\N	\N	\N	\N	🇦🇺	.au	\N	f	f	f	\N	\N	\N
262	AW	ABW	Aruba	Aruba	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	533	\N	Aruba	\N	\N	\N	\N	\N	\N	🇦🇼	.aw	\N	f	f	f	\N	\N	\N
263	AX	ALA	Åland Islands	Åland Islands	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	248	\N	Åland Islands	\N	\N	\N	\N	\N	\N	🇦🇽	.ax	\N	f	f	f	\N	\N	\N
264	AZ	AZE	Azerbaijan	Azərbaycan	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	031	\N	Azerbaijan	\N	\N	\N	\N	\N	\N	🇦🇿	.az	\N	f	f	f	\N	\N	\N
265	BA	BIH	Bosnia & Herzegovina	Босния һәм Герцеговина	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	070	\N	Bosnia & Herzegovina	\N	\N	\N	\N	\N	\N	🇧🇦	.ba	\N	f	f	f	\N	\N	\N
266	BB	BRB	Barbados	Barbados	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	052	\N	Barbados	\N	\N	\N	\N	\N	\N	🇧🇧	.bb	\N	f	f	f	\N	\N	\N
267	BD	BGD	Bangladesh	Bangladesh	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	050	\N	Bangladesh	\N	\N	\N	\N	\N	\N	🇧🇩	.bd	\N	f	f	f	\N	\N	\N
8	BE	BEL	Belgium	België	+32	t	80	2026-05-08 10:00:43	2026-05-13 12:09:59	056	\N	Belgium	\N	\N	\N	\N	\N	\N	🇧🇪	.be	\N	t	t	t	\N	\N	\N
268	BF	BFA	Burkina Faso	Burkina Faso	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	854	\N	Burkina Faso	\N	\N	\N	\N	\N	\N	🇧🇫	.bf	\N	f	f	f	\N	\N	\N
269	BG	BGR	Bulgaria	България	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	100	\N	Bulgaria	\N	\N	\N	\N	\N	\N	🇧🇬	.bg	\N	t	t	t	\N	\N	\N
270	BH	BHR	Bahrain	Bahrain	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	048	\N	Bahrain	\N	\N	\N	\N	\N	\N	🇧🇭	.bh	\N	f	f	f	\N	\N	\N
271	BI	BDI	Burundi	Burundi	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	108	\N	Burundi	\N	\N	\N	\N	\N	\N	🇧🇮	.bi	\N	f	f	f	\N	\N	\N
272	BJ	BEN	Benin	Benin	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	204	\N	Benin	\N	\N	\N	\N	\N	\N	🇧🇯	.bj	\N	f	f	f	\N	\N	\N
273	BL	BLM	St. Barthélemy	St. Barthélemy	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	652	\N	St. Barthélemy	\N	\N	\N	\N	\N	\N	🇧🇱	.bl	\N	f	f	f	\N	\N	\N
274	BM	BMU	Bermuda	Bermudi	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	060	\N	Bermuda	\N	\N	\N	\N	\N	\N	🇧🇲	.bm	\N	f	f	f	\N	\N	\N
275	BN	BRN	Brunei	ব্রুনেই	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	096	\N	Brunei	\N	\N	\N	\N	\N	\N	🇧🇳	.bn	\N	f	f	f	\N	\N	\N
276	BO	BOL	Bolivia	Bolivia	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	068	\N	Bolivia	\N	\N	\N	\N	\N	\N	🇧🇴	.bo	\N	f	f	f	\N	\N	\N
277	BQ	BES	Caribbean Netherlands	Caribbean Netherlands	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	535	\N	Caribbean Netherlands	\N	\N	\N	\N	\N	\N	🇧🇶	.bq	\N	f	f	f	\N	\N	\N
278	BR	BRA	Brazil	Brazil	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	076	\N	Brazil	\N	\N	\N	\N	\N	\N	🇧🇷	.br	\N	f	f	f	\N	\N	\N
279	BS	BHS	Bahamas	Bahami	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	044	\N	Bahamas	\N	\N	\N	\N	\N	\N	🇧🇸	.bs	\N	f	f	f	\N	\N	\N
280	BT	BTN	Bhutan	Bhutan	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	064	\N	Bhutan	\N	\N	\N	\N	\N	\N	🇧🇹	.bt	\N	f	f	f	\N	\N	\N
281	BV	BVT	Bouvet Island	Bouvet Island	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	074	\N	Bouvet Island	\N	\N	\N	\N	\N	\N	🇧🇻	.bv	\N	f	f	f	\N	\N	\N
282	BW	BWA	Botswana	Botswana	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	072	\N	Botswana	\N	\N	\N	\N	\N	\N	🇧🇼	.bw	\N	f	f	f	\N	\N	\N
283	BY	BLR	Belarus	Belarus	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	112	\N	Belarus	\N	\N	\N	\N	\N	\N	🇧🇾	.by	\N	f	f	f	\N	\N	\N
284	BZ	BLZ	Belize	Belize	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	084	\N	Belize	\N	\N	\N	\N	\N	\N	🇧🇿	.bz	\N	f	f	f	\N	\N	\N
285	CA	CAN	Canada	Canadà	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	124	\N	Canada	\N	\N	\N	\N	\N	\N	🇨🇦	.ca	\N	f	f	f	\N	\N	\N
286	CC	CCK	Cocos (Keeling) Islands	Cocos (Keeling) Islands	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	166	\N	Cocos (Keeling) Islands	\N	\N	\N	\N	\N	\N	🇨🇨	.cc	\N	f	f	f	\N	\N	\N
287	CD	COD	Congo - Kinshasa	Congo - Kinshasa	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	180	\N	Congo - Kinshasa	\N	\N	\N	\N	\N	\N	🇨🇩	.cd	\N	f	f	f	\N	\N	\N
288	CF	CAF	Central African Republic	Central African Republic	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	140	\N	Central African Republic	\N	\N	\N	\N	\N	\N	🇨🇫	.cf	\N	f	f	f	\N	\N	\N
289	CG	COG	Congo - Brazzaville	Congo - Brazzaville	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	178	\N	Congo - Brazzaville	\N	\N	\N	\N	\N	\N	🇨🇬	.cg	\N	f	f	f	\N	\N	\N
10	CH	CHE	Switzerland	Schweiz	+41	t	100	2026-05-08 10:00:43	2026-05-13 12:09:59	756	\N	Switzerland	Bern	EU	Europe	Western Europe	47.0000000	8.0000000	🇨🇭	.ch	t	f	f	t	\N	\N	\N
290	CI	CIV	Côte d’Ivoire	Côte d’Ivoire	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	384	\N	Côte d’Ivoire	\N	\N	\N	\N	\N	\N	🇨🇮	.ci	\N	f	f	f	\N	\N	\N
291	CK	COK	Cook Islands	Cook Islands	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	184	\N	Cook Islands	\N	\N	\N	\N	\N	\N	🇨🇰	.ck	\N	f	f	f	\N	\N	\N
292	CL	CHL	Chile	Chile	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	152	\N	Chile	\N	\N	\N	\N	\N	\N	🇨🇱	.cl	\N	f	f	f	\N	\N	\N
293	CM	CMR	Cameroon	Cameroon	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	120	\N	Cameroon	\N	\N	\N	\N	\N	\N	🇨🇲	.cm	\N	f	f	f	\N	\N	\N
294	CN	CHN	China	China	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	156	\N	China	\N	\N	\N	\N	\N	\N	🇨🇳	.cn	\N	f	f	f	\N	\N	\N
295	CO	COL	Colombia	Colombia	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	170	\N	Colombia	\N	\N	\N	\N	\N	\N	🇨🇴	.co	\N	f	f	f	\N	\N	\N
296	CR	CRI	Costa Rica	Costa Rica	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	188	\N	Costa Rica	\N	\N	\N	\N	\N	\N	🇨🇷	.cr	\N	f	f	f	\N	\N	\N
297	CU	CUB	Cuba	Cuba	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	192	\N	Cuba	\N	\N	\N	\N	\N	\N	🇨🇺	.cu	\N	f	f	f	\N	\N	\N
298	CV	CPV	Cape Verde	Кабо-Верде	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	132	\N	Cape Verde	\N	\N	\N	\N	\N	\N	🇨🇻	.cv	\N	f	f	f	\N	\N	\N
299	CW	CUW	Curaçao	Curaçao	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	531	\N	Curaçao	\N	\N	\N	\N	\N	\N	🇨🇼	.cw	\N	f	f	f	\N	\N	\N
300	CX	CXR	Christmas Island	Christmas Island	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	162	\N	Christmas Island	\N	\N	\N	\N	\N	\N	🇨🇽	.cx	\N	f	f	f	\N	\N	\N
301	CY	CYP	Cyprus	Cyprus	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	196	\N	Cyprus	\N	\N	\N	\N	\N	\N	🇨🇾	.cy	\N	t	t	f	\N	\N	\N
302	CZ	CZE	Czechia	Czechia	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	203	\N	Czechia	\N	\N	\N	\N	\N	\N	🇨🇿	.cz	\N	t	t	t	\N	\N	\N
1	DE	DEU	Germany	Deutschland	+49	t	10	2026-05-08 10:00:43	2026-05-13 12:09:59	276	\N	Germany	Berlin	EU	Europe	Western Europe	51.0000000	9.0000000	🇩🇪	.de	t	t	t	t	\N	\N	\N
303	DJ	DJI	Djibouti	Djibouti	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	262	\N	Djibouti	\N	\N	\N	\N	\N	\N	🇩🇯	.dj	\N	f	f	f	\N	\N	\N
304	DK	DNK	Denmark	Denmark	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	208	\N	Denmark	\N	\N	\N	\N	\N	\N	🇩🇰	.dk	\N	t	t	t	\N	\N	\N
305	DM	DMA	Dominica	Dominica	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	212	\N	Dominica	\N	\N	\N	\N	\N	\N	🇩🇲	.dm	\N	f	f	f	\N	\N	\N
306	DO	DOM	Dominican Republic	Dominican Republic	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	214	\N	Dominican Republic	\N	\N	\N	\N	\N	\N	🇩🇴	.do	\N	f	f	f	\N	\N	\N
307	DZ	DZA	Algeria	ཨཱལ་ཇི་རི་ཡ	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	012	\N	Algeria	\N	\N	\N	\N	\N	\N	🇩🇿	.dz	\N	f	f	f	\N	\N	\N
308	EC	ECU	Ecuador	Ecuador	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	218	\N	Ecuador	\N	\N	\N	\N	\N	\N	🇪🇨	.ec	\N	f	f	f	\N	\N	\N
309	EE	EST	Estonia	Estonia nutome	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	233	\N	Estonia	\N	\N	\N	\N	\N	\N	🇪🇪	.ee	\N	t	t	t	\N	\N	\N
310	EG	EGY	Egypt	Egypt	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	818	\N	Egypt	\N	\N	\N	\N	\N	\N	🇪🇬	.eg	\N	f	f	f	\N	\N	\N
311	EH	ESH	Western Sahara	Western Sahara	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	732	\N	Western Sahara	\N	\N	\N	\N	\N	\N	🇪🇭	.eh	\N	f	f	f	\N	\N	\N
312	ER	ERI	Eritrea	Eritrea	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	232	\N	Eritrea	\N	\N	\N	\N	\N	\N	🇪🇷	.er	\N	f	f	f	\N	\N	\N
2	ES	ESP	Spain	España	+34	t	20	2026-05-08 10:00:43	2026-05-13 12:09:59	724	\N	Spain	\N	\N	\N	\N	\N	\N	🇪🇸	.es	\N	t	t	t	\N	\N	\N
313	ET	ETH	Ethiopia	Etioopia	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	231	\N	Ethiopia	\N	\N	\N	\N	\N	\N	🇪🇹	.et	\N	f	f	f	\N	\N	\N
314	FI	FIN	Finland	Suomi	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	246	\N	Finland	\N	\N	\N	\N	\N	\N	🇫🇮	.fi	\N	t	t	t	\N	\N	\N
315	FJ	FJI	Fiji	Fiji	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	242	\N	Fiji	\N	\N	\N	\N	\N	\N	🇫🇯	.fj	\N	f	f	f	\N	\N	\N
316	FK	FLK	Falkland Islands	Falkland Islands	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	238	\N	Falkland Islands	\N	\N	\N	\N	\N	\N	🇫🇰	.fk	\N	f	f	f	\N	\N	\N
317	FM	FSM	Micronesia	Micronesia	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	583	\N	Micronesia	\N	\N	\N	\N	\N	\N	🇫🇲	.fm	\N	f	f	f	\N	\N	\N
318	FO	FRO	Faroe Islands	Føroyar	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	234	\N	Faroe Islands	\N	\N	\N	\N	\N	\N	🇫🇴	.fo	\N	f	f	f	\N	\N	\N
5	FR	FRA	France	France	+33	t	50	2026-05-08 10:00:43	2026-05-13 12:09:59	250	\N	France	\N	\N	\N	\N	\N	\N	🇫🇷	.fr	\N	t	t	t	\N	\N	\N
319	GA	GAB	Gabon	an Ghabúin	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	266	\N	Gabon	\N	\N	\N	\N	\N	\N	🇬🇦	.ga	\N	f	f	f	\N	\N	\N
3	GB	GBR	United Kingdom	United Kingdom	+44	t	30	2026-05-08 10:00:43	2026-05-13 12:09:59	826	\N	United Kingdom	\N	\N	\N	\N	\N	\N	🇬🇧	.gb	\N	f	f	f	\N	\N	\N
320	GD	GRD	Grenada	Greanàda	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	308	\N	Grenada	\N	\N	\N	\N	\N	\N	🇬🇩	.gd	\N	f	f	f	\N	\N	\N
321	GE	GEO	Georgia	Georgia	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	268	\N	Georgia	\N	\N	\N	\N	\N	\N	🇬🇪	.ge	\N	f	f	f	\N	\N	\N
322	GF	GUF	French Guiana	French Guiana	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	254	\N	French Guiana	\N	\N	\N	\N	\N	\N	🇬🇫	.gf	\N	f	f	f	\N	\N	\N
323	GG	GGY	Guernsey	Guernsey	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	831	\N	Guernsey	\N	\N	\N	\N	\N	\N	🇬🇬	.gg	\N	f	f	f	\N	\N	\N
324	GH	GHA	Ghana	Ghana	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	288	\N	Ghana	\N	\N	\N	\N	\N	\N	🇬🇭	.gh	\N	f	f	f	\N	\N	\N
325	GI	GIB	Gibraltar	Gibraltar	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	292	\N	Gibraltar	\N	\N	\N	\N	\N	\N	🇬🇮	.gi	\N	f	f	f	\N	\N	\N
326	GL	GRL	Greenland	Groenlandia	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	304	\N	Greenland	\N	\N	\N	\N	\N	\N	🇬🇱	.gl	\N	f	f	f	\N	\N	\N
327	GM	GMB	Gambia	Gambia	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	270	\N	Gambia	\N	\N	\N	\N	\N	\N	🇬🇲	.gm	\N	f	f	f	\N	\N	\N
328	GN	GIN	Guinea	Guinea	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	324	\N	Guinea	\N	\N	\N	\N	\N	\N	🇬🇳	.gn	\N	f	f	f	\N	\N	\N
329	GP	GLP	Guadeloupe	Guadeloupe	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	312	\N	Guadeloupe	\N	\N	\N	\N	\N	\N	🇬🇵	.gp	\N	f	f	f	\N	\N	\N
330	GQ	GNQ	Equatorial Guinea	Equatorial Guinea	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	226	\N	Equatorial Guinea	\N	\N	\N	\N	\N	\N	🇬🇶	.gq	\N	f	f	f	\N	\N	\N
331	GR	GRC	Greece	Greece	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	300	\N	Greece	\N	\N	\N	\N	\N	\N	🇬🇷	.gr	\N	t	t	t	\N	\N	\N
332	GS	SGS	South Georgia & South Sandwich Islands	South Georgia & South Sandwich Islands	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	239	\N	South Georgia & South Sandwich Islands	\N	\N	\N	\N	\N	\N	🇬🇸	.gs	\N	f	f	f	\N	\N	\N
333	GT	GTM	Guatemala	Guatemala	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	320	\N	Guatemala	\N	\N	\N	\N	\N	\N	🇬🇹	.gt	\N	f	f	f	\N	\N	\N
334	GU	GUM	Guam	ગ્વામ	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	316	\N	Guam	\N	\N	\N	\N	\N	\N	🇬🇺	.gu	\N	f	f	f	\N	\N	\N
335	GW	GNB	Guinea-Bissau	Guinea-Bissau	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	624	\N	Guinea-Bissau	\N	\N	\N	\N	\N	\N	🇬🇼	.gw	\N	f	f	f	\N	\N	\N
336	GY	GUY	Guyana	Guyana	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	328	\N	Guyana	\N	\N	\N	\N	\N	\N	🇬🇾	.gy	\N	f	f	f	\N	\N	\N
337	HK	HKG	Hong Kong SAR China	Hong Kong SAR China	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	344	\N	Hong Kong SAR China	\N	\N	\N	\N	\N	\N	🇭🇰	.hk	\N	f	f	f	\N	\N	\N
338	HM	HMD	Heard & McDonald Islands	Heard & McDonald Islands	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	334	\N	Heard & McDonald Islands	\N	\N	\N	\N	\N	\N	🇭🇲	.hm	\N	f	f	f	\N	\N	\N
339	HN	HND	Honduras	Honduras	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	340	\N	Honduras	\N	\N	\N	\N	\N	\N	🇭🇳	.hn	\N	f	f	f	\N	\N	\N
340	HR	HRV	Croatia	Hrvatska	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	191	\N	Croatia	\N	\N	\N	\N	\N	\N	🇭🇷	.hr	\N	t	t	t	\N	\N	\N
341	HT	HTI	Haiti	Haiti	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	332	\N	Haiti	\N	\N	\N	\N	\N	\N	🇭🇹	.ht	\N	f	f	f	\N	\N	\N
342	HU	HUN	Hungary	Magyarország	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	348	\N	Hungary	\N	\N	\N	\N	\N	\N	🇭🇺	.hu	\N	t	t	t	\N	\N	\N
343	ID	IDN	Indonesia	Indonesia	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	360	\N	Indonesia	\N	\N	\N	\N	\N	\N	🇮🇩	.id	\N	f	f	f	\N	\N	\N
344	IE	IRL	Ireland	Irland	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	372	\N	Ireland	\N	\N	\N	\N	\N	\N	🇮🇪	.ie	\N	t	t	f	\N	\N	\N
345	IL	ISR	Israel	Israel	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	376	\N	Israel	\N	\N	\N	\N	\N	\N	🇮🇱	.il	\N	f	f	f	\N	\N	\N
346	IM	IMN	Isle of Man	Isle of Man	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	833	\N	Isle of Man	\N	\N	\N	\N	\N	\N	🇮🇲	.im	\N	f	f	f	\N	\N	\N
347	IN	IND	India	India	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	356	\N	India	\N	\N	\N	\N	\N	\N	🇮🇳	.in	\N	f	f	f	\N	\N	\N
348	IO	IOT	British Indian Ocean Territory	British Indian Ocean Territory	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	086	\N	British Indian Ocean Territory	\N	\N	\N	\N	\N	\N	🇮🇴	.io	\N	f	f	f	\N	\N	\N
349	IQ	IRQ	Iraq	Iraq	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	368	\N	Iraq	\N	\N	\N	\N	\N	\N	🇮🇶	.iq	\N	f	f	f	\N	\N	\N
350	IR	IRN	Iran	Iran	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	364	\N	Iran	\N	\N	\N	\N	\N	\N	🇮🇷	.ir	\N	f	f	f	\N	\N	\N
351	IS	ISL	Iceland	Ísland	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	352	\N	Iceland	\N	\N	\N	\N	\N	\N	🇮🇸	.is	\N	f	t	t	\N	\N	\N
6	IT	ITA	Italy	Italia	+39	t	60	2026-05-08 10:00:43	2026-05-13 12:09:59	380	\N	Italy	\N	\N	\N	\N	\N	\N	🇮🇹	.it	\N	t	t	t	\N	\N	\N
352	JE	JEY	Jersey	Jersey	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	832	\N	Jersey	\N	\N	\N	\N	\N	\N	🇯🇪	.je	\N	f	f	f	\N	\N	\N
353	JM	JAM	Jamaica	Jamaica	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	388	\N	Jamaica	\N	\N	\N	\N	\N	\N	🇯🇲	.jm	\N	f	f	f	\N	\N	\N
354	JO	JOR	Jordan	Jordan	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	400	\N	Jordan	\N	\N	\N	\N	\N	\N	🇯🇴	.jo	\N	f	f	f	\N	\N	\N
355	JP	JPN	Japan	Japan	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	392	\N	Japan	\N	\N	\N	\N	\N	\N	🇯🇵	.jp	\N	f	f	f	\N	\N	\N
356	KE	KEN	Kenya	Kenya	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	404	\N	Kenya	\N	\N	\N	\N	\N	\N	🇰🇪	.ke	\N	f	f	f	\N	\N	\N
357	KG	KGZ	Kyrgyzstan	Kyrgyzstan	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	417	\N	Kyrgyzstan	\N	\N	\N	\N	\N	\N	🇰🇬	.kg	\N	f	f	f	\N	\N	\N
358	KH	KHM	Cambodia	Cambodia	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	116	\N	Cambodia	\N	\N	\N	\N	\N	\N	🇰🇭	.kh	\N	f	f	f	\N	\N	\N
359	KI	KIR	Kiribati	Kiribati	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	296	\N	Kiribati	\N	\N	\N	\N	\N	\N	🇰🇮	.ki	\N	f	f	f	\N	\N	\N
360	KM	COM	Comoros	កូម័រ	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	174	\N	Comoros	\N	\N	\N	\N	\N	\N	🇰🇲	.km	\N	f	f	f	\N	\N	\N
361	KN	KNA	St. Kitts & Nevis	ಸೇಂಟ್ ಕಿಟ್ಸ್ ಮತ್ತು ನೆವಿಸ್	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	659	\N	St. Kitts & Nevis	\N	\N	\N	\N	\N	\N	🇰🇳	.kn	\N	f	f	f	\N	\N	\N
362	KP	PRK	North Korea	North Korea	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	408	\N	North Korea	\N	\N	\N	\N	\N	\N	🇰🇵	.kp	\N	f	f	f	\N	\N	\N
363	KR	KOR	South Korea	South Korea	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	410	\N	South Korea	\N	\N	\N	\N	\N	\N	🇰🇷	.kr	\N	f	f	f	\N	\N	\N
364	KW	KWT	Kuwait	Kuwait	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	414	\N	Kuwait	\N	\N	\N	\N	\N	\N	🇰🇼	.kw	\N	f	f	f	\N	\N	\N
365	KY	CYM	Cayman Islands	Кайман аралдары	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	136	\N	Cayman Islands	\N	\N	\N	\N	\N	\N	🇰🇾	.ky	\N	f	f	f	\N	\N	\N
366	KZ	KAZ	Kazakhstan	Kazakhstan	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	398	\N	Kazakhstan	\N	\N	\N	\N	\N	\N	🇰🇿	.kz	\N	f	f	f	\N	\N	\N
367	LA	LAO	Laos	Laos	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	418	\N	Laos	\N	\N	\N	\N	\N	\N	🇱🇦	.la	\N	f	f	f	\N	\N	\N
368	LB	LBN	Lebanon	Libanon	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	422	\N	Lebanon	\N	\N	\N	\N	\N	\N	🇱🇧	.lb	\N	f	f	f	\N	\N	\N
369	LC	LCA	St. Lucia	St. Lucia	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	662	\N	St. Lucia	\N	\N	\N	\N	\N	\N	🇱🇨	.lc	\N	f	f	f	\N	\N	\N
370	LI	LIE	Liechtenstein	Liechtenstein	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	438	\N	Liechtenstein	\N	\N	\N	\N	\N	\N	🇱🇮	.li	\N	f	t	t	\N	\N	\N
371	LK	LKA	Sri Lanka	Sri Lanka	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	144	\N	Sri Lanka	\N	\N	\N	\N	\N	\N	🇱🇰	.lk	\N	f	f	f	\N	\N	\N
372	LR	LBR	Liberia	Liberia	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	430	\N	Liberia	\N	\N	\N	\N	\N	\N	🇱🇷	.lr	\N	f	f	f	\N	\N	\N
373	LS	LSO	Lesotho	Lesotho	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	426	\N	Lesotho	\N	\N	\N	\N	\N	\N	🇱🇸	.ls	\N	f	f	f	\N	\N	\N
375	LU	LUX	Luxembourg	Likisambulu	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	442	\N	Luxembourg	\N	\N	\N	\N	\N	\N	🇱🇺	.lu	\N	t	t	t	\N	\N	\N
376	LV	LVA	Latvia	Latvija	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	428	\N	Latvia	\N	\N	\N	\N	\N	\N	🇱🇻	.lv	\N	t	t	t	\N	\N	\N
377	LY	LBY	Libya	Libya	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	434	\N	Libya	\N	\N	\N	\N	\N	\N	🇱🇾	.ly	\N	f	f	f	\N	\N	\N
378	MA	MAR	Morocco	Morocco	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	504	\N	Morocco	\N	\N	\N	\N	\N	\N	🇲🇦	.ma	\N	f	f	f	\N	\N	\N
379	MC	MCO	Monaco	Monaco	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	492	\N	Monaco	\N	\N	\N	\N	\N	\N	🇲🇨	.mc	\N	f	f	f	\N	\N	\N
380	MD	MDA	Moldova	Moldova	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	498	\N	Moldova	\N	\N	\N	\N	\N	\N	🇲🇩	.md	\N	f	f	f	\N	\N	\N
381	ME	MNE	Montenegro	Montenegro	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	499	\N	Montenegro	\N	\N	\N	\N	\N	\N	🇲🇪	.me	\N	f	f	f	\N	\N	\N
382	MF	MAF	St. Martin	St. Martin	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	663	\N	St. Martin	\N	\N	\N	\N	\N	\N	🇲🇫	.mf	\N	f	f	f	\N	\N	\N
383	MG	MDG	Madagascar	Madagasikara	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	450	\N	Madagascar	\N	\N	\N	\N	\N	\N	🇲🇬	.mg	\N	f	f	f	\N	\N	\N
384	MH	MHL	Marshall Islands	Marshall Islands	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	584	\N	Marshall Islands	\N	\N	\N	\N	\N	\N	🇲🇭	.mh	\N	f	f	f	\N	\N	\N
385	MK	MKD	North Macedonia	Северна Македонија	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	807	\N	North Macedonia	\N	\N	\N	\N	\N	\N	🇲🇰	.mk	\N	f	f	f	\N	\N	\N
386	ML	MLI	Mali	മാലി	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	466	\N	Mali	\N	\N	\N	\N	\N	\N	🇲🇱	.ml	\N	f	f	f	\N	\N	\N
387	MM	MMR	Myanmar (Burma)	Myanmar (Burma)	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	104	\N	Myanmar (Burma)	\N	\N	\N	\N	\N	\N	🇲🇲	.mm	\N	f	f	f	\N	\N	\N
388	MN	MNG	Mongolia	Монгол	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	496	\N	Mongolia	\N	\N	\N	\N	\N	\N	🇲🇳	.mn	\N	f	f	f	\N	\N	\N
389	MO	MAC	Macao SAR China	R.A.S. Macao, China	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	446	\N	Macao SAR China	\N	\N	\N	\N	\N	\N	🇲🇴	.mo	\N	f	f	f	\N	\N	\N
390	MP	MNP	Northern Mariana Islands	Northern Mariana Islands	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	580	\N	Northern Mariana Islands	\N	\N	\N	\N	\N	\N	🇲🇵	.mp	\N	f	f	f	\N	\N	\N
391	MQ	MTQ	Martinique	Martinique	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	474	\N	Martinique	\N	\N	\N	\N	\N	\N	🇲🇶	.mq	\N	f	f	f	\N	\N	\N
392	MR	MRT	Mauritania	मॉरिटानिया	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	478	\N	Mauritania	\N	\N	\N	\N	\N	\N	🇲🇷	.mr	\N	f	f	f	\N	\N	\N
393	MS	MSR	Montserrat	Montserrat	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	500	\N	Montserrat	\N	\N	\N	\N	\N	\N	🇲🇸	.ms	\N	f	f	f	\N	\N	\N
394	MT	MLT	Malta	Malta	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	470	\N	Malta	\N	\N	\N	\N	\N	\N	🇲🇹	.mt	\N	t	t	t	\N	\N	\N
395	MU	MUS	Mauritius	Mauritius	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	480	\N	Mauritius	\N	\N	\N	\N	\N	\N	🇲🇺	.mu	\N	f	f	f	\N	\N	\N
396	MV	MDV	Maldives	Maldives	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	462	\N	Maldives	\N	\N	\N	\N	\N	\N	🇲🇻	.mv	\N	f	f	f	\N	\N	\N
397	MW	MWI	Malawi	Malawi	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	454	\N	Malawi	\N	\N	\N	\N	\N	\N	🇲🇼	.mw	\N	f	f	f	\N	\N	\N
398	MX	MEX	Mexico	Mexico	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	484	\N	Mexico	\N	\N	\N	\N	\N	\N	🇲🇽	.mx	\N	f	f	f	\N	\N	\N
399	MY	MYS	Malaysia	မလေးရှား	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	458	\N	Malaysia	\N	\N	\N	\N	\N	\N	🇲🇾	.my	\N	f	f	f	\N	\N	\N
400	MZ	MOZ	Mozambique	Mozambique	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	508	\N	Mozambique	\N	\N	\N	\N	\N	\N	🇲🇿	.mz	\N	f	f	f	\N	\N	\N
401	NA	NAM	Namibia	Namibia	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	516	\N	Namibia	\N	\N	\N	\N	\N	\N	🇳🇦	.na	\N	f	f	f	\N	\N	\N
402	NC	NCL	New Caledonia	New Caledonia	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	540	\N	New Caledonia	\N	\N	\N	\N	\N	\N	🇳🇨	.nc	\N	f	f	f	\N	\N	\N
403	NE	NER	Niger	नाइजर	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	562	\N	Niger	\N	\N	\N	\N	\N	\N	🇳🇪	.ne	\N	f	f	f	\N	\N	\N
404	NF	NFK	Norfolk Island	Norfolk Island	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	574	\N	Norfolk Island	\N	\N	\N	\N	\N	\N	🇳🇫	.nf	\N	f	f	f	\N	\N	\N
405	NG	NGA	Nigeria	Nigeria	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	566	\N	Nigeria	\N	\N	\N	\N	\N	\N	🇳🇬	.ng	\N	f	f	f	\N	\N	\N
406	NI	NIC	Nicaragua	Nicaragua	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	558	\N	Nicaragua	\N	\N	\N	\N	\N	\N	🇳🇮	.ni	\N	f	f	f	\N	\N	\N
7	NL	NLD	Netherlands	Nederland	+31	t	70	2026-05-08 10:00:43	2026-05-13 12:09:59	528	\N	Netherlands	\N	\N	\N	\N	\N	\N	🇳🇱	.nl	\N	t	t	t	\N	\N	\N
407	NO	NOR	Norway	Norge	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	578	\N	Norway	\N	\N	\N	\N	\N	\N	🇳🇴	.no	\N	f	t	t	\N	\N	\N
408	NP	NPL	Nepal	Nepal	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	524	\N	Nepal	\N	\N	\N	\N	\N	\N	🇳🇵	.np	\N	f	f	f	\N	\N	\N
409	NR	NRU	Nauru	Nauru	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	520	\N	Nauru	\N	\N	\N	\N	\N	\N	🇳🇷	.nr	\N	f	f	f	\N	\N	\N
410	NU	NIU	Niue	Niue	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	570	\N	Niue	\N	\N	\N	\N	\N	\N	🇳🇺	.nu	\N	f	f	f	\N	\N	\N
411	NZ	NZL	New Zealand	New Zealand	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	554	\N	New Zealand	\N	\N	\N	\N	\N	\N	🇳🇿	.nz	\N	f	f	f	\N	\N	\N
412	OM	OMN	Oman	Omaan	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	512	\N	Oman	\N	\N	\N	\N	\N	\N	🇴🇲	.om	\N	f	f	f	\N	\N	\N
413	PA	PAN	Panama	ਪਨਾਮਾ	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	591	\N	Panama	\N	\N	\N	\N	\N	\N	🇵🇦	.pa	\N	f	f	f	\N	\N	\N
414	PE	PER	Peru	Peru	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	604	\N	Peru	\N	\N	\N	\N	\N	\N	🇵🇪	.pe	\N	f	f	f	\N	\N	\N
415	PF	PYF	French Polynesia	French Polynesia	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	258	\N	French Polynesia	\N	\N	\N	\N	\N	\N	🇵🇫	.pf	\N	f	f	f	\N	\N	\N
416	PG	PNG	Papua New Guinea	Papua New Guinea	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	598	\N	Papua New Guinea	\N	\N	\N	\N	\N	\N	🇵🇬	.pg	\N	f	f	f	\N	\N	\N
417	PH	PHL	Philippines	Philippines	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	608	\N	Philippines	\N	\N	\N	\N	\N	\N	🇵🇭	.ph	\N	f	f	f	\N	\N	\N
418	PK	PAK	Pakistan	Pakistan	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	586	\N	Pakistan	\N	\N	\N	\N	\N	\N	🇵🇰	.pk	\N	f	f	f	\N	\N	\N
419	PL	POL	Poland	Polska	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	616	\N	Poland	\N	\N	\N	\N	\N	\N	🇵🇱	.pl	\N	t	t	t	\N	\N	\N
420	PM	SPM	St. Pierre & Miquelon	St. Pierre & Miquelon	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	666	\N	St. Pierre & Miquelon	\N	\N	\N	\N	\N	\N	🇵🇲	.pm	\N	f	f	f	\N	\N	\N
421	PN	PCN	Pitcairn Islands	Pitcairn Islands	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	612	\N	Pitcairn Islands	\N	\N	\N	\N	\N	\N	🇵🇳	.pn	\N	f	f	f	\N	\N	\N
422	PR	PRI	Puerto Rico	Puerto Rico	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	630	\N	Puerto Rico	\N	\N	\N	\N	\N	\N	🇵🇷	.pr	\N	f	f	f	\N	\N	\N
423	PS	PSE	Palestinian Territories	فلسطیني سيمې	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	275	\N	Palestinian Territories	\N	\N	\N	\N	\N	\N	🇵🇸	.ps	\N	f	f	f	\N	\N	\N
424	PT	PRT	Portugal	Portugal	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	620	\N	Portugal	\N	\N	\N	\N	\N	\N	🇵🇹	.pt	\N	t	t	t	\N	\N	\N
425	PW	PLW	Palau	Palau	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	585	\N	Palau	\N	\N	\N	\N	\N	\N	🇵🇼	.pw	\N	f	f	f	\N	\N	\N
426	PY	PRY	Paraguay	Paraguay	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	600	\N	Paraguay	\N	\N	\N	\N	\N	\N	🇵🇾	.py	\N	f	f	f	\N	\N	\N
427	QA	QAT	Qatar	Qatar	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	634	\N	Qatar	\N	\N	\N	\N	\N	\N	🇶🇦	.qa	\N	f	f	f	\N	\N	\N
428	RE	REU	Réunion	Réunion	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	638	\N	Réunion	\N	\N	\N	\N	\N	\N	🇷🇪	.re	\N	f	f	f	\N	\N	\N
429	RO	ROU	Romania	România	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	642	\N	Romania	\N	\N	\N	\N	\N	\N	🇷🇴	.ro	\N	t	t	t	\N	\N	\N
434	SB	SLB	Solomon Islands	Solomon Islands	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	090	\N	Solomon Islands	\N	\N	\N	\N	\N	\N	🇸🇧	.sb	\N	f	f	f	\N	\N	\N
435	SC	SYC	Seychelles	Seychelles	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	690	\N	Seychelles	\N	\N	\N	\N	\N	\N	🇸🇨	.sc	\N	f	f	f	\N	\N	\N
436	SD	SDN	Sudan	سوڊان	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	729	\N	Sudan	\N	\N	\N	\N	\N	\N	🇸🇩	.sd	\N	f	f	f	\N	\N	\N
437	SE	SWE	Sweden	Ruoŧŧa	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	752	\N	Sweden	\N	\N	\N	\N	\N	\N	🇸🇪	.se	\N	t	t	t	\N	\N	\N
438	SG	SGP	Singapore	Sïngäpûru	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	702	\N	Singapore	\N	\N	\N	\N	\N	\N	🇸🇬	.sg	\N	f	f	f	\N	\N	\N
439	SH	SHN	St. Helena	Sveta Jelena	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	654	\N	St. Helena	\N	\N	\N	\N	\N	\N	🇸🇭	.sh	\N	f	f	f	\N	\N	\N
440	SI	SVN	Slovenia	ස්ලෝවේනියාව	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	705	\N	Slovenia	\N	\N	\N	\N	\N	\N	🇸🇮	.si	\N	t	t	t	\N	\N	\N
441	SJ	SJM	Svalbard & Jan Mayen	Svalbard & Jan Mayen	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	744	\N	Svalbard & Jan Mayen	\N	\N	\N	\N	\N	\N	🇸🇯	.sj	\N	f	f	f	\N	\N	\N
442	SK	SVK	Slovakia	Slovensko	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	703	\N	Slovakia	\N	\N	\N	\N	\N	\N	🇸🇰	.sk	\N	t	t	t	\N	\N	\N
443	SL	SLE	Sierra Leone	Sierra Leone	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	694	\N	Sierra Leone	\N	\N	\N	\N	\N	\N	🇸🇱	.sl	\N	f	f	f	\N	\N	\N
444	SM	SMR	San Marino	San Marino	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	674	\N	San Marino	\N	\N	\N	\N	\N	\N	🇸🇲	.sm	\N	f	f	f	\N	\N	\N
445	SN	SEN	Senegal	Senegal	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	686	\N	Senegal	\N	\N	\N	\N	\N	\N	🇸🇳	.sn	\N	f	f	f	\N	\N	\N
446	SO	SOM	Somalia	Soomaaliya	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	706	\N	Somalia	\N	\N	\N	\N	\N	\N	🇸🇴	.so	\N	f	f	f	\N	\N	\N
447	SR	SUR	Suriname	Суринам	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	740	\N	Suriname	\N	\N	\N	\N	\N	\N	🇸🇷	.sr	\N	f	f	f	\N	\N	\N
448	SS	SSD	South Sudan	South Sudan	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	728	\N	South Sudan	\N	\N	\N	\N	\N	\N	🇸🇸	.ss	\N	f	f	f	\N	\N	\N
449	ST	STP	São Tomé & Príncipe	São Tomé & Príncipe	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	678	\N	São Tomé & Príncipe	\N	\N	\N	\N	\N	\N	🇸🇹	.st	\N	f	f	f	\N	\N	\N
450	SV	SLV	El Salvador	El Salvador	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	222	\N	El Salvador	\N	\N	\N	\N	\N	\N	🇸🇻	.sv	\N	f	f	f	\N	\N	\N
451	SX	SXM	Sint Maarten	Sint Maarten	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	534	\N	Sint Maarten	\N	\N	\N	\N	\N	\N	🇸🇽	.sx	\N	f	f	f	\N	\N	\N
452	SY	SYR	Syria	Syria	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	760	\N	Syria	\N	\N	\N	\N	\N	\N	🇸🇾	.sy	\N	f	f	f	\N	\N	\N
453	SZ	SWZ	Eswatini	Eswatini	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	748	\N	Eswatini	\N	\N	\N	\N	\N	\N	🇸🇿	.sz	\N	f	f	f	\N	\N	\N
454	TC	TCA	Turks & Caicos Islands	Turks & Caicos Islands	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	796	\N	Turks & Caicos Islands	\N	\N	\N	\N	\N	\N	🇹🇨	.tc	\N	f	f	f	\N	\N	\N
455	TD	TCD	Chad	Chad	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	148	\N	Chad	\N	\N	\N	\N	\N	\N	🇹🇩	.td	\N	f	f	f	\N	\N	\N
456	TF	ATF	French Southern Territories	French Southern Territories	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	260	\N	French Southern Territories	\N	\N	\N	\N	\N	\N	🇹🇫	.tf	\N	f	f	f	\N	\N	\N
457	TG	TGO	Togo	Того	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	768	\N	Togo	\N	\N	\N	\N	\N	\N	🇹🇬	.tg	\N	f	f	f	\N	\N	\N
458	TH	THA	Thailand	ไทย	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	764	\N	Thailand	\N	\N	\N	\N	\N	\N	🇹🇭	.th	\N	f	f	f	\N	\N	\N
459	TJ	TJK	Tajikistan	Tajikistan	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	762	\N	Tajikistan	\N	\N	\N	\N	\N	\N	🇹🇯	.tj	\N	f	f	f	\N	\N	\N
460	TK	TKL	Tokelau	Tokelau	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	772	\N	Tokelau	\N	\N	\N	\N	\N	\N	🇹🇰	.tk	\N	f	f	f	\N	\N	\N
461	TL	TLS	Timor-Leste	Timor-Leste	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	626	\N	Timor-Leste	\N	\N	\N	\N	\N	\N	🇹🇱	.tl	\N	f	f	f	\N	\N	\N
462	TM	TKM	Turkmenistan	Turkmenistan	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	795	\N	Turkmenistan	\N	\N	\N	\N	\N	\N	🇹🇲	.tm	\N	f	f	f	\N	\N	\N
463	TN	TUN	Tunisia	Tunisia	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	788	\N	Tunisia	\N	\N	\N	\N	\N	\N	🇹🇳	.tn	\N	f	f	f	\N	\N	\N
464	TO	TON	Tonga	Tonga	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	776	\N	Tonga	\N	\N	\N	\N	\N	\N	🇹🇴	.to	\N	f	f	f	\N	\N	\N
465	TR	TUR	Türkiye	Türkiye	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	792	\N	Türkiye	\N	\N	\N	\N	\N	\N	🇹🇷	.tr	\N	f	f	f	\N	\N	\N
466	TT	TTO	Trinidad & Tobago	Тринидад һәм Тобаго	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	780	\N	Trinidad & Tobago	\N	\N	\N	\N	\N	\N	🇹🇹	.tt	\N	f	f	f	\N	\N	\N
467	TV	TUV	Tuvalu	Tuvalu	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	798	\N	Tuvalu	\N	\N	\N	\N	\N	\N	🇹🇻	.tv	\N	f	f	f	\N	\N	\N
468	TW	TWN	Taiwan	Taiwan	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	158	\N	Taiwan	\N	\N	\N	\N	\N	\N	🇹🇼	.tw	\N	f	f	f	\N	\N	\N
469	TZ	TZA	Tanzania	Tanzania	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	834	\N	Tanzania	\N	\N	\N	\N	\N	\N	🇹🇿	.tz	\N	f	f	f	\N	\N	\N
470	UA	UKR	Ukraine	Ukraine	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	804	\N	Ukraine	\N	\N	\N	\N	\N	\N	🇺🇦	.ua	\N	f	f	f	\N	\N	\N
471	UG	UGA	Uganda	ئۇگاندا	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	800	\N	Uganda	\N	\N	\N	\N	\N	\N	🇺🇬	.ug	\N	f	f	f	\N	\N	\N
472	UM	UMI	U.S. Outlying Islands	U.S. Outlying Islands	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	581	\N	U.S. Outlying Islands	\N	\N	\N	\N	\N	\N	🇺🇲	.um	\N	f	f	f	\N	\N	\N
4	US	USA	United States	United States	+1	t	40	2026-05-08 10:00:43	2026-05-13 12:09:59	840	\N	United States	\N	\N	\N	\N	\N	\N	🇺🇸	.us	\N	f	f	f	\N	\N	\N
473	UY	URY	Uruguay	Uruguay	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	858	\N	Uruguay	\N	\N	\N	\N	\N	\N	🇺🇾	.uy	\N	f	f	f	\N	\N	\N
474	UZ	UZB	Uzbekistan	Oʻzbekiston	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	860	\N	Uzbekistan	\N	\N	\N	\N	\N	\N	🇺🇿	.uz	\N	f	f	f	\N	\N	\N
475	VA	VAT	Vatican City	Vatican City	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	336	\N	Vatican City	\N	\N	\N	\N	\N	\N	🇻🇦	.va	\N	f	f	f	\N	\N	\N
476	VC	VCT	St. Vincent & Grenadines	St. Vincent & Grenadines	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	670	\N	St. Vincent & Grenadines	\N	\N	\N	\N	\N	\N	🇻🇨	.vc	\N	f	f	f	\N	\N	\N
477	VE	VEN	Venezuela	Venezuela	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	862	\N	Venezuela	\N	\N	\N	\N	\N	\N	🇻🇪	.ve	\N	f	f	f	\N	\N	\N
478	VG	VGB	British Virgin Islands	British Virgin Islands	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	092	\N	British Virgin Islands	\N	\N	\N	\N	\N	\N	🇻🇬	.vg	\N	f	f	f	\N	\N	\N
479	VI	VIR	U.S. Virgin Islands	Quần đảo Virgin thuộc Hoa Kỳ	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	850	\N	U.S. Virgin Islands	\N	\N	\N	\N	\N	\N	🇻🇮	.vi	\N	f	f	f	\N	\N	\N
480	VN	VNM	Vietnam	Vietnam	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	704	\N	Vietnam	\N	\N	\N	\N	\N	\N	🇻🇳	.vn	\N	f	f	f	\N	\N	\N
481	VU	VUT	Vanuatu	Vanuatu	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	548	\N	Vanuatu	\N	\N	\N	\N	\N	\N	🇻🇺	.vu	\N	f	f	f	\N	\N	\N
482	WF	WLF	Wallis & Futuna	Wallis & Futuna	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	876	\N	Wallis & Futuna	\N	\N	\N	\N	\N	\N	🇼🇫	.wf	\N	f	f	f	\N	\N	\N
483	WS	WSM	Samoa	Samoa	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	882	\N	Samoa	\N	\N	\N	\N	\N	\N	🇼🇸	.ws	\N	f	f	f	\N	\N	\N
484	YE	YEM	Yemen	Yemen	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	887	\N	Yemen	\N	\N	\N	\N	\N	\N	🇾🇪	.ye	\N	f	f	f	\N	\N	\N
485	YT	MYT	Mayotte	Mayotte	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	175	\N	Mayotte	\N	\N	\N	\N	\N	\N	🇾🇹	.yt	\N	f	f	f	\N	\N	\N
486	ZA	ZAF	South Africa	South Africa	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	710	\N	South Africa	\N	\N	\N	\N	\N	\N	🇿🇦	.za	\N	f	f	f	\N	\N	\N
487	ZM	ZMB	Zambia	Zambia	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	894	\N	Zambia	\N	\N	\N	\N	\N	\N	🇿🇲	.zm	\N	f	f	f	\N	\N	\N
488	ZW	ZWE	Zimbabwe	Zimbabwe	\N	t	0	2026-05-13 10:34:23	2026-05-13 12:09:59	716	\N	Zimbabwe	\N	\N	\N	\N	\N	\N	🇿🇼	.zw	\N	f	f	f	\N	\N	\N
\.


--
-- Name: countries_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gunreip
--

SELECT pg_catalog.setval('public.countries_id_seq', 488, true);


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

\unrestrict C5iU0SerhkhnjygFVQcGeCE5muFy7adMpJh5ktxVdhjteamoBRwFZe5Esh8FDsM

