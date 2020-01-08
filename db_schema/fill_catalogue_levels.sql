--
-- PostgreSQL database dump
--

-- Dumped from database version 9.6.16
-- Dumped by pg_dump version 12.0

-- Started on 2020-01-08 20:31:58

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

--
-- TOC entry 5350 (class 0 OID 6134376)
-- Dependencies: 234
-- Data for Name: catalogue_levels; Type: TABLE DATA; Schema: darwin2; Owner: darwin2
--

COPY darwin2.catalogue_levels (id, level_type, level_name, level_sys_name, optional_level, level_order) FROM stdin;
1	taxonomy	domain	domain	f	1
2	taxonomy	kingdom	kingdom	f	2
3	taxonomy	super phylum	super_phylum	t	3
4	taxonomy	phylum	phylum	f	4
5	taxonomy	sub phylum	sub_phylum	t	5
6	taxonomy	infra phylum	infra_phylum	t	6
7	taxonomy	super cohort - botany	super_cohort_botany	t	7
8	taxonomy	cohort - botany	cohort_botany	t	8
9	taxonomy	sub cohort - botany	sub_cohort_botany	t	9
10	taxonomy	infra cohort - botany	infra_cohort_botany	t	10
11	taxonomy	super class	super_class	t	11
12	taxonomy	class	class	f	12
13	taxonomy	sub class	sub_class	t	13
14	taxonomy	infra class	infra_class	t	14
15	taxonomy	super division	super_division	t	15
16	taxonomy	division	division	t	16
17	taxonomy	sub division	sub_division	t	17
18	taxonomy	infra division	infra_division	t	18
19	taxonomy	super legion	super_legion	t	19
20	taxonomy	legion	legion	t	20
21	taxonomy	sub legion	sub_legion	t	21
22	taxonomy	infra legion	infra_legion	t	22
23	taxonomy	super cohort - zoology	super_cohort_zoology	t	23
24	taxonomy	cohort - zoology	cohort_zoology	t	24
25	taxonomy	sub cohort - zoology	sub_cohort_zoology	t	25
26	taxonomy	infra cohort - zoology	infra_cohort_zoology	t	26
27	taxonomy	super order	super_order	t	27
28	taxonomy	order	order	f	28
29	taxonomy	sub order	sub_order	t	29
30	taxonomy	infra order	infra_order	t	30
31	taxonomy	section - zoology	section_zoology	t	31
32	taxonomy	sub section - zoology	sub_section_zoology	t	32
33	taxonomy	super family	super_family	t	33
34	taxonomy	family	family	f	34
35	taxonomy	sub family	sub_family	t	35
36	taxonomy	infra family	infra_family	t	36
37	taxonomy	super tribe	super_tribe	t	37
38	taxonomy	tribe	tribe	t	38
39	taxonomy	sub tribe	sub_tribe	t	39
40	taxonomy	infra tribe	infra_tribe	t	40
41	taxonomy	genus	genus	f	41
42	taxonomy	sub genus	sub_genus	t	42
43	taxonomy	section - botany	section_botany	t	43
44	taxonomy	sub section - botany	sub_section_botany	t	44
45	taxonomy	serie	serie	t	45
46	taxonomy	sub serie	sub_serie	t	46
47	taxonomy	super species	super_species	t	47
48	taxonomy	species	species	f	48
49	taxonomy	sub species	sub_species	t	49
50	taxonomy	variety	variety	t	50
51	taxonomy	sub variety	sub_variety	t	51
52	taxonomy	form	form	t	52
53	taxonomy	sub form	sub_form	t	53
54	taxonomy	abberans	abberans	t	54
55	chronostratigraphy	eon	eon	f	55
56	chronostratigraphy	era	era	f	56
57	chronostratigraphy	sub era	sub_era	t	57
58	chronostratigraphy	system	system	f	58
59	chronostratigraphy	serie	serie	f	59
60	chronostratigraphy	stage	stage	f	60
61	chronostratigraphy	sub stage	sub_stage	t	61
62	chronostratigraphy	sub level 1	sub_level_1	t	62
63	chronostratigraphy	sub level 2	sub_level_2	t	63
64	lithostratigraphy	group	group	f	65
65	lithostratigraphy	formation	formation	f	66
66	lithostratigraphy	member	member	f	67
67	lithostratigraphy	layer	layer	f	68
68	lithostratigraphy	sub level 1	sub_level_1	t	69
69	lithostratigraphy	sub level 2	sub_level_2	t	70
70	mineralogy	class	unit_class	f	71
75	lithology	main group	unit_main_group	f	76
76	lithology	group	unit_group	f	77
77	lithology	sub group	unit_sub_group	t	78
78	lithology	rock	unit_rock	f	79
83	lithology	category	unit_category	f	84
71	mineralogy	sub class	unit_sub_class	f	72
72	mineralogy	series	unit_series	f	73
73	mineralogy	variety	unit_variety	f	74
84	lithology	main class	unit_main_class	f	80
85	lithostratigraphy	supergroup	supergroup	f	64
79	lithology	class	unit_class	t	81
80	lithology	clan	unit_clan	t	82
\.


-- Completed on 2020-01-08 20:31:59

--
-- PostgreSQL database dump complete
--

