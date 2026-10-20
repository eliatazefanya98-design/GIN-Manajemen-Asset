--
-- PostgreSQL database dump
--

\restrict tQkvqoBuO9Nqs0sgSSrKIXYdhZjPfCyhA7bqyzOSEb17Lf0dmIOr2dg4U15f0lM

-- Dumped from database version 14.24
-- Dumped by pg_dump version 14.24

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
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
-- Name: ai_predictions; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.ai_predictions (
    id bigint NOT NULL,
    asset_id bigint NOT NULL,
    prediction boolean NOT NULL,
    risk_score numeric(5,4) NOT NULL,
    risk_category character varying(255) NOT NULL,
    factors json,
    recommendation text,
    model_name character varying(255),
    model_version character varying(255),
    predicted_at timestamp(0) without time zone NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.ai_predictions OWNER TO postgres;

--
-- Name: ai_predictions_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.ai_predictions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.ai_predictions_id_seq OWNER TO postgres;

--
-- Name: ai_predictions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.ai_predictions_id_seq OWNED BY public.ai_predictions.id;


--
-- Name: asset_histories; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.asset_histories (
    id bigint NOT NULL,
    asset_id bigint NOT NULL,
    user_id bigint,
    aktivitas character varying(255) NOT NULL,
    deskripsi text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    tanggal timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.asset_histories OWNER TO postgres;

--
-- Name: asset_histories_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.asset_histories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.asset_histories_id_seq OWNER TO postgres;

--
-- Name: asset_histories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.asset_histories_id_seq OWNED BY public.asset_histories.id;


--
-- Name: asset_loans; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.asset_loans (
    id bigint NOT NULL,
    asset_id bigint NOT NULL,
    user_id bigint,
    tanggal_pinjam date NOT NULL,
    tanggal_rencana_kembali date,
    tanggal_kembali date,
    keperluan text,
    kondisi_sebelum character varying(255),
    kondisi_sesudah character varying(255),
    status character varying(255) DEFAULT 'Diajukan'::character varying NOT NULL,
    catatan text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT asset_loans_status_check CHECK (((status)::text = ANY ((ARRAY['Diajukan'::character varying, 'Dipinjam'::character varying, 'Dikembalikan'::character varying, 'Ditolak'::character varying])::text[])))
);


ALTER TABLE public.asset_loans OWNER TO postgres;

--
-- Name: asset_loans_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.asset_loans_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.asset_loans_id_seq OWNER TO postgres;

--
-- Name: asset_loans_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.asset_loans_id_seq OWNED BY public.asset_loans.id;


--
-- Name: assets; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.assets (
    id bigint NOT NULL,
    category_id bigint NOT NULL,
    location_id bigint,
    vendor_id bigint,
    kode_asset character varying(255) NOT NULL,
    nama_asset character varying(255) NOT NULL,
    deskripsi text,
    tanggal_perolehan date,
    harga numeric(15,2) DEFAULT '0'::numeric NOT NULL,
    kondisi character varying(255) DEFAULT 'baik'::character varying NOT NULL,
    status character varying(255) DEFAULT 'aktif'::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    merk character varying(255),
    tipe character varying(255),
    penanggung_jawab character varying(255),
    CONSTRAINT assets_condition_check CHECK (((kondisi)::text = ANY ((ARRAY['Baik'::character varying, 'Rusak Ringan'::character varying, 'Rusak Berat'::character varying])::text[]))),
    CONSTRAINT assets_status_check CHECK (((status)::text = ANY ((ARRAY['Aktif'::character varying, 'Dipinjam'::character varying, 'Maintenance'::character varying, 'Rusak'::character varying, 'Dihapus'::character varying])::text[])))
);


ALTER TABLE public.assets OWNER TO postgres;

--
-- Name: assets_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.assets_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.assets_id_seq OWNER TO postgres;

--
-- Name: assets_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.assets_id_seq OWNED BY public.assets.id;


--
-- Name: cache; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration integer NOT NULL
);


ALTER TABLE public.cache OWNER TO postgres;

--
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration integer NOT NULL
);


ALTER TABLE public.cache_locks OWNER TO postgres;

