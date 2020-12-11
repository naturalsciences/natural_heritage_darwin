delete from users;
alter sequence users_id_seq restart;
insert into users 
(family_name, db_user_type)
values ('admin',8);

delete from users_login_infos;
alter sequence users_login_info_id_seq restart;
insert into users_login_infos
(user_Ref, login_type, user_name, password)
values(1, 'local', 'admin', sha1('CONFIG_SALTPASSWD'));



--
-- PostgreSQL database dump
--

-- Dumped from database version 9.6.15
-- Dumped by pg_dump version 12.2

-- Started on 2020-07-07 15:29:56
/*
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
*/
--
-- TOC entry 5724 (class 0 OID 5740951)
-- Dependencies: 729
-- Data for Name: my_widgets_bck_admin; Type: TABLE DATA; Schema: darwin2; Owner: darwin2
--

INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'board_widget', 'addSpecimen', 2, 1, false, true, true, '#5BAABD', true, NULL, 'Add new specimen', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'board_widget', 'addTaxon', 1, 1, false, true, true, '#5BAABD', true, NULL, 'Add new taxon', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'board_widget', 'myChangesPlotted', 0, 1, false, true, true, '#5BAABD', true, NULL, 'My activity', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'board_widget', 'myLastsItems', 0, 1, false, true, true, '#5BAABD', true, NULL, 'My Last Actions', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'board_widget', 'myLoans', 3, 1, false, true, true, '#5BAABD', true, NULL, 'My loans', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'board_widget', 'news', 0, 2, false, true, true, '#5BAABD', true, NULL, 'News', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'board_widget', 'savedSearch', 0, 2, false, true, false, '#5BAABD', true, NULL, 'My saved searches', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'board_widget', 'savedSpecimens', 0, 2, false, true, true, '#5BAABD', true, NULL, 'My saved specimens', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'board_widget', 'stats', 0, 1, false, true, true, '#5BAABD', true, NULL, 'Statistics', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'board_widget', 'workflowsSummary', 2, 2, false, false, false, '#5BAABD', true, NULL, 'Workflows summary', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_bibliography_widget', 'comment', 6, 1, false, false, false, '#5BAABD', true, NULL, 'Comments', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_bibliography_widget', 'extLinks', 8, 1, false, false, false, '#5BAABD', true, NULL, 'External Links', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_bibliography_widget', 'keywords', 2, 1, false, true, true, '#5BAABD', true, NULL, 'Keywords', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_bibliography_widget', 'properties', 7, 1, false, false, false, '#5BAABD', true, NULL, 'Properties', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_bibliography_widget', 'relatedFiles', 0, 1, false, true, true, '#5BAABD', true, NULL, 'Related Files', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_chronostratigraphy_widget', 'biblio', 5, 1, false, true, true, '#5BAABD', true, NULL, 'Bibliography', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_chronostratigraphy_widget', 'cataloguePeople', 1, 1, false, true, true, '#5BAABD', true, NULL, 'People', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_chronostratigraphy_widget', 'comment', 5, 1, false, false, false, '#5BAABD', true, NULL, 'Comments', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_chronostratigraphy_widget', 'extLinks', 0, 1, false, false, false, '#5BAABD', true, NULL, 'External Links', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_chronostratigraphy_widget', 'informativeWorkflow', 8, 1, false, true, true, '#5BAABD', true, NULL, 'Suggestions / Report problem', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_chronostratigraphy_widget', 'properties', 0, 1, false, true, false, '#5BAABD', true, NULL, 'Properties', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_chronostratigraphy_widget', 'relatedFiles', 6, 1, false, true, true, '#5BAABD', true, NULL, 'Related Files', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_chronostratigraphy_widget', 'synonym', 3, 1, false, true, true, '#5BAABD', true, NULL, 'Synonymies', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_chronostratigraphy_widget', 'vernacularNames', 4, 1, false, true, true, '#5BAABD', true, NULL, 'Vernacular names', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_collections_widget', 'collectionsCodes', 0, 1, false, true, true, '#5BAABD', true, NULL, 'Default specimens codes', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_collections_widget', 'comment', 0, 1, false, true, true, '#5BAABD', true, NULL, 'Comments', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_collections_widget', 'extLinks', 3, 1, false, true, true, '#5BAABD', true, NULL, 'External link', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_expeditions_widget', 'comment', 0, 1, false, true, true, '#5BAABD', true, NULL, 'Comments', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_expeditions_widget', 'extLinks', 1, 1, false, true, true, '#5BAABD', true, NULL, 'External Links', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_expeditions_widget', 'relatedFiles', 2, 1, false, true, true, '#5BAABD', true, NULL, 'Related Files', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_gtu_widget', 'comment', 1, 1, false, true, true, '#5BAABD', true, NULL, 'Comments', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_gtu_widget', 'extLinks', 0, 1, false, true, true, '#5BAABD', true, NULL, 'External Links', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_gtu_widget', 'informativeWorkflow', 4, 1, false, true, true, '#5BAABD', true, NULL, 'Suggestions / Report problem', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_gtu_widget', 'properties', 2, 1, false, true, true, '#5BAABD', true, NULL, 'Properties', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_gtu_widget', 'relatedFiles', 5, 1, false, true, true, '#5BAABD', true, NULL, 'Related Files', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_igs_widget', 'comment', 0, 1, false, true, true, '#5BAABD', true, NULL, 'Comments', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_igs_widget', 'extLinks', 2, 1, false, true, true, '#5BAABD', true, NULL, 'External Links', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_igs_widget', 'insurances', 0, 1, false, true, true, '#5BAABD', true, NULL, 'Insurances', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_igs_widget', 'relatedFiles', 3, 1, false, true, true, '#5BAABD', true, NULL, 'Related Files', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_lithology_widget', 'biblio', 5, 1, false, true, true, '#5BAABD', true, NULL, 'Bibliography', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_lithology_widget', 'cataloguePeople', 1, 1, false, true, true, '#5BAABD', true, NULL, 'People', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_lithology_widget', 'comment', 5, 1, false, false, false, '#5BAABD', true, NULL, 'Comments', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_lithology_widget', 'extLinks', 7, 1, false, false, false, '#5BAABD', true, NULL, 'External Links', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_lithology_widget', 'informativeWorkflow', 6, 1, false, true, true, '#5BAABD', true, NULL, 'Suggestions / Report problem', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_lithology_widget', 'properties', 6, 1, false, false, false, '#5BAABD', true, NULL, 'Properties', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_lithology_widget', 'relatedFiles', 6, 1, false, true, true, '#5BAABD', true, NULL, 'Related Files', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_lithology_widget', 'synonym', 3, 1, false, true, true, '#5BAABD', true, NULL, 'Synonymies', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_lithology_widget', 'vernacularNames', 5, 1, false, true, true, '#5BAABD', true, NULL, 'Vernacular names', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_lithostratigraphy_widget', 'biblio', 5, 1, false, true, true, '#5BAABD', true, NULL, 'Bibliography', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_lithostratigraphy_widget', 'cataloguePeople', 1, 1, false, true, true, '#5BAABD', true, NULL, 'People', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_lithostratigraphy_widget', 'comment', 5, 1, false, false, false, '#5BAABD', true, NULL, 'Comments', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_lithostratigraphy_widget', 'extLinks', 7, 1, false, false, false, '#5BAABD', true, NULL, 'External Links', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_lithostratigraphy_widget', 'informativeWorkflow', 8, 1, false, true, true, '#5BAABD', true, NULL, 'Suggestions / Report problem', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_lithostratigraphy_widget', 'properties', 6, 1, false, false, false, '#5BAABD', true, NULL, 'Properties', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_lithostratigraphy_widget', 'relatedFiles', 6, 1, false, true, true, '#5BAABD', true, NULL, 'Related Files', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_lithostratigraphy_widget', 'synonym', 3, 1, false, true, true, '#5BAABD', true, NULL, 'Synonymies', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_lithostratigraphy_widget', 'vernacularNames', 4, 1, false, true, true, '#5BAABD', true, NULL, 'Vernacular names', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_methods_and_tools_widget', 'comment', 2, 1, false, true, true, '#5BAABD', true, NULL, 'Comments', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_methods_and_tools_widget', 'extLinks', 3, 1, false, true, true, '#5BAABD', true, NULL, 'External Links', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_methods_and_tools_widget', 'informativeWorkflow', 1, 1, false, true, true, '#5BAABD', true, NULL, 'Suggestions / Report problem', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_methods_and_tools_widget', 'properties', 1, 1, false, true, true, '#5BAABD', true, NULL, 'Properties', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_mineralogy_widget', 'biblio', 6, 1, false, true, true, '#5BAABD', true, NULL, 'Bibliography', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_mineralogy_widget', 'cataloguePeople', 1, 1, false, true, true, '#5BAABD', true, NULL, 'People', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_mineralogy_widget', 'comment', 5, 1, false, false, false, '#5BAABD', true, NULL, 'Comments', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_mineralogy_widget', 'extLinks', 7, 1, false, false, false, '#5BAABD', true, NULL, 'External Links', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_mineralogy_widget', 'informativeWorkflow', 6, 1, false, true, true, '#5BAABD', true, NULL, 'Suggestions / Report problem', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_mineralogy_widget', 'keywords', 5, 1, false, true, true, '#5BAABD', true, NULL, 'Keywords', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_mineralogy_widget', 'properties', 6, 1, false, false, false, '#5BAABD', true, NULL, 'Properties', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_mineralogy_widget', 'relatedFiles', 7, 1, false, true, true, '#5BAABD', true, NULL, 'Related Files', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_mineralogy_widget', 'synonym', 3, 1, false, true, true, '#5BAABD', true, NULL, 'Synonymies', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_mineralogy_widget', 'vernacularNames', 4, 1, false, true, true, '#5BAABD', true, NULL, 'Vernacular names', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_taxonomy_widget', 'biblio', 10, 1, false, true, true, '#5BAABD', true, NULL, 'Bibliography', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_taxonomy_widget', 'cataloguePeople', 3, 1, false, true, true, '#5BAABD', true, NULL, 'People', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_taxonomy_widget', 'comment', 1, 1, false, true, true, '#5BAABD', true, NULL, 'Comments', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_taxonomy_widget', 'extLinks', 5, 1, false, true, true, '#5BAABD', true, NULL, 'External Links', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_taxonomy_widget', 'informativeWorkflow', 11, 1, false, true, true, '#5BAABD', true, NULL, 'Suggestions / Report problem', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_taxonomy_widget', 'keywords', 8, 1, false, true, true, '#5BAABD', true, NULL, 'Keywords', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_taxonomy_widget', 'properties', 2, 1, false, true, true, '#5BAABD', true, NULL, 'Properties', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_taxonomy_widget', 'relatedFiles', 9, 1, false, true, true, '#5BAABD', true, NULL, 'Related Files', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_taxonomy_widget', 'relationRecombination', 4, 1, false, true, true, '#5BAABD', true, NULL, 'Recombined from', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_taxonomy_widget', 'synonym', 6, 1, false, true, true, '#5BAABD', true, NULL, 'Synonymies', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'catalogue_taxonomy_widget', 'vernacularNames', 7, 1, false, true, true, '#5BAABD', true, NULL, 'Vernacular names', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'loanitem_widget', 'actors', 2, 1, false, true, true, '#5BAABD', true, NULL, 'People involved', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'loanitem_widget', 'mainInfo', 1, 1, true, true, true, '#5BAABD', true, NULL, 'Loan Item', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'loanitem_widget', 'maintenances', 8, 1, false, true, true, '#5BAABD', true, NULL, 'Maintenances', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'loanitem_widget', 'refCodes', 5, 1, false, true, true, '#5BAABD', true, NULL, 'Codes', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'loanitem_widget', 'refComments', 5, 1, false, true, true, '#5BAABD', true, NULL, 'Comments', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'loanitem_widget', 'refInsurances', 6, 1, false, true, true, '#5BAABD', true, NULL, 'Insurances', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'loanitem_widget', 'refProperties', 4, 1, false, true, true, '#5BAABD', true, NULL, 'Properties', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'loanitem_widget', 'refRelatedFiles', 7, 1, false, true, true, '#5BAABD', true, NULL, 'Related Files', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'loan_widget', 'actors', 2, 1, false, true, true, '#5BAABD', true, NULL, 'People involved', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'loan_widget', 'loanStatus', 3, 1, false, true, true, '#5BAABD', true, NULL, 'Loan status', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'loan_widget', 'mainInfo', 1, 1, true, true, true, '#5BAABD', true, NULL, 'Loan', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'loan_widget', 'maintenances', 9, 1, false, true, true, '#5BAABD', true, NULL, 'Maintenances', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'loan_widget', 'refComments', 5, 1, false, true, true, '#5BAABD', true, NULL, 'Comments', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'loan_widget', 'refInsurances', 6, 1, false, true, true, '#5BAABD', true, NULL, 'Insurances', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'loan_widget', 'refProperties', 4, 1, false, true, true, '#5BAABD', true, NULL, 'Properties', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'loan_widget', 'refRelatedFiles', 7, 1, false, true, true, '#5BAABD', true, NULL, 'Related Files', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'loan_widget', 'refUsers', 8, 1, false, true, true, '#5BAABD', true, NULL, 'Darwin Users', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'maintenances_widget', 'extLinks', 4, 1, false, false, true, '#5BAABD', true, NULL, 'External Url', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'maintenances_widget', 'refComments', 2, 1, false, false, true, '#5BAABD', true, NULL, 'Comments', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'maintenances_widget', 'refProperties', 3, 1, false, false, true, '#5BAABD', true, NULL, 'Properties', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'maintenances_widget', 'refRelatedFiles', 1, 1, false, true, true, '#5BAABD', true, NULL, 'Related Files', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'people_institution_widget', 'address', 0, 1, false, true, true, '#5BAABD', true, NULL, 'Addresses', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'people_institution_widget', 'comm', 3, 1, false, false, false, '#5BAABD', true, NULL, 'Communications', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'people_institution_widget', 'comment', 5, 1, false, false, false, '#5BAABD', true, NULL, 'Comments', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'people_institution_widget', 'extLinks', 6, 1, false, false, false, '#5BAABD', true, NULL, 'External Links', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'people_institution_widget', 'informativeWorkflow', 7, 2, false, true, true, '#5BAABD', true, NULL, 'Suggestions / Report problem', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'people_institution_widget', 'properties', 4, 1, false, false, false, '#5BAABD', true, NULL, 'Properties', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'people_institution_widget', 'relatedFiles', 8, 2, false, true, true, '#5BAABD', true, NULL, 'Related Files', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'people_institution_widget', 'relation', 1, 1, false, true, true, '#5BAABD', true, NULL, 'Relationships', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'people_institution_widget', 'identifiers', 9, 2, false, true, true, '#5BAABD', true, NULL, 'Identifiers', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'people_institution_widget', 'peopleSubTypes', 10, 2, false, true, true, '#5BAABD', true, NULL, 'People category', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'people_widget', 'address', 0, 1, false, true, true, '#5BAABD', true, NULL, 'Addresses', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'people_widget', 'comm', 0, 1, false, true, true, '#5BAABD', true, NULL, 'Communications', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'people_widget', 'comment', 0, 1, false, true, true, '#5BAABD', true, NULL, 'Comments', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'people_widget', 'extLinks', 6, 1, false, false, false, '#5BAABD', true, NULL, 'External Links', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'people_widget', 'informativeWorkflow', 8, 1, false, true, true, '#5BAABD', true, NULL, 'Suggestions / Report problem', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'people_widget', 'lang', 0, 1, false, true, true, '#5BAABD', true, NULL, 'Languages', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'people_widget', 'properties', 0, 1, false, true, true, '#5BAABD', true, NULL, 'Properties', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'people_widget', 'relatedFiles', 9, 1, false, true, true, '#5BAABD', true, NULL, 'Related Files', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'people_widget', 'relation', 1, 1, false, true, true, '#5BAABD', true, NULL, 'Relationships', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'people_widget', 'identifiers', 10, 1, false, true, true, '#5BAABD', true, NULL, 'Identifiers', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimensearch_widget', 'acquisitions', 6, 2, false, false, true, '#5BAABD', true, NULL, 'Acquisitions', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimensearch_widget', 'codes', 2, 1, false, true, true, '#5BAABD', true, NULL, 'Codes', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimensearch_widget', 'comments', 1, 2, false, true, true, '#5BAABD', true, NULL, 'Comments', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimensearch_widget', 'container', 14, 2, false, true, true, '#5BAABD', true, NULL, 'Container', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimensearch_widget', 'expedition', 4, 1, false, true, true, '#5BAABD', true, NULL, 'Expedition', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimensearch_widget', 'identification', 11, 2, false, true, false, 'e#5BAABD', true, NULL, 'Identification', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimensearch_widget', 'latlong', 4, 2, false, true, true, '#5BAABD', true, NULL, 'Latitude Longitude', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimensearch_widget', 'localisation', 13, 2, false, true, true, '#5BAABD', true, NULL, 'Localisation', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimensearch_widget', 'methods', 8, 2, false, false, false, '#5BAABD', true, NULL, 'Methods', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimensearch_widget', 'multimedia', 9, 1, false, true, true, '#5BAABD', true, NULL, 'Multimedia', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimensearch_widget', 'nagoya', 18, 2, false, true, true, 'e#5BAABD', true, NULL, 'Nagoya', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimensearch_widget', 'part', 7, 2, false, true, true, '#5BAABD', true, NULL, 'Part', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimensearch_widget', 'people_role', 12, 2, false, true, true, '#5BAABD', true, NULL, 'People role', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimensearch_widget', 'properties', 8, 2, false, true, true, '#5BAABD', true, NULL, 'Properties', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimensearch_widget', 'refChrono', 3, 2, false, true, false, '#5BAABD', true, NULL, 'Chronostratigraphy', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimensearch_widget', 'refCollection', 1, 1, true, true, true, '#5BAABD', true, NULL, 'Collection', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimensearch_widget', 'refGtu', 5, 2, false, true, true, '#5BAABD', true, NULL, 'Sampling Location', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimensearch_widget', 'refIgs', 8, 1, false, true, true, '#5BAABD', true, NULL, 'I.G.', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimensearch_widget', 'refLitho', 5, 1, false, true, false, '#5BAABD', true, NULL, 'Lithostratigraphy', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimensearch_widget', 'refLithology', 6, 1, false, true, false, '#5BAABD', true, NULL, 'Lithology', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimensearch_widget', 'refMineral', 2, 2, false, true, true, '#5BAABD', true, NULL, 'Mineralogy', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimensearch_widget', 'refTaxon', 3, 1, false, true, true, '#5BAABD', true, NULL, 'Taxonomy', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimensearch_widget', 'rockform', 8, 2, false, false, false, '#5BAABD', true, NULL, 'Rock Form', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimensearch_widget', 'sex', 5, 1, false, false, false, '#5BAABD', true, NULL, 'Sex', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimensearch_widget', 'social', 9, 2, false, true, false, '#5BAABD', true, NULL, 'Social Status', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimensearch_widget', 'specIds', 17, 2, false, true, true, '#5BAABD', true, NULL, 'Advanced: Search By ID', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimensearch_widget', 'stage', 7, 1, false, true, false, '#5BAABD', true, NULL, 'Stage', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimensearch_widget', 'status', 10, 2, false, true, false, '#5BAABD', true, NULL, 'Sexual State', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimensearch_widget', 'statusAndCount', 16, 2, false, true, true, '#5BAABD', true, NULL, 'Specimen Status / Count', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimensearch_widget', 'tools', 0, 1, false, false, false, '#5BAABD', true, NULL, 'Tools', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimensearch_widget', 'type', 15, 2, false, true, true, '#5BAABD', true, NULL, 'Type', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimen_widget', 'acquisitionCategory', 7, 1, false, true, true, '#5BAABD', true, NULL, 'Acquisition', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimen_widget', 'biblio', 5, 2, false, true, false, '#5BAABD', true, NULL, 'Bibliography', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimen_widget', 'complete', 1, 2, false, true, true, '#5BAABD', true, NULL, 'Complete', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimen_widget', 'container', 3, 2, false, true, true, '#5BAABD', true, NULL, 'Container', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimen_widget', 'extLinks', 9, 2, false, true, true, '#5BAABD', true, NULL, 'External Links', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimen_widget', 'historic', 19, 1, false, true, false, '#5BAABD', true, NULL, 'Historic', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimen_widget', 'informativeWorkflow', 13, 2, false, true, false, '#5BAABD', true, NULL, 'Suggestions / Report problem', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimen_widget', 'loan', 8, 1, false, true, false, '#5BAABD', true, NULL, 'Loans', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimen_widget', 'localisation', 2, 2, true, true, true, '#5BAABD', true, NULL, 'Localisation', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimen_widget', 'maintenance', 15, 2, false, true, false, '#5BAABD', true, NULL, 'Maintenance', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimen_widget', 'method', 7, 2, false, true, true, '#5BAABD', true, NULL, 'Collecting method', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimen_widget', 'nagoya', 2, 1, false, true, true, 'e#5BAABD', true, NULL, 'Nagoya', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimen_widget', 'refChrono', 20, 1, false, true, false, '#5BAABD', true, NULL, 'Chronostratigraphy', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimen_widget', 'refCodes', 1, 1, false, true, true, '#5BAABD', true, NULL, 'Codes', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimen_widget', 'refCollection', 3, 1, true, true, true, '#5BAABD', true, NULL, 'Collection', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimen_widget', 'refCollectors', 10, 2, false, true, true, '#5BAABD', true, NULL, 'Collectors', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimen_widget', 'refComment', 6, 2, false, true, true, '#5BAABD', true, NULL, 'Comments', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimen_widget', 'refDonators', 17, 2, false, true, true, '#5BAABD', true, NULL, 'Donators or Sellers', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimen_widget', 'refExpedition', 11, 2, false, true, true, '#5BAABD', true, NULL, 'Expedition', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimen_widget', 'refGtu', 8, 2, false, true, true, '#5BAABD', true, NULL, 'Sampling location', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimen_widget', 'refIdentifications', 4, 2, false, true, true, '#5BAABD', true, NULL, 'Identifications', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimen_widget', 'refIgs', 12, 1, false, true, true, '#5BAABD', true, NULL, 'I.G. number', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimen_widget', 'refInsurances', 14, 2, false, true, false, '#5BAABD', true, NULL, 'Insurances', ',', false);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimen_widget', 'refLitho', 10, 1, false, true, false, '#5BAABD', true, NULL, 'Lithostratigraphy', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimen_widget', 'refLithology', 13, 1, false, true, false, '#5BAABD', true, NULL, 'Lithology', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimen_widget', 'refMineral', 21, 1, false, true, false, '#5BAABD', true, NULL, 'Mineralogy', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimen_widget', 'refProperties', 5, 1, false, true, true, '#5BAABD', true, NULL, 'Properties', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimen_widget', 'refRelatedFiles', 16, 2, false, true, true, '#5BAABD', true, NULL, 'Related Files', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimen_widget', 'refTaxon', 4, 1, false, true, true, '#5BAABD', true, NULL, 'Taxonomy', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimen_widget', 'rockForm', 17, 1, false, true, false, '#5BAABD', true, NULL, 'Rock form', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimen_widget', 'sex', 16, 1, false, true, false, '#5BAABD', true, NULL, 'Sex', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimen_widget', 'socialStatus', 14, 1, false, true, false, '#5BAABD', true, NULL, 'Social Status', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimen_widget', 'specimenCount', 15, 1, false, true, false, '#5BAABD', true, NULL, 'Count', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimen_widget', 'specimensRelationships', 6, 1, false, true, true, '#5BAABD', true, NULL, 'Relationships', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimen_widget', 'specPart', 9, 1, true, true, true, '#5BAABD', true, NULL, 'Part', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimen_widget', 'stage', 18, 1, false, true, false, '#5BAABD', true, NULL, 'Stage', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimen_widget', 'tool', 12, 2, false, true, false, '#5BAABD', true, NULL, 'Collecting tools', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimen_widget', 'type', 11, 1, false, true, true, '#5BAABD', true, NULL, 'Type', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'users_widget', 'address', 4, 1, false, true, true, '#5BAABD', true, NULL, 'Addresses', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'users_widget', 'comm', 3, 1, false, true, true, '#5BAABD', true, NULL, 'Communications', ',', true);
INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'users_widget', 'info', 1, 1, false, true, true, '#5BAABD', true, NULL, 'User information', ',', true);

