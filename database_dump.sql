--
-- PostgreSQL database dump
--

-- Dumped from database version 17.5 (Debian 17.5-1.pgdg130+1)
-- Dumped by pg_dump version 17.0

-- Started on 2026-01-28 16:35:12

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
-- TOC entry 222 (class 1259 OID 49188)
-- Name: exercises; Type: TABLE; Schema: public; Owner: docker
--

CREATE TABLE public.exercises (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    type character varying(50) NOT NULL
);


ALTER TABLE public.exercises OWNER TO docker;

--
-- TOC entry 221 (class 1259 OID 49187)
-- Name: exercises_id_seq; Type: SEQUENCE; Schema: public; Owner: docker
--

CREATE SEQUENCE public.exercises_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.exercises_id_seq OWNER TO docker;

--
-- TOC entry 3533 (class 0 OID 0)
-- Dependencies: 221
-- Name: exercises_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: docker
--

ALTER SEQUENCE public.exercises_id_seq OWNED BY public.exercises.id;


--
-- TOC entry 234 (class 1259 OID 49378)
-- Name: login_attempts; Type: TABLE; Schema: public; Owner: docker
--

CREATE TABLE public.login_attempts (
    id integer NOT NULL,
    ip_address character varying(45) NOT NULL,
    attempts integer DEFAULT 1,
    last_attempt timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.login_attempts OWNER TO docker;

--
-- TOC entry 233 (class 1259 OID 49377)
-- Name: login_attempts_id_seq; Type: SEQUENCE; Schema: public; Owner: docker
--

CREATE SEQUENCE public.login_attempts_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.login_attempts_id_seq OWNER TO docker;

--
-- TOC entry 3534 (class 0 OID 0)
-- Dependencies: 233
-- Name: login_attempts_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: docker
--

ALTER SEQUENCE public.login_attempts_id_seq OWNED BY public.login_attempts.id;


--
-- TOC entry 226 (class 1259 OID 49248)
-- Name: plan_exercises; Type: TABLE; Schema: public; Owner: docker
--

CREATE TABLE public.plan_exercises (
    id integer NOT NULL,
    workout_plan_id integer NOT NULL,
    exercise_id integer NOT NULL,
    order_index integer DEFAULT 0,
    sets_count integer DEFAULT 3 NOT NULL,
    note text
);


ALTER TABLE public.plan_exercises OWNER TO docker;

--
-- TOC entry 225 (class 1259 OID 49247)
-- Name: plan_exercises_id_seq; Type: SEQUENCE; Schema: public; Owner: docker
--

CREATE SEQUENCE public.plan_exercises_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.plan_exercises_id_seq OWNER TO docker;

--
-- TOC entry 3535 (class 0 OID 0)
-- Dependencies: 225
-- Name: plan_exercises_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: docker
--

ALTER SEQUENCE public.plan_exercises_id_seq OWNED BY public.plan_exercises.id;


--
-- TOC entry 218 (class 1259 OID 49165)
-- Name: roles; Type: TABLE; Schema: public; Owner: docker
--

CREATE TABLE public.roles (
    id integer NOT NULL,
    name character varying(100) NOT NULL
);


ALTER TABLE public.roles OWNER TO docker;

--
-- TOC entry 217 (class 1259 OID 49164)
-- Name: roles_id_seq; Type: SEQUENCE; Schema: public; Owner: docker
--

CREATE SEQUENCE public.roles_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.roles_id_seq OWNER TO docker;

--
-- TOC entry 3536 (class 0 OID 0)
-- Dependencies: 217
-- Name: roles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: docker
--

ALTER SEQUENCE public.roles_id_seq OWNED BY public.roles.id;


--
-- TOC entry 228 (class 1259 OID 49302)
-- Name: trainer_trainees; Type: TABLE; Schema: public; Owner: docker
--

CREATE TABLE public.trainer_trainees (
    id integer NOT NULL,
    trainer_id integer NOT NULL,
    trainee_id integer NOT NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    status character varying(20) DEFAULT 'pending'::character varying
);


ALTER TABLE public.trainer_trainees OWNER TO docker;

--
-- TOC entry 227 (class 1259 OID 49301)
-- Name: trainer_trainees_id_seq; Type: SEQUENCE; Schema: public; Owner: docker
--

CREATE SEQUENCE public.trainer_trainees_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.trainer_trainees_id_seq OWNER TO docker;

--
-- TOC entry 3537 (class 0 OID 0)
-- Dependencies: 227
-- Name: trainer_trainees_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: docker
--

ALTER SEQUENCE public.trainer_trainees_id_seq OWNED BY public.trainer_trainees.id;


--
-- TOC entry 220 (class 1259 OID 49172)
-- Name: users; Type: TABLE; Schema: public; Owner: docker
--

CREATE TABLE public.users (
    id integer NOT NULL,
    name character varying(100) NOT NULL,
    surname character varying(100) NOT NULL,
    email character varying(255) NOT NULL,
    password character varying(255) NOT NULL,
    role_id integer NOT NULL
);


ALTER TABLE public.users OWNER TO docker;

--
-- TOC entry 219 (class 1259 OID 49171)
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: docker
--

CREATE SEQUENCE public.users_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.users_id_seq OWNER TO docker;

--
-- TOC entry 3538 (class 0 OID 0)
-- Dependencies: 219
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: docker
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- TOC entry 232 (class 1259 OID 49361)
-- Name: workout_logs; Type: TABLE; Schema: public; Owner: docker
--

CREATE TABLE public.workout_logs (
    id integer NOT NULL,
    workout_session_id integer NOT NULL,
    plan_exercise_id integer NOT NULL,
    set_number integer NOT NULL,
    weight numeric(5,2),
    reps integer,
    time_seconds integer,
    distance_km numeric(5,2)
);


ALTER TABLE public.workout_logs OWNER TO docker;

--
-- TOC entry 231 (class 1259 OID 49360)
-- Name: workout_logs_id_seq; Type: SEQUENCE; Schema: public; Owner: docker
--

CREATE SEQUENCE public.workout_logs_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.workout_logs_id_seq OWNER TO docker;

--
-- TOC entry 3539 (class 0 OID 0)
-- Dependencies: 231
-- Name: workout_logs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: docker
--

ALTER SEQUENCE public.workout_logs_id_seq OWNED BY public.workout_logs.id;


--
-- TOC entry 224 (class 1259 OID 49197)
-- Name: workout_plans; Type: TABLE; Schema: public; Owner: docker
--

CREATE TABLE public.workout_plans (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    user_id integer NOT NULL,
    trainer_id integer,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.workout_plans OWNER TO docker;

--
-- TOC entry 223 (class 1259 OID 49196)
-- Name: workout_plans_id_seq; Type: SEQUENCE; Schema: public; Owner: docker
--

CREATE SEQUENCE public.workout_plans_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.workout_plans_id_seq OWNER TO docker;

--
-- TOC entry 3540 (class 0 OID 0)
-- Dependencies: 223
-- Name: workout_plans_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: docker
--

ALTER SEQUENCE public.workout_plans_id_seq OWNED BY public.workout_plans.id;


--
-- TOC entry 230 (class 1259 OID 49346)
-- Name: workout_sessions; Type: TABLE; Schema: public; Owner: docker
--

CREATE TABLE public.workout_sessions (
    id integer NOT NULL,
    workout_plan_id integer NOT NULL,
    date timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    user_note text
);


ALTER TABLE public.workout_sessions OWNER TO docker;

--
-- TOC entry 229 (class 1259 OID 49345)
-- Name: workout_sessions_id_seq; Type: SEQUENCE; Schema: public; Owner: docker
--

CREATE SEQUENCE public.workout_sessions_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.workout_sessions_id_seq OWNER TO docker;

--
-- TOC entry 3541 (class 0 OID 0)
-- Dependencies: 229
-- Name: workout_sessions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: docker
--

ALTER SEQUENCE public.workout_sessions_id_seq OWNED BY public.workout_sessions.id;


--
-- TOC entry 3316 (class 2604 OID 49191)
-- Name: exercises id; Type: DEFAULT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.exercises ALTER COLUMN id SET DEFAULT nextval('public.exercises_id_seq'::regclass);


--
-- TOC entry 3328 (class 2604 OID 49381)
-- Name: login_attempts id; Type: DEFAULT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.login_attempts ALTER COLUMN id SET DEFAULT nextval('public.login_attempts_id_seq'::regclass);


--
-- TOC entry 3319 (class 2604 OID 49251)
-- Name: plan_exercises id; Type: DEFAULT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.plan_exercises ALTER COLUMN id SET DEFAULT nextval('public.plan_exercises_id_seq'::regclass);


--
-- TOC entry 3314 (class 2604 OID 49168)
-- Name: roles id; Type: DEFAULT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.roles ALTER COLUMN id SET DEFAULT nextval('public.roles_id_seq'::regclass);


--
-- TOC entry 3322 (class 2604 OID 49305)
-- Name: trainer_trainees id; Type: DEFAULT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.trainer_trainees ALTER COLUMN id SET DEFAULT nextval('public.trainer_trainees_id_seq'::regclass);


--
-- TOC entry 3315 (class 2604 OID 49175)
-- Name: users id; Type: DEFAULT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- TOC entry 3327 (class 2604 OID 49364)
-- Name: workout_logs id; Type: DEFAULT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.workout_logs ALTER COLUMN id SET DEFAULT nextval('public.workout_logs_id_seq'::regclass);


--
-- TOC entry 3317 (class 2604 OID 49200)
-- Name: workout_plans id; Type: DEFAULT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.workout_plans ALTER COLUMN id SET DEFAULT nextval('public.workout_plans_id_seq'::regclass);


--
-- TOC entry 3325 (class 2604 OID 49349)
-- Name: workout_sessions id; Type: DEFAULT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.workout_sessions ALTER COLUMN id SET DEFAULT nextval('public.workout_sessions_id_seq'::regclass);


--
-- TOC entry 3515 (class 0 OID 49188)
-- Dependencies: 222
-- Data for Name: exercises; Type: TABLE DATA; Schema: public; Owner: docker
--

COPY public.exercises (id, name, type) FROM stdin;
1	Wyciskanie sztangi leżąc	weight_reps
2	Przysiady ze sztangą	weight_reps
3	Martwy ciąg	weight_reps
4	Bieganie na bieżni	time_distance
5	Deska (Plank)	time_only
6	Pompki klasyczne	reps_only
7	Bieganie	weight_reps
8	test	weight_reps
9	test123	time_only
\.


--
-- TOC entry 3527 (class 0 OID 49378)
-- Dependencies: 234
-- Data for Name: login_attempts; Type: TABLE DATA; Schema: public; Owner: docker
--

COPY public.login_attempts (id, ip_address, attempts, last_attempt) FROM stdin;
\.


--
-- TOC entry 3519 (class 0 OID 49248)
-- Dependencies: 226
-- Data for Name: plan_exercises; Type: TABLE DATA; Schema: public; Owner: docker
--

COPY public.plan_exercises (id, workout_plan_id, exercise_id, order_index, sets_count, note) FROM stdin;
3	7	3	0	3	
4	7	1	0	3	asd
6	7	8	0	2	
7	7	8	0	3	
8	7	8	0	3	
9	7	9	0	3	
10	7	4	0	2	Lekka przebieżka max 5 minut
13	8	3	0	1	Uwaga
14	8	1	0	1	Notatka
15	9	7	0	3	Note for running
16	10	5	0	1	Light
17	10	6	0	2	
19	10	5	0	3	ostatnia seria
22	13	3	0	2	Proste plecy
23	13	6	0	1	Szybkie powtórzenia
\.


--
-- TOC entry 3511 (class 0 OID 49165)
-- Dependencies: 218
-- Data for Name: roles; Type: TABLE DATA; Schema: public; Owner: docker
--

COPY public.roles (id, name) FROM stdin;
1	trainee
2	trainer
\.


--
-- TOC entry 3521 (class 0 OID 49302)
-- Dependencies: 228
-- Data for Name: trainer_trainees; Type: TABLE DATA; Schema: public; Owner: docker
--

COPY public.trainer_trainees (id, trainer_id, trainee_id, created_at, status) FROM stdin;
4	2	1	2026-01-12 09:11:04.995363	accepted
5	2	3	2026-01-27 09:19:01.293312	accepted
6	4	1	2026-01-28 15:30:06.456326	accepted
\.


--
-- TOC entry 3513 (class 0 OID 49172)
-- Dependencies: 220
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: docker
--

COPY public.users (id, name, surname, email, password, role_id) FROM stdin;
1	Piotr	Gąska	04piotr04@gmail.com	$2y$10$r7aHZ6AG.KsIsI4Tkn1fdOyYRHzq0ShSwlrGXVGgR7GvqmHFqWi/q	1
2	Piotr	Gąska	test@gmail.com	$2y$10$RJnm4GdEqZsA/eJvazUItePROAZnLuUBCAL6MtF9eoF8Y15QuYkHe	2
3	John	Doe	user@example.com	$2y$10$yQheS.piUln7KTuojgRf2OCCfwaFQ0nAoaaSb8mWWgohVuWt8ISjq	1
4	John	Doe	johndoe@example.com	$2y$10$NFL7LqtGV1euD/KGkVNcsuj.Ne2I649/PXnhZzqCLX4vIPUS1OVoS	2
\.


--
-- TOC entry 3525 (class 0 OID 49361)
-- Dependencies: 232
-- Data for Name: workout_logs; Type: TABLE DATA; Schema: public; Owner: docker
--

COPY public.workout_logs (id, workout_session_id, plan_exercise_id, set_number, weight, reps, time_seconds, distance_km) FROM stdin;
24	22	15	1	10.00	10	\N	\N
25	22	15	2	10.00	10	\N	\N
26	22	15	3	10.00	10	\N	\N
31	24	16	1	\N	\N	10	\N
32	24	17	1	\N	10	\N	\N
33	24	17	2	\N	10	\N	\N
35	28	13	1	10.00	10	\N	\N
36	28	14	1	10.00	10	\N	\N
37	29	13	1	10.00	10	\N	\N
38	29	14	1	10.00	10	\N	\N
39	30	15	1	20.00	20	\N	\N
40	30	15	2	20.00	20	\N	\N
41	30	15	3	20.00	20	\N	\N
42	31	15	1	30.00	30	\N	\N
43	31	15	2	30.00	30	\N	\N
44	31	15	3	30.00	30	\N	\N
\.


--
-- TOC entry 3517 (class 0 OID 49197)
-- Dependencies: 224
-- Data for Name: workout_plans; Type: TABLE DATA; Schema: public; Owner: docker
--

COPY public.workout_plans (id, name, user_id, trainer_id, created_at) FROM stdin;
2	SPLIT	2	\N	2026-01-12 09:38:16.161917
4	SPLIT 123	2	\N	2026-01-12 09:38:57.046262
7	123	1	2	2026-01-12 17:13:46.400941
8	FBW	1	\N	2026-01-13 11:49:25.121044
9	SPLIT	1	\N	2026-01-20 07:14:33.322566
10	Trainer plan	1	2	2026-01-20 07:16:18.547917
13	FBW1 	1	4	2026-01-28 15:30:41.649329
\.


--
-- TOC entry 3523 (class 0 OID 49346)
-- Dependencies: 230
-- Data for Name: workout_sessions; Type: TABLE DATA; Schema: public; Owner: docker
--

COPY public.workout_sessions (id, workout_plan_id, date, user_note) FROM stdin;
22	9	2026-01-20 07:15:16.941013	felt bad
24	10	2026-01-20 07:17:56.583768	
28	8	2026-01-20 08:48:49.365179	felt strong
29	8	2026-01-20 08:49:11.73622	felt bad
30	9	2026-01-20 09:00:24.117644	felt bad
31	9	2026-01-20 09:05:52.453922	aaaaaaaaa aaaaaaaa aaaaaaaa aaaaa aaaa aaaaa aaaaaa aaaaaaa aaaaaa aaaaaa aaaaa aaaaa a
32	9	2026-01-20 09:35:23.224794	Miałem dzisiaj całkiem średni dzień, ale myślę że następnym razem trening będzie lepszy.
\.


--
-- TOC entry 3542 (class 0 OID 0)
-- Dependencies: 221
-- Name: exercises_id_seq; Type: SEQUENCE SET; Schema: public; Owner: docker
--

SELECT pg_catalog.setval('public.exercises_id_seq', 9, true);


--
-- TOC entry 3543 (class 0 OID 0)
-- Dependencies: 233
-- Name: login_attempts_id_seq; Type: SEQUENCE SET; Schema: public; Owner: docker
--

SELECT pg_catalog.setval('public.login_attempts_id_seq', 2, true);


--
-- TOC entry 3544 (class 0 OID 0)
-- Dependencies: 225
-- Name: plan_exercises_id_seq; Type: SEQUENCE SET; Schema: public; Owner: docker
--

SELECT pg_catalog.setval('public.plan_exercises_id_seq', 23, true);


--
-- TOC entry 3545 (class 0 OID 0)
-- Dependencies: 217
-- Name: roles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: docker
--

SELECT pg_catalog.setval('public.roles_id_seq', 2, true);


--
-- TOC entry 3546 (class 0 OID 0)
-- Dependencies: 227
-- Name: trainer_trainees_id_seq; Type: SEQUENCE SET; Schema: public; Owner: docker
--

SELECT pg_catalog.setval('public.trainer_trainees_id_seq', 6, true);


--
-- TOC entry 3547 (class 0 OID 0)
-- Dependencies: 219
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: docker
--

SELECT pg_catalog.setval('public.users_id_seq', 4, true);


--
-- TOC entry 3548 (class 0 OID 0)
-- Dependencies: 231
-- Name: workout_logs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: docker
--

SELECT pg_catalog.setval('public.workout_logs_id_seq', 59, true);


--
-- TOC entry 3549 (class 0 OID 0)
-- Dependencies: 223
-- Name: workout_plans_id_seq; Type: SEQUENCE SET; Schema: public; Owner: docker
--

SELECT pg_catalog.setval('public.workout_plans_id_seq', 13, true);


--
-- TOC entry 3550 (class 0 OID 0)
-- Dependencies: 229
-- Name: workout_sessions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: docker
--

SELECT pg_catalog.setval('public.workout_sessions_id_seq', 36, true);


--
-- TOC entry 3338 (class 2606 OID 49195)
-- Name: exercises exercises_name_key; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.exercises
    ADD CONSTRAINT exercises_name_key UNIQUE (name);


--
-- TOC entry 3340 (class 2606 OID 49193)
-- Name: exercises exercises_pkey; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.exercises
    ADD CONSTRAINT exercises_pkey PRIMARY KEY (id);


--
-- TOC entry 3354 (class 2606 OID 49385)
-- Name: login_attempts login_attempts_pkey; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.login_attempts
    ADD CONSTRAINT login_attempts_pkey PRIMARY KEY (id);


--
-- TOC entry 3344 (class 2606 OID 49257)
-- Name: plan_exercises plan_exercises_pkey; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.plan_exercises
    ADD CONSTRAINT plan_exercises_pkey PRIMARY KEY (id);


--
-- TOC entry 3332 (class 2606 OID 49170)
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);


--
-- TOC entry 3346 (class 2606 OID 49308)
-- Name: trainer_trainees trainer_trainees_pkey; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.trainer_trainees
    ADD CONSTRAINT trainer_trainees_pkey PRIMARY KEY (id);


--
-- TOC entry 3348 (class 2606 OID 49310)
-- Name: trainer_trainees unique_trainer_trainee; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.trainer_trainees
    ADD CONSTRAINT unique_trainer_trainee UNIQUE (trainer_id, trainee_id);


--
-- TOC entry 3334 (class 2606 OID 49181)
-- Name: users users_email_key; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_key UNIQUE (email);


--
-- TOC entry 3336 (class 2606 OID 49179)
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- TOC entry 3352 (class 2606 OID 49366)
-- Name: workout_logs workout_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.workout_logs
    ADD CONSTRAINT workout_logs_pkey PRIMARY KEY (id);


--
-- TOC entry 3342 (class 2606 OID 49204)
-- Name: workout_plans workout_plans_pkey; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.workout_plans
    ADD CONSTRAINT workout_plans_pkey PRIMARY KEY (id);


--
-- TOC entry 3350 (class 2606 OID 49354)
-- Name: workout_sessions workout_sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.workout_sessions
    ADD CONSTRAINT workout_sessions_pkey PRIMARY KEY (id);


--
-- TOC entry 3358 (class 2606 OID 49263)
-- Name: plan_exercises fk_pe_exercise; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.plan_exercises
    ADD CONSTRAINT fk_pe_exercise FOREIGN KEY (exercise_id) REFERENCES public.exercises(id);


--
-- TOC entry 3359 (class 2606 OID 49258)
-- Name: plan_exercises fk_pe_plan; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.plan_exercises
    ADD CONSTRAINT fk_pe_plan FOREIGN KEY (workout_plan_id) REFERENCES public.workout_plans(id) ON DELETE CASCADE;


--
-- TOC entry 3360 (class 2606 OID 49316)
-- Name: trainer_trainees fk_tt_trainee; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.trainer_trainees
    ADD CONSTRAINT fk_tt_trainee FOREIGN KEY (trainee_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- TOC entry 3361 (class 2606 OID 49311)
-- Name: trainer_trainees fk_tt_trainer; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.trainer_trainees
    ADD CONSTRAINT fk_tt_trainer FOREIGN KEY (trainer_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- TOC entry 3355 (class 2606 OID 49182)
-- Name: users fk_users_roles; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT fk_users_roles FOREIGN KEY (role_id) REFERENCES public.roles(id);


--
-- TOC entry 3356 (class 2606 OID 49210)
-- Name: workout_plans fk_wp_trainer; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.workout_plans
    ADD CONSTRAINT fk_wp_trainer FOREIGN KEY (trainer_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 3357 (class 2606 OID 49205)
-- Name: workout_plans fk_wp_user; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.workout_plans
    ADD CONSTRAINT fk_wp_user FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- TOC entry 3363 (class 2606 OID 49372)
-- Name: workout_logs workout_logs_plan_exercise_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.workout_logs
    ADD CONSTRAINT workout_logs_plan_exercise_id_fkey FOREIGN KEY (plan_exercise_id) REFERENCES public.plan_exercises(id) ON DELETE CASCADE;


--
-- TOC entry 3364 (class 2606 OID 49367)
-- Name: workout_logs workout_logs_workout_session_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.workout_logs
    ADD CONSTRAINT workout_logs_workout_session_id_fkey FOREIGN KEY (workout_session_id) REFERENCES public.workout_sessions(id) ON DELETE CASCADE;


--
-- TOC entry 3362 (class 2606 OID 49355)
-- Name: workout_sessions workout_sessions_workout_plan_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.workout_sessions
    ADD CONSTRAINT workout_sessions_workout_plan_id_fkey FOREIGN KEY (workout_plan_id) REFERENCES public.workout_plans(id) ON DELETE CASCADE;


-- Completed on 2026-01-28 16:35:12

--
-- PostgreSQL database dump complete
--