--
-- Name: categories; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.categories (
    id bigint NOT NULL,
    nama character varying(255) NOT NULL,
    deskripsi text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.categories OWNER TO postgres;

--
-- Name: categories_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.categories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.categories_id_seq OWNER TO postgres;

--
-- Name: categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.categories_id_seq OWNED BY public.categories.id;


--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.failed_jobs OWNER TO postgres;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.failed_jobs_id_seq OWNER TO postgres;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: job_batches; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.job_batches OWNER TO postgres;

--
-- Name: jobs; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.jobs OWNER TO postgres;

--
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.jobs_id_seq OWNER TO postgres;

--
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- Name: locations; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.locations (
    id bigint NOT NULL,
    nama character varying(255) NOT NULL,
    deskripsi text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    gedung character varying(255),
    lantai character varying(255),
    ruangan character varying(255)
);


ALTER TABLE public.locations OWNER TO postgres;

--
-- Name: locations_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.locations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.locations_id_seq OWNER TO postgres;

--
-- Name: locations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.locations_id_seq OWNED BY public.locations.id;


--
-- Name: maintenance_records; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.maintenance_records (
    id bigint NOT NULL,
    asset_id bigint NOT NULL,
    tanggal date NOT NULL,
    keluhan text NOT NULL,
    biaya numeric(15,2) DEFAULT '0'::numeric NOT NULL,
    teknisi character varying(255),
    status character varying(255) DEFAULT 'terjadwal'::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    jenis character varying(255),
    kerusakan text,
    hasil_tindakan text,
    kondisi_sebelum character varying(255),
    kondisi_sesudah character varying(255),
    CONSTRAINT maintenance_records_status_check CHECK (((status)::text = ANY ((ARRAY['Terjadwal'::character varying, 'Proses'::character varying, 'Selesai'::character varying, 'Dibatalkan'::character varying])::text[])))
);


ALTER TABLE public.maintenance_records OWNER TO postgres;

--
-- Name: maintenance_records_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.maintenance_records_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.maintenance_records_id_seq OWNER TO postgres;

--
-- Name: maintenance_records_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.maintenance_records_id_seq OWNED BY public.maintenance_records.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO postgres;

--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.migrations_id_seq OWNER TO postgres;

--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.password_reset_tokens OWNER TO postgres;

--
-- Name: procurement_items; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.procurement_items (
    id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.procurement_items OWNER TO postgres;

--
-- Name: procurement_items_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.procurement_items_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.procurement_items_id_seq OWNER TO postgres;

--
-- Name: procurement_items_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.procurement_items_id_seq OWNED BY public.procurement_items.id;


--
-- Name: procurements; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.procurements (
    id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.procurements OWNER TO postgres;

--
-- Name: procurements_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.procurements_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.procurements_id_seq OWNER TO postgres;

--
-- Name: procurements_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.procurements_id_seq OWNED BY public.procurements.id;


--
-- Name: sessions; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


ALTER TABLE public.sessions OWNER TO postgres;

--
-- Name: stock_opnames; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.stock_opnames (
    id bigint NOT NULL,
    asset_id bigint NOT NULL,
    tanggal_opname date NOT NULL,
    kondisi_sistem character varying(255),
    kondisi_aktual character varying(255),
    lokasi_sistem character varying(255),
    lokasi_aktual character varying(255),
    keberadaan character varying(255) DEFAULT 'Ada'::character varying NOT NULL,
    hasil character varying(255) DEFAULT 'Sesuai'::character varying NOT NULL,
    catatan text,
    user_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT stock_opnames_hasil_check CHECK (((hasil)::text = ANY ((ARRAY['Sesuai'::character varying, 'Tidak Sesuai'::character varying])::text[]))),
    CONSTRAINT stock_opnames_keberadaan_check CHECK (((keberadaan)::text = ANY ((ARRAY['Ada'::character varying, 'Tidak Ditemukan'::character varying, 'Rusak'::character varying])::text[])))
);


ALTER TABLE public.stock_opnames OWNER TO postgres;

--
-- Name: stock_opnames_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.stock_opnames_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.stock_opnames_id_seq OWNER TO postgres;

--
-- Name: stock_opnames_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.stock_opnames_id_seq OWNED BY public.stock_opnames.id;


--
-- Name: system_settings; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.system_settings (
    id bigint NOT NULL,
    organization_name character varying(255) NOT NULL,
    organization_unit character varying(255) NOT NULL,
    timezone character varying(255) DEFAULT 'Asia/Jakarta (GMT+07:00)'::character varying NOT NULL,
    date_format character varying(255) DEFAULT 'DD/MM/YYYY'::character varying NOT NULL,
    asset_prefix character varying(20) DEFAULT 'AST-GIN'::character varying NOT NULL,
    asset_separator character varying(3) DEFAULT '-'::character varying NOT NULL,
    asset_digits smallint DEFAULT '4'::smallint NOT NULL,
    asset_next_number integer DEFAULT 1 NOT NULL,
    logo_path character varying(255),
    email_notifications boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.system_settings OWNER TO postgres;

--
-- Name: system_settings_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.system_settings_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.system_settings_id_seq OWNER TO postgres;

--
-- Name: system_settings_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.system_settings_id_seq OWNED BY public.system_settings.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
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
    role character varying(30) DEFAULT 'admin'::character varying NOT NULL
);


ALTER TABLE public.users OWNER TO postgres;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.users_id_seq OWNER TO postgres;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: vendors; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.vendors (
    id bigint NOT NULL,
    nama character varying(255) NOT NULL,
    kontak character varying(255),
    alamat text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    email character varying(255)
);


ALTER TABLE public.vendors OWNER TO postgres;

--
-- Name: vendors_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.vendors_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.vendors_id_seq OWNER TO postgres;

--
-- Name: vendors_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.vendors_id_seq OWNED BY public.vendors.id;


--
-- Name: ai_predictions id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ai_predictions ALTER COLUMN id SET DEFAULT nextval('public.ai_predictions_id_seq'::regclass);


--
-- Name: asset_histories id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asset_histories ALTER COLUMN id SET DEFAULT nextval('public.asset_histories_id_seq'::regclass);


--
-- Name: asset_loans id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asset_loans ALTER COLUMN id SET DEFAULT nextval('public.asset_loans_id_seq'::regclass);


--
-- Name: assets id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.assets ALTER COLUMN id SET DEFAULT nextval('public.assets_id_seq'::regclass);


--
-- Name: categories id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categories ALTER COLUMN id SET DEFAULT nextval('public.categories_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- Name: locations id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.locations ALTER COLUMN id SET DEFAULT nextval('public.locations_id_seq'::regclass);


--
-- Name: maintenance_records id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.maintenance_records ALTER COLUMN id SET DEFAULT nextval('public.maintenance_records_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: procurement_items id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.procurement_items ALTER COLUMN id SET DEFAULT nextval('public.procurement_items_id_seq'::regclass);


--
-- Name: procurements id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.procurements ALTER COLUMN id SET DEFAULT nextval('public.procurements_id_seq'::regclass);


--
-- Name: stock_opnames id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.stock_opnames ALTER COLUMN id SET DEFAULT nextval('public.stock_opnames_id_seq'::regclass);


--
-- Name: system_settings id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_settings ALTER COLUMN id SET DEFAULT nextval('public.system_settings_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Name: vendors id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vendors ALTER COLUMN id SET DEFAULT nextval('public.vendors_id_seq'::regclass);


--
-- Data for Name: ai_predictions; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.ai_predictions (id, asset_id, prediction, risk_score, risk_category, factors, recommendation, model_name, model_version, predicted_at, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: asset_histories; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.asset_histories (id, asset_id, user_id, aktivitas, deskripsi, created_at, updated_at, tanggal) FROM stdin;
2	4	\N	Asset Diperbarui	Kondisi: Rusak Ringan → Baik	2026-09-09 03:37:29	2026-09-09 03:37:29	2026-09-09 03:37:29
3	4	\N	Maintenance	Maintenance asset dilakukan. Jenis: Perbaikan. Keluhan: Audio tidak jelas. Status: Proses.	2026-09-09 03:41:17	2026-09-09 03:44:30	2026-09-09 03:41:00
4	4	\N	Maintenance Diperbarui	Status: Proses → Selesai	2026-09-09 04:01:54	2026-09-09 04:01:54	2026-09-09 04:01:54
5	5	\N	Asset Dibuat	Asset AST-03 - Rak berhasil ditambahkan ke sistem.	2026-09-10 11:53:10	2026-09-10 11:53:10	2026-09-10 11:53:10
6	4	\N	Asset Dipinjam	Asset dipinjam melalui modul peminjaman.	2026-09-14 03:17:33	2026-09-14 03:17:33	2026-09-14 10:17:33.933206
7	4	\N	Asset Dikembalikan	Asset telah dikembalikan melalui modul peminjaman.	2026-09-14 03:27:59	2026-09-14 03:27:59	2026-09-14 10:27:59.000318
8	4	\N	Stock Opname	Stock opname dilakukan. Hasil: Sesuai. Keberadaan: Ada.	2026-09-14 03:47:30	2026-09-14 03:47:30	2026-09-14 10:47:30.851414
9	4	\N	Stock Opname Diperbarui	Stock opname diperbarui. Keberadaan: Rusak → Ada; Hasil: Tidak Sesuai → Sesuai	2026-09-14 04:15:41	2026-09-14 04:15:41	2026-09-14 11:15:41.649908
10	6	1	Asset Dibuat	Asset AST-GIN-0001 - Laptop Lenovo Ideapad Slim 3 berhasil ditambahkan ke sistem.	2026-09-22 13:45:57	2026-09-22 13:45:57	2026-09-22 13:45:57
\.


--
-- Data for Name: asset_loans; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.asset_loans (id, asset_id, user_id, tanggal_pinjam, tanggal_rencana_kembali, tanggal_kembali, keperluan, kondisi_sebelum, kondisi_sesudah, status, catatan, created_at, updated_at) FROM stdin;
1	4	\N	2026-09-14	2026-09-16	2026-09-14	Digunakan untuk keperluan project	Baik	Baik	Dikembalikan	\N	2026-09-14 03:17:33	2026-09-14 03:27:59
\.


--
-- Data for Name: assets; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.assets (id, category_id, location_id, vendor_id, kode_asset, nama_asset, deskripsi, tanggal_perolehan, harga, kondisi, status, created_at, updated_at, merk, tipe, penanggung_jawab) FROM stdin;
5	3	3	2	AST-03	Rak	Meja kayu jati	2026-09-10	150000.00	Baik	Aktif	2026-09-10 11:53:10	2026-09-10 11:53:10	jatikuat	Kayu	Kesekretariatan
4	2	2	2	AST-02	Laptop Lenovo Ideapad	Laptop office	2026-09-08	10000000.00	Baik	Aktif	2026-09-08 07:39:51	2026-09-14 03:27:59	Lenovo	Ideapad	Admin R. Andong
6	2	2	2	AST-GIN-0001	Laptop Lenovo Ideapad Slim 3	Laptop office	2023-10-22	9000000.00	Baik	Aktif	2026-09-22 13:45:57	2026-09-22 13:45:57	Lenovo	Ideapad	Admin R. Andong
\.


--
-- Data for Name: cache; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.cache (key, value, expiration) FROM stdin;
\.


--
-- Data for Name: cache_locks; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.cache_locks (key, owner, expiration) FROM stdin;
\.


--
-- Data for Name: categories; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.categories (id, nama, deskripsi, created_at, updated_at) FROM stdin;
2	IT	Barang-barang IT	2026-09-08 07:31:48	2026-09-08 07:31:48
3	Sekretariat	Barang barang kesekretariatan	2026-09-10 11:49:58	2026-09-10 11:49:58
\.


--
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- Data for Name: job_batches; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.job_batches (id, name, total_jobs, pending_jobs, failed_jobs, failed_job_ids, options, cancelled_at, created_at, finished_at) FROM stdin;
\.


--
-- Data for Name: jobs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.jobs (id, queue, payload, attempts, reserved_at, available_at, created_at) FROM stdin;
\.


--
-- Data for Name: locations; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.locations (id, nama, deskripsi, created_at, updated_at, gedung, lantai, ruangan) FROM stdin;
2	Lab Komputer 1	Ruang laptop dan komputer	2026-09-08 07:33:03	2026-09-08 07:33:03	Gedung 1	Lantai 4	R. Andong
3	Ruang Rapat	Ruang rapat lantai 1 gedung 1	2026-09-10 11:51:06	2026-09-10 11:51:06	Gedung 1	Lantai 1	R. Andong
\.


--
-- Data for Name: maintenance_records; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.maintenance_records (id, asset_id, tanggal, keluhan, biaya, teknisi, status, created_at, updated_at, jenis, kerusakan, hasil_tindakan, kondisi_sebelum, kondisi_sesudah) FROM stdin;
3	4	2026-09-09	Audio tidak jelas	50000.00	Teknisi IT	Selesai	2026-09-09 03:41:17	2026-09-09 04:01:54	Perbaikan	Audio kotor	Pembersihan audio	Baik	Rusak Ringan
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	0001_01_01_000000_create_users_table	1
2	0001_01_01_000001_create_cache_table	1
3	0001_01_01_000002_create_jobs_table	1
4	2026_09_02_065008_create_mahasiswas_table	1
5	2026_09_08_032158_create_categories_table	2
6	2026_09_08_032207_create_locations_table	2
7	2026_09_08_032215_create_vendors_table	2
8	2026_09_08_032222_create_assets_table	2
9	2026_09_08_032228_create_maintenance_records_table	2
10	2026_09_08_032234_create_asset_histories_table	2
11	2026_09_08_033353_create_ai_predictions_table	3
12	2026_09_14_022313_create_procurements_table	4
13	2026_09_14_022316_create_procurement_items_table	4
14	2026_09_14_022640_create_asset_loans_table	4
15	2026_09_14_033511_create_stock_opnames_table	5
16	2026_09_22_000000_create_system_settings_table	6
17	2026_09_22_010000_add_role_to_users_table	7
\.


--
-- Data for Name: password_reset_tokens; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.password_reset_tokens (email, token, created_at) FROM stdin;
\.


--
-- Data for Name: procurement_items; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.procurement_items (id, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: procurements; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.procurements (id, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.sessions (id, user_id, ip_address, user_agent, payload, last_activity) FROM stdin;
ewNr3XveAkLDjlrZf44rNSTqMkTztJG5Dvxhfbnx	1	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36	YTo1OntzOjY6Il90b2tlbiI7czo0MDoiVnYwbXllcGE4QXJ3djRuOUdnWFB1QjBwQ1N4WkxxV016Vjk5QUdlUSI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMwOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvc2V0dGluZ3MiO3M6NToicm91dGUiO3M6MTQ6InNldHRpbmdzLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9	1790301294
zmwkVpEmJf8pbVoSxJJBHlsRRlsYI260Ni1rWUct	1	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36	YTo0OntzOjY6Il90b2tlbiI7czo0MDoicElJbVVDeHhEZjRPbVNyblg3M01ZOTF2YmM0WU9tUmVYNjdUb0w4SSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC91c2VycyI7czo1OiJyb3V0ZSI7czoxMToidXNlcnMuaW5kZXgiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=	1790266866
\.


--
-- Data for Name: stock_opnames; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.stock_opnames (id, asset_id, tanggal_opname, kondisi_sistem, kondisi_aktual, lokasi_sistem, lokasi_aktual, keberadaan, hasil, catatan, user_id, created_at, updated_at) FROM stdin;
1	4	2026-09-14	Baik	Baik	Lab Komputer 1	Ruang IT	Ada	Sesuai	\N	\N	2026-09-14 03:47:30	2026-09-14 04:15:41
\.


--
-- Data for Name: system_settings; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.system_settings (id, organization_name, organization_unit, timezone, date_format, asset_prefix, asset_separator, asset_digits, asset_next_number, logo_path, email_notifications, created_at, updated_at) FROM stdin;
1	PT Global Intermedia Nusantara	Divisi Infrastruktur & IT Ops	Asia/Jakarta (GMT+07:00)	DD/MM/YYYY	AST-GIN	-	4	1	system/r0YMboWbhGGew2eWenYRksizoUAJvhKG5ZxwQMOe.jpg	t	2026-09-22 06:32:37	2026-09-25 08:54:53
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.users (id, name, email, email_verified_at, password, remember_token, created_at, updated_at, role) FROM stdin;
1	PT Global Intermedia Nusantara	admin@admin.com	\N	$2y$12$K4NmCf9TbOhY3A/QBpv1fO4.K7QYyuf1Q4YhVFRZlOrCI5HJmUe3K	\N	2026-09-22 04:35:18	2026-09-22 04:35:18	admin
2	CEO Alexander	pimpinan@gmail.com	\N	$2y$12$SC8ARrpe2lRs7j/pGkPjVezgLpttviGgaM3Gvv7F3GtdiQuYxmJri	\N	2026-09-22 14:04:00	2026-09-22 14:04:00	pemimpin
\.


--
-- Data for Name: vendors; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.vendors (id, nama, kontak, alamat, created_at, updated_at, email) FROM stdin;
2	PT Teknologi Indonesia	089765456786	Kalasan, Sleman, Yogyakarta	2026-09-08 07:33:54	2026-09-08 07:33:54	ti@gmail.com
\.


--
-- Name: ai_predictions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.ai_predictions_id_seq', 1, false);


--
-- Name: asset_histories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.asset_histories_id_seq', 10, true);


--
-- Name: asset_loans_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.asset_loans_id_seq', 1, true);


--
-- Name: assets_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.assets_id_seq', 6, true);


--
-- Name: categories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.categories_id_seq', 3, true);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- Name: jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.jobs_id_seq', 1, false);


--
-- Name: locations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.locations_id_seq', 3, true);


--
-- Name: maintenance_records_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.maintenance_records_id_seq', 3, true);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.migrations_id_seq', 17, true);


--
-- Name: procurement_items_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.procurement_items_id_seq', 1, false);


--
-- Name: procurements_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.procurements_id_seq', 1, false);


--
-- Name: stock_opnames_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.stock_opnames_id_seq', 1, true);


--
-- Name: system_settings_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.system_settings_id_seq', 1, true);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.users_id_seq', 2, true);


--
-- Name: vendors_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.vendors_id_seq', 2, true);


--
-- Name: ai_predictions ai_predictions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ai_predictions
    ADD CONSTRAINT ai_predictions_pkey PRIMARY KEY (id);


--
-- Name: asset_histories asset_histories_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asset_histories
    ADD CONSTRAINT asset_histories_pkey PRIMARY KEY (id);


--
-- Name: asset_loans asset_loans_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asset_loans
    ADD CONSTRAINT asset_loans_pkey PRIMARY KEY (id);


--
-- Name: assets assets_asset_code_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.assets
    ADD CONSTRAINT assets_asset_code_unique UNIQUE (kode_asset);


--
-- Name: assets assets_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.assets
    ADD CONSTRAINT assets_pkey PRIMARY KEY (id);


--
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- Name: categories categories_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categories
    ADD CONSTRAINT categories_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- Name: locations locations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.locations
    ADD CONSTRAINT locations_pkey PRIMARY KEY (id);


--
-- Name: maintenance_records maintenance_records_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.maintenance_records
    ADD CONSTRAINT maintenance_records_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: procurement_items procurement_items_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.procurement_items
    ADD CONSTRAINT procurement_items_pkey PRIMARY KEY (id);


--
-- Name: procurements procurements_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.procurements
    ADD CONSTRAINT procurements_pkey PRIMARY KEY (id);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: stock_opnames stock_opnames_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.stock_opnames
    ADD CONSTRAINT stock_opnames_pkey PRIMARY KEY (id);


--
-- Name: system_settings system_settings_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.system_settings
    ADD CONSTRAINT system_settings_pkey PRIMARY KEY (id);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: vendors vendors_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vendors
    ADD CONSTRAINT vendors_pkey PRIMARY KEY (id);


--
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- Name: ai_predictions ai_predictions_asset_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ai_predictions
    ADD CONSTRAINT ai_predictions_asset_id_foreign FOREIGN KEY (asset_id) REFERENCES public.assets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: asset_histories asset_histories_asset_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asset_histories
    ADD CONSTRAINT asset_histories_asset_id_foreign FOREIGN KEY (asset_id) REFERENCES public.assets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: asset_histories asset_histories_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asset_histories
    ADD CONSTRAINT asset_histories_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: asset_loans asset_loans_asset_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asset_loans
    ADD CONSTRAINT asset_loans_asset_id_foreign FOREIGN KEY (asset_id) REFERENCES public.assets(id) ON DELETE CASCADE;


--
-- Name: asset_loans asset_loans_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asset_loans
    ADD CONSTRAINT asset_loans_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: assets assets_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.assets
    ADD CONSTRAINT assets_category_id_foreign FOREIGN KEY (category_id) REFERENCES public.categories(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: assets assets_location_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.assets
    ADD CONSTRAINT assets_location_id_foreign FOREIGN KEY (location_id) REFERENCES public.locations(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- Name: assets assets_vendor_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.assets
    ADD CONSTRAINT assets_vendor_id_foreign FOREIGN KEY (vendor_id) REFERENCES public.vendors(id) ON DELETE SET NULL;


--
-- Name: maintenance_records maintenance_records_asset_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.maintenance_records
    ADD CONSTRAINT maintenance_records_asset_id_foreign FOREIGN KEY (asset_id) REFERENCES public.assets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: stock_opnames stock_opnames_asset_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.stock_opnames
    ADD CONSTRAINT stock_opnames_asset_id_foreign FOREIGN KEY (asset_id) REFERENCES public.assets(id) ON DELETE CASCADE;


--
-- Name: stock_opnames stock_opnames_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.stock_opnames
    ADD CONSTRAINT stock_opnames_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- PostgreSQL database dump complete
--

\unrestrict tQkvqoBuO9Nqs0sgSSrKIXYdhZjPfCyhA7bqyzOSEb17Lf0dmIOr2dg4U15f0lM