INSERT INTO darwin2.my_widgets (user_ref, category, group_name, order_by, col_num, mandatory,  visible, opened, color, is_available, icon_ref, title_perso, collections, all_public) VALUES ( 1, 'specimensearch_widget', 'bibliography', 17, 2, false, true, true, '#5BAABD', true, NULL, 'Bibliography', ',', true);



-- Completed on 2020-07-07 15:29:58

--
-- PostgreSQL database dump complete
--



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



insert into possible_upper_levels (level_ref, level_upper_ref) values (1, null);
insert into possible_upper_levels (level_ref, level_upper_ref) values (55, null);
insert into possible_upper_levels (level_ref, level_upper_ref) values (64, null);
insert into possible_upper_levels (level_ref, level_upper_ref) values (70, null);
insert into possible_upper_levels (level_ref, level_upper_ref) values (75, null);
insert into possible_upper_levels (level_ref, level_upper_ref) values (65, null);
insert into possible_upper_levels (level_ref, level_upper_ref) values (2, 1);
insert into possible_upper_levels (level_ref, level_upper_ref) values (3, 2);
insert into possible_upper_levels (level_ref, level_upper_ref) values (4, 2);
insert into possible_upper_levels (level_ref, level_upper_ref) values (4, 3);
insert into possible_upper_levels (level_ref, level_upper_ref) values (5, 4);
insert into possible_upper_levels (level_ref, level_upper_ref) values (6, 5);
insert into possible_upper_levels (level_ref, level_upper_ref) values (7, 2);
insert into possible_upper_levels (level_ref, level_upper_ref) values (7, 4);
insert into possible_upper_levels (level_ref, level_upper_ref) values (7, 5);
insert into possible_upper_levels (level_ref, level_upper_ref) values (7, 6);
insert into possible_upper_levels (level_ref, level_upper_ref) values (8, 2);
insert into possible_upper_levels (level_ref, level_upper_ref) values (8, 4);
insert into possible_upper_levels (level_ref, level_upper_ref) values (8, 5);
insert into possible_upper_levels (level_ref, level_upper_ref) values (8, 6);
insert into possible_upper_levels (level_ref, level_upper_ref) values (8, 7);
insert into possible_upper_levels (level_ref, level_upper_ref) values (9, 8);
insert into possible_upper_levels (level_ref, level_upper_ref) values (10, 9);
insert into possible_upper_levels (level_ref, level_upper_ref) values (11, 2);
insert into possible_upper_levels (level_ref, level_upper_ref) values (11, 4);
insert into possible_upper_levels (level_ref, level_upper_ref) values (11, 5);
insert into possible_upper_levels (level_ref, level_upper_ref) values (11, 6);
insert into possible_upper_levels (level_ref, level_upper_ref) values (11, 8);
insert into possible_upper_levels (level_ref, level_upper_ref) values (11, 9);
insert into possible_upper_levels (level_ref, level_upper_ref) values (11, 10);
insert into possible_upper_levels (level_ref, level_upper_ref) values (12, 2);
insert into possible_upper_levels (level_ref, level_upper_ref) values (12, 4);
insert into possible_upper_levels (level_ref, level_upper_ref) values (12, 5);
insert into possible_upper_levels (level_ref, level_upper_ref) values (12, 6);
insert into possible_upper_levels (level_ref, level_upper_ref) values (12, 8);
insert into possible_upper_levels (level_ref, level_upper_ref) values (12, 9);
insert into possible_upper_levels (level_ref, level_upper_ref) values (12, 10);
insert into possible_upper_levels (level_ref, level_upper_ref) values (12, 11);
insert into possible_upper_levels (level_ref, level_upper_ref) values (13, 12);
insert into possible_upper_levels (level_ref, level_upper_ref) values (14, 13);
insert into possible_upper_levels (level_ref, level_upper_ref) values (15, 2);
insert into possible_upper_levels (level_ref, level_upper_ref) values (15, 4);
insert into possible_upper_levels (level_ref, level_upper_ref) values (15, 5);
insert into possible_upper_levels (level_ref, level_upper_ref) values (15, 6);
insert into possible_upper_levels (level_ref, level_upper_ref) values (15, 8);
insert into possible_upper_levels (level_ref, level_upper_ref) values (15, 9);
insert into possible_upper_levels (level_ref, level_upper_ref) values (15, 10);
insert into possible_upper_levels (level_ref, level_upper_ref) values (15, 12);
insert into possible_upper_levels (level_ref, level_upper_ref) values (15, 13);
insert into possible_upper_levels (level_ref, level_upper_ref) values (15, 14);
insert into possible_upper_levels (level_ref, level_upper_ref) values (16, 2);
insert into possible_upper_levels (level_ref, level_upper_ref) values (16, 4);
insert into possible_upper_levels (level_ref, level_upper_ref) values (16, 5);
insert into possible_upper_levels (level_ref, level_upper_ref) values (16, 6);
insert into possible_upper_levels (level_ref, level_upper_ref) values (16, 8);
insert into possible_upper_levels (level_ref, level_upper_ref) values (16, 9);
insert into possible_upper_levels (level_ref, level_upper_ref) values (16, 10);
insert into possible_upper_levels (level_ref, level_upper_ref) values (16, 12);
insert into possible_upper_levels (level_ref, level_upper_ref) values (16, 13);
insert into possible_upper_levels (level_ref, level_upper_ref) values (16, 14);
insert into possible_upper_levels (level_ref, level_upper_ref) values (16, 15);
insert into possible_upper_levels (level_ref, level_upper_ref) values (17, 16);
insert into possible_upper_levels (level_ref, level_upper_ref) values (18, 17);
insert into possible_upper_levels (level_ref, level_upper_ref) values (19, 2);
insert into possible_upper_levels (level_ref, level_upper_ref) values (19, 4);
insert into possible_upper_levels (level_ref, level_upper_ref) values (19, 5);
insert into possible_upper_levels (level_ref, level_upper_ref) values (19, 6);
insert into possible_upper_levels (level_ref, level_upper_ref) values (19, 8);
insert into possible_upper_levels (level_ref, level_upper_ref) values (19, 9);
insert into possible_upper_levels (level_ref, level_upper_ref) values (19, 10);
insert into possible_upper_levels (level_ref, level_upper_ref) values (19, 12);
insert into possible_upper_levels (level_ref, level_upper_ref) values (19, 13);
insert into possible_upper_levels (level_ref, level_upper_ref) values (19, 14);
insert into possible_upper_levels (level_ref, level_upper_ref) values (19, 16);
insert into possible_upper_levels (level_ref, level_upper_ref) values (19, 17);
insert into possible_upper_levels (level_ref, level_upper_ref) values (19, 18);
insert into possible_upper_levels (level_ref, level_upper_ref) values (20, 2);
insert into possible_upper_levels (level_ref, level_upper_ref) values (20, 4);
insert into possible_upper_levels (level_ref, level_upper_ref) values (20, 5);
insert into possible_upper_levels (level_ref, level_upper_ref) values (20, 6);
insert into possible_upper_levels (level_ref, level_upper_ref) values (20, 8);
insert into possible_upper_levels (level_ref, level_upper_ref) values (20, 9);
insert into possible_upper_levels (level_ref, level_upper_ref) values (20, 10);
insert into possible_upper_levels (level_ref, level_upper_ref) values (20, 12);
insert into possible_upper_levels (level_ref, level_upper_ref) values (20, 13);
insert into possible_upper_levels (level_ref, level_upper_ref) values (20, 14);
insert into possible_upper_levels (level_ref, level_upper_ref) values (20, 16);
insert into possible_upper_levels (level_ref, level_upper_ref) values (20, 17);
insert into possible_upper_levels (level_ref, level_upper_ref) values (20, 18);
insert into possible_upper_levels (level_ref, level_upper_ref) values (20, 19);
insert into possible_upper_levels (level_ref, level_upper_ref) values (21, 20);
insert into possible_upper_levels (level_ref, level_upper_ref) values (22, 21);
insert into possible_upper_levels (level_ref, level_upper_ref) values (23, 2);
insert into possible_upper_levels (level_ref, level_upper_ref) values (23, 4);
insert into possible_upper_levels (level_ref, level_upper_ref) values (23, 5);
insert into possible_upper_levels (level_ref, level_upper_ref) values (23, 6);
insert into possible_upper_levels (level_ref, level_upper_ref) values (23, 8);
insert into possible_upper_levels (level_ref, level_upper_ref) values (23, 9);
insert into possible_upper_levels (level_ref, level_upper_ref) values (23, 10);
insert into possible_upper_levels (level_ref, level_upper_ref) values (23, 12);
insert into possible_upper_levels (level_ref, level_upper_ref) values (23, 13);
insert into possible_upper_levels (level_ref, level_upper_ref) values (23, 14);
insert into possible_upper_levels (level_ref, level_upper_ref) values (23, 16);
insert into possible_upper_levels (level_ref, level_upper_ref) values (23, 17);
insert into possible_upper_levels (level_ref, level_upper_ref) values (23, 18);
insert into possible_upper_levels (level_ref, level_upper_ref) values (23, 20);
insert into possible_upper_levels (level_ref, level_upper_ref) values (23, 21);
insert into possible_upper_levels (level_ref, level_upper_ref) values (23, 22);
insert into possible_upper_levels (level_ref, level_upper_ref) values (24, 2);
insert into possible_upper_levels (level_ref, level_upper_ref) values (24, 4);
insert into possible_upper_levels (level_ref, level_upper_ref) values (24, 5);
insert into possible_upper_levels (level_ref, level_upper_ref) values (24, 6);
insert into possible_upper_levels (level_ref, level_upper_ref) values (24, 8);
insert into possible_upper_levels (level_ref, level_upper_ref) values (24, 9);
insert into possible_upper_levels (level_ref, level_upper_ref) values (24, 10);
insert into possible_upper_levels (level_ref, level_upper_ref) values (24, 12);
insert into possible_upper_levels (level_ref, level_upper_ref) values (24, 13);
insert into possible_upper_levels (level_ref, level_upper_ref) values (24, 14);
insert into possible_upper_levels (level_ref, level_upper_ref) values (24, 16);
insert into possible_upper_levels (level_ref, level_upper_ref) values (24, 17);
insert into possible_upper_levels (level_ref, level_upper_ref) values (24, 18);
insert into possible_upper_levels (level_ref, level_upper_ref) values (24, 20);
insert into possible_upper_levels (level_ref, level_upper_ref) values (24, 21);
insert into possible_upper_levels (level_ref, level_upper_ref) values (24, 22);
insert into possible_upper_levels (level_ref, level_upper_ref) values (24, 23);
insert into possible_upper_levels (level_ref, level_upper_ref) values (25, 24);
insert into possible_upper_levels (level_ref, level_upper_ref) values (26, 25);
insert into possible_upper_levels (level_ref, level_upper_ref) values (27, 2);
insert into possible_upper_levels (level_ref, level_upper_ref) values (27, 4);
insert into possible_upper_levels (level_ref, level_upper_ref) values (27, 5);
insert into possible_upper_levels (level_ref, level_upper_ref) values (27, 6);
insert into possible_upper_levels (level_ref, level_upper_ref) values (27, 8);
insert into possible_upper_levels (level_ref, level_upper_ref) values (27, 9);
insert into possible_upper_levels (level_ref, level_upper_ref) values (27, 10);
insert into possible_upper_levels (level_ref, level_upper_ref) values (27, 12);
insert into possible_upper_levels (level_ref, level_upper_ref) values (27, 13);
insert into possible_upper_levels (level_ref, level_upper_ref) values (27, 14);
insert into possible_upper_levels (level_ref, level_upper_ref) values (27, 16);
insert into possible_upper_levels (level_ref, level_upper_ref) values (27, 17);
insert into possible_upper_levels (level_ref, level_upper_ref) values (27, 18);
insert into possible_upper_levels (level_ref, level_upper_ref) values (27, 20);
insert into possible_upper_levels (level_ref, level_upper_ref) values (27, 21);
insert into possible_upper_levels (level_ref, level_upper_ref) values (27, 22);
insert into possible_upper_levels (level_ref, level_upper_ref) values (27, 24);
insert into possible_upper_levels (level_ref, level_upper_ref) values (27, 25);
insert into possible_upper_levels (level_ref, level_upper_ref) values (27, 26);
insert into possible_upper_levels (level_ref, level_upper_ref) values (28, 2);
insert into possible_upper_levels (level_ref, level_upper_ref) values (28, 4);
insert into possible_upper_levels (level_ref, level_upper_ref) values (28, 5);
insert into possible_upper_levels (level_ref, level_upper_ref) values (28, 6);
insert into possible_upper_levels (level_ref, level_upper_ref) values (28, 8);
insert into possible_upper_levels (level_ref, level_upper_ref) values (28, 9);
insert into possible_upper_levels (level_ref, level_upper_ref) values (28, 10);
insert into possible_upper_levels (level_ref, level_upper_ref) values (28, 12);
insert into possible_upper_levels (level_ref, level_upper_ref) values (28, 13);
insert into possible_upper_levels (level_ref, level_upper_ref) values (28, 14);
insert into possible_upper_levels (level_ref, level_upper_ref) values (28, 16);
insert into possible_upper_levels (level_ref, level_upper_ref) values (28, 17);
insert into possible_upper_levels (level_ref, level_upper_ref) values (28, 18);
insert into possible_upper_levels (level_ref, level_upper_ref) values (28, 20);
insert into possible_upper_levels (level_ref, level_upper_ref) values (28, 21);
insert into possible_upper_levels (level_ref, level_upper_ref) values (28, 22);
insert into possible_upper_levels (level_ref, level_upper_ref) values (28, 24);
insert into possible_upper_levels (level_ref, level_upper_ref) values (28, 25);
insert into possible_upper_levels (level_ref, level_upper_ref) values (28, 26);
insert into possible_upper_levels (level_ref, level_upper_ref) values (28, 27);
insert into possible_upper_levels (level_ref, level_upper_ref) values (29, 28);
insert into possible_upper_levels (level_ref, level_upper_ref) values (30, 29);
insert into possible_upper_levels (level_ref, level_upper_ref) values (31, 2);
insert into possible_upper_levels (level_ref, level_upper_ref) values (31, 4);
insert into possible_upper_levels (level_ref, level_upper_ref) values (31, 5);
insert into possible_upper_levels (level_ref, level_upper_ref) values (31, 6);
insert into possible_upper_levels (level_ref, level_upper_ref) values (31, 8);
insert into possible_upper_levels (level_ref, level_upper_ref) values (31, 9);
insert into possible_upper_levels (level_ref, level_upper_ref) values (31, 10);
insert into possible_upper_levels (level_ref, level_upper_ref) values (31, 12);
insert into possible_upper_levels (level_ref, level_upper_ref) values (31, 13);
insert into possible_upper_levels (level_ref, level_upper_ref) values (31, 14);
insert into possible_upper_levels (level_ref, level_upper_ref) values (31, 16);
insert into possible_upper_levels (level_ref, level_upper_ref) values (31, 17);
insert into possible_upper_levels (level_ref, level_upper_ref) values (31, 18);
insert into possible_upper_levels (level_ref, level_upper_ref) values (31, 20);
insert into possible_upper_levels (level_ref, level_upper_ref) values (31, 21);
insert into possible_upper_levels (level_ref, level_upper_ref) values (31, 22);
insert into possible_upper_levels (level_ref, level_upper_ref) values (31, 24);
insert into possible_upper_levels (level_ref, level_upper_ref) values (31, 25);
insert into possible_upper_levels (level_ref, level_upper_ref) values (31, 26);
insert into possible_upper_levels (level_ref, level_upper_ref) values (31, 28);
insert into possible_upper_levels (level_ref, level_upper_ref) values (31, 29);
insert into possible_upper_levels (level_ref, level_upper_ref) values (31, 30);
insert into possible_upper_levels (level_ref, level_upper_ref) values (32, 31);
insert into possible_upper_levels (level_ref, level_upper_ref) values (33, 2);
insert into possible_upper_levels (level_ref, level_upper_ref) values (33, 4);
insert into possible_upper_levels (level_ref, level_upper_ref) values (33, 5);
insert into possible_upper_levels (level_ref, level_upper_ref) values (33, 6);
insert into possible_upper_levels (level_ref, level_upper_ref) values (33, 8);
insert into possible_upper_levels (level_ref, level_upper_ref) values (33, 9);
insert into possible_upper_levels (level_ref, level_upper_ref) values (33, 10);
insert into possible_upper_levels (level_ref, level_upper_ref) values (33, 12);
insert into possible_upper_levels (level_ref, level_upper_ref) values (33, 13);
insert into possible_upper_levels (level_ref, level_upper_ref) values (33, 14);
insert into possible_upper_levels (level_ref, level_upper_ref) values (33, 16);
insert into possible_upper_levels (level_ref, level_upper_ref) values (33, 17);
insert into possible_upper_levels (level_ref, level_upper_ref) values (33, 18);
insert into possible_upper_levels (level_ref, level_upper_ref) values (33, 20);
insert into possible_upper_levels (level_ref, level_upper_ref) values (33, 21);
insert into possible_upper_levels (level_ref, level_upper_ref) values (33, 22);
insert into possible_upper_levels (level_ref, level_upper_ref) values (33, 24);
insert into possible_upper_levels (level_ref, level_upper_ref) values (33, 25);
insert into possible_upper_levels (level_ref, level_upper_ref) values (33, 26);
insert into possible_upper_levels (level_ref, level_upper_ref) values (33, 28);
insert into possible_upper_levels (level_ref, level_upper_ref) values (33, 29);
insert into possible_upper_levels (level_ref, level_upper_ref) values (33, 30);
insert into possible_upper_levels (level_ref, level_upper_ref) values (33, 31);
insert into possible_upper_levels (level_ref, level_upper_ref) values (33, 32);
insert into possible_upper_levels (level_ref, level_upper_ref) values (34, 2);
insert into possible_upper_levels (level_ref, level_upper_ref) values (34, 4);
insert into possible_upper_levels (level_ref, level_upper_ref) values (34, 5);
insert into possible_upper_levels (level_ref, level_upper_ref) values (34, 6);
insert into possible_upper_levels (level_ref, level_upper_ref) values (34, 8);
insert into possible_upper_levels (level_ref, level_upper_ref) values (34, 9);
insert into possible_upper_levels (level_ref, level_upper_ref) values (34, 10);
insert into possible_upper_levels (level_ref, level_upper_ref) values (34, 12);
insert into possible_upper_levels (level_ref, level_upper_ref) values (34, 13);
insert into possible_upper_levels (level_ref, level_upper_ref) values (34, 14);
insert into possible_upper_levels (level_ref, level_upper_ref) values (34, 16);
insert into possible_upper_levels (level_ref, level_upper_ref) values (34, 17);
insert into possible_upper_levels (level_ref, level_upper_ref) values (34, 18);
insert into possible_upper_levels (level_ref, level_upper_ref) values (34, 20);
insert into possible_upper_levels (level_ref, level_upper_ref) values (34, 21);
insert into possible_upper_levels (level_ref, level_upper_ref) values (34, 22);
insert into possible_upper_levels (level_ref, level_upper_ref) values (34, 24);
insert into possible_upper_levels (level_ref, level_upper_ref) values (34, 25);
insert into possible_upper_levels (level_ref, level_upper_ref) values (34, 26);
insert into possible_upper_levels (level_ref, level_upper_ref) values (34, 28);
insert into possible_upper_levels (level_ref, level_upper_ref) values (34, 29);
insert into possible_upper_levels (level_ref, level_upper_ref) values (34, 30);
insert into possible_upper_levels (level_ref, level_upper_ref) values (34, 31);
insert into possible_upper_levels (level_ref, level_upper_ref) values (34, 32);
insert into possible_upper_levels (level_ref, level_upper_ref) values (34, 33);
insert into possible_upper_levels (level_ref, level_upper_ref) values (35, 34);
insert into possible_upper_levels (level_ref, level_upper_ref) values (36, 34);
insert into possible_upper_levels (level_ref, level_upper_ref) values (36, 35);
insert into possible_upper_levels (level_ref, level_upper_ref) values (37, 2);
insert into possible_upper_levels (level_ref, level_upper_ref) values (37, 4);
insert into possible_upper_levels (level_ref, level_upper_ref) values (37, 5);
insert into possible_upper_levels (level_ref, level_upper_ref) values (37, 6);
insert into possible_upper_levels (level_ref, level_upper_ref) values (37, 8);
insert into possible_upper_levels (level_ref, level_upper_ref) values (37, 9);
insert into possible_upper_levels (level_ref, level_upper_ref) values (37, 10);
insert into possible_upper_levels (level_ref, level_upper_ref) values (37, 12);
insert into possible_upper_levels (level_ref, level_upper_ref) values (37, 13);
insert into possible_upper_levels (level_ref, level_upper_ref) values (37, 14);
insert into possible_upper_levels (level_ref, level_upper_ref) values (37, 16);
insert into possible_upper_levels (level_ref, level_upper_ref) values (37, 17);
insert into possible_upper_levels (level_ref, level_upper_ref) values (37, 18);
insert into possible_upper_levels (level_ref, level_upper_ref) values (37, 20);
insert into possible_upper_levels (level_ref, level_upper_ref) values (37, 21);
insert into possible_upper_levels (level_ref, level_upper_ref) values (37, 22);
insert into possible_upper_levels (level_ref, level_upper_ref) values (37, 24);
insert into possible_upper_levels (level_ref, level_upper_ref) values (37, 25);
insert into possible_upper_levels (level_ref, level_upper_ref) values (37, 26);
insert into possible_upper_levels (level_ref, level_upper_ref) values (37, 28);
insert into possible_upper_levels (level_ref, level_upper_ref) values (37, 29);
insert into possible_upper_levels (level_ref, level_upper_ref) values (37, 30);
insert into possible_upper_levels (level_ref, level_upper_ref) values (37, 31);
insert into possible_upper_levels (level_ref, level_upper_ref) values (37, 32);
insert into possible_upper_levels (level_ref, level_upper_ref) values (37, 33);
insert into possible_upper_levels (level_ref, level_upper_ref) values (37, 34);
insert into possible_upper_levels (level_ref, level_upper_ref) values (37, 35);
insert into possible_upper_levels (level_ref, level_upper_ref) values (37, 36);
insert into possible_upper_levels (level_ref, level_upper_ref) values (38, 2);
insert into possible_upper_levels (level_ref, level_upper_ref) values (38, 4);
insert into possible_upper_levels (level_ref, level_upper_ref) values (38, 5);
insert into possible_upper_levels (level_ref, level_upper_ref) values (38, 6);
insert into possible_upper_levels (level_ref, level_upper_ref) values (38, 8);
insert into possible_upper_levels (level_ref, level_upper_ref) values (38, 9);
insert into possible_upper_levels (level_ref, level_upper_ref) values (38, 10);
insert into possible_upper_levels (level_ref, level_upper_ref) values (38, 12);
insert into possible_upper_levels (level_ref, level_upper_ref) values (38, 13);
insert into possible_upper_levels (level_ref, level_upper_ref) values (38, 14);
insert into possible_upper_levels (level_ref, level_upper_ref) values (38, 16);
insert into possible_upper_levels (level_ref, level_upper_ref) values (38, 17);
insert into possible_upper_levels (level_ref, level_upper_ref) values (38, 18);
insert into possible_upper_levels (level_ref, level_upper_ref) values (38, 20);
insert into possible_upper_levels (level_ref, level_upper_ref) values (38, 21);
insert into possible_upper_levels (level_ref, level_upper_ref) values (38, 22);
insert into possible_upper_levels (level_ref, level_upper_ref) values (38, 24);
insert into possible_upper_levels (level_ref, level_upper_ref) values (38, 25);
insert into possible_upper_levels (level_ref, level_upper_ref) values (38, 26);
insert into possible_upper_levels (level_ref, level_upper_ref) values (38, 28);
insert into possible_upper_levels (level_ref, level_upper_ref) values (38, 29);
insert into possible_upper_levels (level_ref, level_upper_ref) values (38, 30);
insert into possible_upper_levels (level_ref, level_upper_ref) values (38, 31);
insert into possible_upper_levels (level_ref, level_upper_ref) values (38, 32);
insert into possible_upper_levels (level_ref, level_upper_ref) values (38, 33);
insert into possible_upper_levels (level_ref, level_upper_ref) values (38, 34);
insert into possible_upper_levels (level_ref, level_upper_ref) values (38, 35);
insert into possible_upper_levels (level_ref, level_upper_ref) values (38, 36);
insert into possible_upper_levels (level_ref, level_upper_ref) values (38, 37);
insert into possible_upper_levels (level_ref, level_upper_ref) values (39, 38);
insert into possible_upper_levels (level_ref, level_upper_ref) values (40, 39);
insert into possible_upper_levels (level_ref, level_upper_ref) values (41, 2);
insert into possible_upper_levels (level_ref, level_upper_ref) values (41, 4);
insert into possible_upper_levels (level_ref, level_upper_ref) values (41, 5);
insert into possible_upper_levels (level_ref, level_upper_ref) values (41, 6);
insert into possible_upper_levels (level_ref, level_upper_ref) values (41, 8);
insert into possible_upper_levels (level_ref, level_upper_ref) values (41, 9);
insert into possible_upper_levels (level_ref, level_upper_ref) values (41, 10);
insert into possible_upper_levels (level_ref, level_upper_ref) values (41, 12);
insert into possible_upper_levels (level_ref, level_upper_ref) values (41, 13);
insert into possible_upper_levels (level_ref, level_upper_ref) values (41, 14);
insert into possible_upper_levels (level_ref, level_upper_ref) values (41, 16);
insert into possible_upper_levels (level_ref, level_upper_ref) values (41, 17);
insert into possible_upper_levels (level_ref, level_upper_ref) values (41, 18);
insert into possible_upper_levels (level_ref, level_upper_ref) values (41, 20);
insert into possible_upper_levels (level_ref, level_upper_ref) values (41, 21);
insert into possible_upper_levels (level_ref, level_upper_ref) values (41, 22);
insert into possible_upper_levels (level_ref, level_upper_ref) values (41, 24);
insert into possible_upper_levels (level_ref, level_upper_ref) values (41, 25);
insert into possible_upper_levels (level_ref, level_upper_ref) values (41, 26);
insert into possible_upper_levels (level_ref, level_upper_ref) values (41, 28);
insert into possible_upper_levels (level_ref, level_upper_ref) values (41, 29);
insert into possible_upper_levels (level_ref, level_upper_ref) values (41, 30);
insert into possible_upper_levels (level_ref, level_upper_ref) values (41, 31);
insert into possible_upper_levels (level_ref, level_upper_ref) values (41, 32);
insert into possible_upper_levels (level_ref, level_upper_ref) values (41, 33);
insert into possible_upper_levels (level_ref, level_upper_ref) values (41, 34);
insert into possible_upper_levels (level_ref, level_upper_ref) values (41, 35);
insert into possible_upper_levels (level_ref, level_upper_ref) values (41, 36);
insert into possible_upper_levels (level_ref, level_upper_ref) values (41, 38);
insert into possible_upper_levels (level_ref, level_upper_ref) values (41, 39);
insert into possible_upper_levels (level_ref, level_upper_ref) values (41, 40);
insert into possible_upper_levels (level_ref, level_upper_ref) values (42, 41);
insert into possible_upper_levels (level_ref, level_upper_ref) values (43, 41);
insert into possible_upper_levels (level_ref, level_upper_ref) values (43, 42);
insert into possible_upper_levels (level_ref, level_upper_ref) values (44, 43);
insert into possible_upper_levels (level_ref, level_upper_ref) values (45, 41);
insert into possible_upper_levels (level_ref, level_upper_ref) values (45, 42);
insert into possible_upper_levels (level_ref, level_upper_ref) values (45, 43);
insert into possible_upper_levels (level_ref, level_upper_ref) values (45, 44);
insert into possible_upper_levels (level_ref, level_upper_ref) values (46, 45);
insert into possible_upper_levels (level_ref, level_upper_ref) values (47, 41);
insert into possible_upper_levels (level_ref, level_upper_ref) values (47, 42);
insert into possible_upper_levels (level_ref, level_upper_ref) values (47, 43);
insert into possible_upper_levels (level_ref, level_upper_ref) values (47, 44);
insert into possible_upper_levels (level_ref, level_upper_ref) values (47, 45);
insert into possible_upper_levels (level_ref, level_upper_ref) values (47, 46);
insert into possible_upper_levels (level_ref, level_upper_ref) values (48, 4);
insert into possible_upper_levels (level_ref, level_upper_ref) values (48, 5);
insert into possible_upper_levels (level_ref, level_upper_ref) values (48, 6);
insert into possible_upper_levels (level_ref, level_upper_ref) values (48, 12);
insert into possible_upper_levels (level_ref, level_upper_ref) values (48, 13);
insert into possible_upper_levels (level_ref, level_upper_ref) values (48, 14);
insert into possible_upper_levels (level_ref, level_upper_ref) values (48, 28);
insert into possible_upper_levels (level_ref, level_upper_ref) values (48, 29);
insert into possible_upper_levels (level_ref, level_upper_ref) values (48, 30);
insert into possible_upper_levels (level_ref, level_upper_ref) values (48, 31);
insert into possible_upper_levels (level_ref, level_upper_ref) values (48, 32);
insert into possible_upper_levels (level_ref, level_upper_ref) values (48, 33);
insert into possible_upper_levels (level_ref, level_upper_ref) values (48, 34);
insert into possible_upper_levels (level_ref, level_upper_ref) values (48, 35);
insert into possible_upper_levels (level_ref, level_upper_ref) values (48, 36);
insert into possible_upper_levels (level_ref, level_upper_ref) values (48, 38);
insert into possible_upper_levels (level_ref, level_upper_ref) values (48, 39);
insert into possible_upper_levels (level_ref, level_upper_ref) values (48, 40);
insert into possible_upper_levels (level_ref, level_upper_ref) values (48, 41);
insert into possible_upper_levels (level_ref, level_upper_ref) values (48, 42);
insert into possible_upper_levels (level_ref, level_upper_ref) values (48, 43);
insert into possible_upper_levels (level_ref, level_upper_ref) values (48, 44);
insert into possible_upper_levels (level_ref, level_upper_ref) values (48, 45);
insert into possible_upper_levels (level_ref, level_upper_ref) values (48, 46);
insert into possible_upper_levels (level_ref, level_upper_ref) values (48, 47);
insert into possible_upper_levels (level_ref, level_upper_ref) values (49, 48);
insert into possible_upper_levels (level_ref, level_upper_ref) values (50, 41);
insert into possible_upper_levels (level_ref, level_upper_ref) values (50, 48);
insert into possible_upper_levels (level_ref, level_upper_ref) values (50, 49);
insert into possible_upper_levels (level_ref, level_upper_ref) values (51, 50);
insert into possible_upper_levels (level_ref, level_upper_ref) values (52, 48);
insert into possible_upper_levels (level_ref, level_upper_ref) values (52, 49);
insert into possible_upper_levels (level_ref, level_upper_ref) values (52, 50);
insert into possible_upper_levels (level_ref, level_upper_ref) values (52, 51);
insert into possible_upper_levels (level_ref, level_upper_ref) values (53, 52);
insert into possible_upper_levels (level_ref, level_upper_ref) values (54, 48);
insert into possible_upper_levels (level_ref, level_upper_ref) values (54, 49);
insert into possible_upper_levels (level_ref, level_upper_ref) values (54, 50);
insert into possible_upper_levels (level_ref, level_upper_ref) values (54, 51);
insert into possible_upper_levels (level_ref, level_upper_ref) values (54, 52);
insert into possible_upper_levels (level_ref, level_upper_ref) values (54, 53);
insert into possible_upper_levels (level_ref, level_upper_ref) values (56, 55);
insert into possible_upper_levels (level_ref, level_upper_ref) values (57, 56);
insert into possible_upper_levels (level_ref, level_upper_ref) values (58, 57);
insert into possible_upper_levels (level_ref, level_upper_ref) values (58, 56);
insert into possible_upper_levels (level_ref, level_upper_ref) values (59, 58);
insert into possible_upper_levels (level_ref, level_upper_ref) values (60, 59);
insert into possible_upper_levels (level_ref, level_upper_ref) values (61, 60);
insert into possible_upper_levels (level_ref, level_upper_ref) values (62, 61);
insert into possible_upper_levels (level_ref, level_upper_ref) values (62, 60);
insert into possible_upper_levels (level_ref, level_upper_ref) values (63, 62);
insert into possible_upper_levels (level_ref, level_upper_ref) values (63, 61);
insert into possible_upper_levels (level_ref, level_upper_ref) values (63, 60);
insert into possible_upper_levels (level_ref, level_upper_ref) values (65, 64);
insert into possible_upper_levels (level_ref, level_upper_ref) values (66, 65);
insert into possible_upper_levels (level_ref, level_upper_ref) values (67, 66);
insert into possible_upper_levels (level_ref, level_upper_ref) values (68, 67);
insert into possible_upper_levels (level_ref, level_upper_ref) values (69, 68);
insert into possible_upper_levels (level_ref, level_upper_ref) values (69, 67);
insert into possible_upper_levels (level_ref, level_upper_ref) values (71, 70);
insert into possible_upper_levels (level_ref, level_upper_ref) values (72, 71);
insert into possible_upper_levels (level_ref, level_upper_ref) values (73, 72);
insert into possible_upper_levels (level_ref, level_upper_ref) values (76, 75);
insert into possible_upper_levels (level_ref, level_upper_ref) values (77, 76);
insert into possible_upper_levels (level_ref, level_upper_ref) values (78, 76);
insert into possible_upper_levels (level_ref, level_upper_ref) values (78, 77);

INSERT INTO classification_keywords (referenced_relation, record_id, keyword_type, keyword, keyword_indexed) (select 'taxonomy', id, 'GenusOrMonomial', 'Virus', 'virus' from taxonomy where name = 'Virus');
INSERT INTO classification_keywords (referenced_relation, record_id, keyword_type, keyword, keyword_indexed) (select 'taxonomy', id, 'GenusOrMonomial', 'Bacteria', 'bacteria' from taxonomy where name = 'Bacteria');
INSERT INTO classification_keywords (referenced_relation, record_id, keyword_type, keyword, keyword_indexed) (select 'taxonomy', id, 'GenusOrMonomial', 'Eucaryota', 'eucaryota' from taxonomy where name = 'Eucaryota');
INSERT INTO classification_keywords (referenced_relation, record_id, keyword_type, keyword, keyword_indexed) (select 'taxonomy', id, 'GenusOrMonomial', 'Archaea', 'archaea' from taxonomy where name = 'Archaea');
